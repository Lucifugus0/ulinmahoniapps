<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\PromoBannerService;
use Illuminate\Http\Request;

class PromoBannerController extends ApiController
{
    protected $promoBannerService;

    public function __construct(PromoBannerService $promoBannerService)
    {
        $this->promoBannerService = $promoBannerService;
    }

    /**
     * Get all promo banners with optional filters
     * Use ?id= to get a single banner by ID
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // If id parameter is provided, return single banner
            if ($request->has('id')) {
                $banner = $this->promoBannerService->getBannerById($request->id);

                if (!$banner) {
                    return $this->respondNotFound('Promo banner not found');
                }

                return $this->respond([
                    'data' => $banner
                ]);
            }

            $filters = [];

            if ($request->has('idrec')) {
                $filters['idrec'] = $request->idrec;
            }

            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            $groupedBanners = $this->promoBannerService->getAllBanners($filters);

            // Handle pagination if requested
            if ($request->has('limit') && $request->has('page')) {
                $page = $request->page;
                $perPage = $request->limit;
                $offset = ($page - 1) * $perPage;

                $paginatedItems = $groupedBanners->slice($offset, $perPage)->values();

                $response = new \Illuminate\Pagination\LengthAwarePaginator(
                    $paginatedItems,
                    $groupedBanners->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                return $this->respondWithPagination($response, [
                    'data' => $response->items()
                ]);
            }

            return $this->respond([
                'data' => $groupedBanners
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    /**
     * Create a new promo banner
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'descriptions' => 'nullable|string',
                'status' => 'nullable|in:0,1',
                'created_by' => 'required|integer',
                'images' => 'nullable|array',
                'images.*.image' => 'required|string',
                'images.*.thumbnail' => 'nullable|string',
                'images.*.caption' => 'nullable|string|max:255',
                'images.*.sort_order' => 'nullable|integer'
            ]);

            // Default status to 1 (active) if not provided
            $validated['status'] = $validated['status'] ?? 1;

            $promoBanner = $this->promoBannerService->createBanner($validated);

            // Add images if provided
            $images = null;
            if (!empty($validated['images'])) {
                $result = $this->promoBannerService->addImages($promoBanner->idrec, $validated['images']);
                $images = $result['data'] ?? null;
            }

            return response()->json([
                'message' => 'Promo banner created successfully',
                'created_id' => $promoBanner->idrec,
                'images' => $images
            ], 201);
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }

    /**
     * Update an existing promo banner
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'image_id' => 'sometimes|nullable|integer',
                'descriptions' => 'sometimes|nullable|string',
                'status' => 'sometimes|in:0,1',
                'updated_by' => 'required|integer'
            ]);

            $promoBanner = $this->promoBannerService->updateBanner($id, $validated);

            if (!$promoBanner) {
                return $this->respondNotFound('Promo banner not found');
            }

            return $this->respond([
                'message' => 'Promo banner updated successfully',
                'data' => $promoBanner
            ]);
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }

    /**
     * Delete a promo banner
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->promoBannerService->deleteBanner($id);

            if (!$deleted) {
                return $this->respondNotFound('Promo banner not found');
            }

            return $this->respondDeleted('Promo banner deleted successfully');
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    /**
     * Add images to a promo banner
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addImages(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'images' => 'required|array',
                'images.*.image' => 'required|string',
                'images.*.thumbnail' => 'nullable|string',
                'images.*.caption' => 'nullable|string|max:255',
                'images.*.sort_order' => 'nullable|integer'
            ]);

            $result = $this->promoBannerService->addImages($id, $validated['images']);

            if (!$result['success']) {
                return $this->respondBadRequest($result['message']);
            }

            return response()->json([
                'message' => $result['message'],
                'data' => $result['data']
            ], 201);
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }

    /**
     * Remove an image from a promo banner
     *
     * @param int $id
     * @param int $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeImage($id, $imageId)
    {
        try {
            $result = $this->promoBannerService->removeImage($id, $imageId);

            if (!$result['success']) {
                return $this->respondNotFound($result['message']);
            }

            return $this->respondDeleted($result['message']);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
}
