<?php

namespace App\Http\Controllers;

use App\Models\PromoBanner;
use App\Models\PromoBannerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PromoBannerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = PromoBanner::with(['primaryImage', 'creator'])
            ->orderBy('created_at', 'desc');

        // Apply filters if present
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('descriptions', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $banners = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        return view('pages.promo-banners.index', compact('banners', 'perPage'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'          => 'required|string|max:255',
                'descriptions'   => 'nullable|string',
                'promo_code'     => 'nullable|string|max:50',
                'how_to_claim'   => 'nullable|array',
                'how_to_claim.*' => 'nullable|string|max:500',
                'banner_image'   => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ], [
                'banner_image.required' => 'Gambar banner wajib diupload.',
                'banner_image.image'    => 'File harus berupa gambar.',
                'banner_image.mimes'    => 'Format gambar harus jpeg, png, jpg, gif, atau webp.',
                'banner_image.max'      => 'Ukuran gambar maksimal 5MB.',
            ]);

            // Filter out empty how_to_claim items
            $howToClaim = !empty($validated['how_to_claim'])
                ? array_values(array_filter($validated['how_to_claim'], fn($v) => $v !== null && $v !== ''))
                : null;

            // Create promo banner
            $banner = PromoBanner::create([
                'title'        => $validated['title'],
                'descriptions' => $validated['descriptions'],
                'promo_code'   => $validated['promo_code'] ?? null,
                'how_to_claim' => $howToClaim,
                'status'       => 1,
                'created_by'   => Auth::id(),
            ]);

            // Handle image upload
            if ($request->hasFile('banner_image')) {
                $file = $request->file('banner_image');
                $fileName = 'promo_banner_' . $banner->idrec . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('promo_banners', $fileName, 'public');

                $image = PromoBannerImage::create([
                    'promo_banner_id' => $banner->idrec,
                    'image' => $path,
                    'caption' => $validated['title'],
                    'sort_order' => 0,
                ]);

                // Update banner with primary image
                $banner->update(['image_id' => $image->idrec]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Promo banner berhasil ditambahkan',
                'data' => $banner->load('primaryImage')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan promo banner: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan promo banner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $banner = PromoBanner::findOrFail($id);

            $validated = $request->validate([
                'title'          => 'required|string|max:255',
                'descriptions'   => 'nullable|string',
                'promo_code'     => 'nullable|string|max:50',
                'how_to_claim'   => 'nullable|array',
                'how_to_claim.*' => 'nullable|string|max:500',
                'banner_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            // Filter out empty how_to_claim items
            $howToClaim = !empty($validated['how_to_claim'])
                ? array_values(array_filter($validated['how_to_claim'], fn($v) => $v !== null && $v !== ''))
                : null;

            $banner->update([
                'title'        => $validated['title'],
                'descriptions' => $validated['descriptions'],
                'promo_code'   => $validated['promo_code'] ?? null,
                'how_to_claim' => $howToClaim,
                'updated_by'   => Auth::id(),
            ]);

            // Handle image upload
            if ($request->hasFile('banner_image')) {
                $file = $request->file('banner_image');
                $fileName = 'promo_banner_' . $banner->idrec . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('promo_banners', $fileName, 'public');

                // Delete old image if exists
                if ($banner->primaryImage) {
                    Storage::disk('public')->delete($banner->primaryImage->image);
                    $banner->primaryImage->update([
                        'image' => $path,
                        'caption' => $validated['title'],
                    ]);
                } else {
                    $image = PromoBannerImage::create([
                        'promo_banner_id' => $banner->idrec,
                        'image' => $path,
                        'caption' => $validated['title'],
                        'sort_order' => 0,
                    ]);
                    $banner->update(['image_id' => $image->idrec]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Promo banner berhasil diupdate',
                'data' => $banner->fresh()->load('primaryImage')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate promo banner: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate promo banner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = PromoBanner::findOrFail($id);

            // Delete associated images from storage
            foreach ($banner->images as $image) {
                Storage::disk('public')->delete($image->image);
            }

            $banner->delete();

            return response()->json([
                'success' => true,
                'message' => 'Promo banner berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus promo banner: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus promo banner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $banner = PromoBanner::with(['primaryImage', 'creator', 'updater'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Promo banner tidak ditemukan'
            ], 404);
        }
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $query = PromoBanner::with(['primaryImage', 'creator'])
            ->orderBy('created_at', 'desc');

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('descriptions', 'like', "%$search%");
            });
        }

        // Status Filter
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        // Pagination
        $banners = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.promo-banners.partials.banner_table', [
                'banners' => $banners,
                'per_page' => $perPage,
            ])->render(),
            'pagination' => $perPage !== 'all'
                ? $banners->links()->toHtml()
                : ''
        ]);
    }

    public function toggleStatus(Request $request)
    {
        try {
            $banner = PromoBanner::findOrFail($request->id);
            $banner->status = $request->status;
            $banner->updated_by = Auth::id();
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => 'Status promo banner berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status promo banner'
            ], 500);
        }
    }
}
