<?php

namespace App\Services;

use App\Models\PromoBanner;
use App\Models\PromoBannerImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromoBannerService
{
    /**
     * Get all promo banners with images
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getAllBanners(array $filters = [])
    {
        $query = PromoBanner::query();

        // Join with promo banner images
        $query->leftJoin('m_promo_banner_images', 'm_promo_banner_images.promo_banner_id', '=', 'm_promo_banners.idrec')
            ->select([
                'm_promo_banners.*',
                'm_promo_banner_images.idrec as img_id',
                'm_promo_banner_images.image as image_data',
                'm_promo_banner_images.thumbnail as thumbnail_data',
                'm_promo_banner_images.caption',
                'm_promo_banner_images.sort_order',
            ]);

        // Add filters if provided
        if (isset($filters['idrec'])) {
            $query->where('m_promo_banners.idrec', $filters['idrec']);
        }

        if (isset($filters['status'])) {
            $query->where('m_promo_banners.status', $filters['status']);
        }

        // Order by created_at (newest first)
        $query->orderBy('m_promo_banners.created_at', 'desc');

        // Get all promo banners with their images
        $promoBanners = $query->get();

        // Group images by promo banner
        return $this->groupBannersWithImages($promoBanners);
    }

    /**
     * Get a single promo banner by ID with images
     *
     * @param int $id
     * @return array|null
     */
    public function getBannerById(int $id)
    {
        $banner = PromoBanner::leftJoin('m_promo_banner_images', 'm_promo_banner_images.promo_banner_id', '=', 'm_promo_banners.idrec')
            ->where('m_promo_banners.idrec', $id)
            ->select([
                'm_promo_banners.*',
                'm_promo_banner_images.idrec as img_id',
                'm_promo_banner_images.image as image_data',
                'm_promo_banner_images.thumbnail as thumbnail_data',
                'm_promo_banner_images.caption',
                'm_promo_banner_images.sort_order',
            ])
            ->get();

        if ($banner->isEmpty()) {
            return null;
        }

        return $this->groupBannersWithImages($banner)->first();
    }

    /**
     * Create a new promo banner
     *
     * @param array $data
     * @return PromoBanner
     */
    public function createBanner(array $data)
    {
        return PromoBanner::create([
            'title' => $data['title'],
            'descriptions' => $data['descriptions'] ?? null,
            'status' => $data['status'],
            'created_by' => $data['created_by']
        ]);
    }

    /**
     * Update an existing promo banner
     *
     * @param int $id
     * @param array $data
     * @return PromoBanner|null
     */
    public function updateBanner(int $id, array $data)
    {
        $promoBanner = PromoBanner::find($id);

        if (!$promoBanner) {
            return null;
        }

        $promoBanner->update($data);

        return $promoBanner;
    }

    /**
     * Delete a promo banner
     *
     * @param int $id
     * @return bool
     */
    public function deleteBanner(int $id)
    {
        $promoBanner = PromoBanner::find($id);

        if (!$promoBanner) {
            return false;
        }

        $promoBanner->delete();

        return true;
    }

    /**
     * Add images to a promo banner
     *
     * @param int $bannerId
     * @param array $images
     * @return array
     */
    public function addImages(int $bannerId, array $images)
    {
        $promoBanner = PromoBanner::find($bannerId);

        if (!$promoBanner) {
            return ['success' => false, 'message' => 'Promo banner not found', 'data' => null];
        }

        try {
            DB::beginTransaction();

            $createdImages = [];
            foreach ($images as $index => $imageData) {
                // Process base64 image if provided
                $imagePath = $this->processBase64Image($imageData['image'], 'promo-banners');
                $thumbnailPath = isset($imageData['thumbnail'])
                    ? $this->processBase64Image($imageData['thumbnail'], 'promo-banners/thumbnails')
                    : null;

                $image = PromoBannerImage::create([
                    'promo_banner_id' => $promoBanner->idrec,
                    'image' => $imagePath,
                    'thumbnail' => $thumbnailPath,
                    'caption' => $imageData['caption'] ?? null,
                    'sort_order' => $imageData['sort_order'] ?? $index
                ]);
                $createdImages[] = $image;
            }

            // Set first image as primary if no primary image is set
            if (!$promoBanner->image_id && count($createdImages) > 0) {
                $promoBanner->update(['image_id' => $createdImages[0]->idrec]);
            }

            DB::commit();

            return ['success' => true, 'message' => 'Images added successfully', 'data' => $createdImages];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding images to promo banner: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    /**
     * Process base64 image and save to storage
     *
     * @param string $imageData Base64 encoded image or file path
     * @param string $folder Storage folder
     * @return string File path
     */
    private function processBase64Image(string $imageData, string $folder = 'promo-banners')
    {
        // Check if it's a base64 encoded image
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            $extension = $matches[1];
            $base64Data = substr($imageData, strpos($imageData, ',') + 1);
            $decodedImage = base64_decode($base64Data);

            // Generate unique filename
            $filename = Str::uuid() . '.' . $extension;
            $path = $folder . '/' . $filename;

            // Save to storage
            Storage::disk('public')->put($path, $decodedImage);

            return $path;
        }

        // If not base64, return as-is (assume it's already a file path)
        return $imageData;
    }

    /**
     * Remove an image from a promo banner
     *
     * @param int $bannerId
     * @param int $imageId
     * @return array
     */
    public function removeImage(int $bannerId, int $imageId)
    {
        $promoBanner = PromoBanner::find($bannerId);

        if (!$promoBanner) {
            return ['success' => false, 'message' => 'Promo banner not found'];
        }

        $image = PromoBannerImage::where('idrec', $imageId)
            ->where('promo_banner_id', $bannerId)
            ->first();

        if (!$image) {
            return ['success' => false, 'message' => 'Image not found'];
        }

        try {
            // If this was the primary image, clear the reference
            if ($promoBanner->image_id == $imageId) {
                $promoBanner->update(['image_id' => null]);
            }

            $image->delete();

            return ['success' => true, 'message' => 'Image removed successfully'];

        } catch (\Exception $e) {
            Log::error('Error removing image from promo banner: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Group banners with their images
     *
     * @param \Illuminate\Support\Collection $banners
     * @return \Illuminate\Support\Collection
     */
    private function groupBannersWithImages($banners)
    {
        return $banners->groupBy('idrec')->map(function ($bannerGroup) {
            $banner = $bannerGroup->first();

            // Map and sort images by sort_order
            $images = $bannerGroup->filter(fn($item) => $item->img_id !== null)
                ->map(fn($imageItem) => [
                    'id' => $imageItem->img_id,
                    'image' => env('ADMIN_URL') . '/storage/' . $imageItem->image_data,
                    'thumbnail' => $imageItem->thumbnail_data ? env('ADMIN_URL') . '/storage/' . $imageItem->thumbnail_data : null,
                    'caption' => $imageItem->caption,
                    'sort_order' => $imageItem->sort_order,
                ])
                ->sortBy('sort_order')
                ->values();

            $bannerArray = $banner->toArray();

            // Add thumbnail field (first image thumbnail or first image)
            $firstImage = $images->first();
            $bannerArray['thumbnail'] = $firstImage['thumbnail'] ?? $firstImage['image'] ?? null;

            $bannerArray['images'] = $images;

            // Remove image-related fields from the main object
            unset(
                $bannerArray['img_id'],
                $bannerArray['image_data'],
                $bannerArray['thumbnail_data'],
                $bannerArray['caption'],
                $bannerArray['sort_order']
            );

            return $bannerArray;
        })->values();
    }
}
