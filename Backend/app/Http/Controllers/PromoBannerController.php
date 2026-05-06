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
                /* New shape: parallel arrays of titles + descs, zipped server-side into [{title, desc}, ...].
                   Old `how_to_claim` (string array) input is no longer accepted from the form. */
                'how_to_claim_titles'   => 'nullable|array',
                'how_to_claim_titles.*' => 'nullable|string|max:255',
                'how_to_claim_descs'    => 'nullable|array',
                'how_to_claim_descs.*'  => 'nullable|string|max:500',
                'terms_conditions'   => 'nullable|array',
                'terms_conditions.*' => 'nullable|string|max:500',
                'banner_image'   => 'required|image|mimes:jpeg,jpg,gif|max:5120',
                'mobile_banner_image' => 'required|image|mimes:jpeg,jpg,gif|max:5120',
            ], [
                'banner_image.required' => 'Gambar banner wajib diupload.',
                'banner_image.image'    => 'File harus berupa gambar.',
                'banner_image.mimes'    => 'Format gambar harus JPG atau GIF.',
                'banner_image.max'      => 'Ukuran gambar maksimal 5MB.',
                'mobile_banner_image.required' => 'Gambar banner mobile wajib diupload.',
                'mobile_banner_image.image' => 'File harus berupa gambar.',
                'mobile_banner_image.mimes' => 'Format gambar harus JPG atau GIF.',
                'mobile_banner_image.max'   => 'Ukuran gambar maksimal 5MB.',
            ]);

            /* Zip parallel title/desc arrays into [{title, desc}, ...] objects. A row counts only
               when BOTH title AND desc are non-empty trimmed strings — anything else is dropped. */
            $howToClaim = $this->zipHowToClaim(
                $validated['how_to_claim_titles'] ?? [],
                $validated['how_to_claim_descs'] ?? []
            );

            /* Same trim+filter pattern for terms_conditions (still a flat string array) */
            $termsConditions = !empty($validated['terms_conditions'])
                ? array_values(array_filter($validated['terms_conditions'], fn($v) => $v !== null && trim($v) !== ''))
                : [];

            /* Business-rule validation on the zipped/filtered arrays. */
            $businessErrors = [];
            if (count($howToClaim) < 2) {
                $businessErrors['how_to_claim_titles'] = [__('ui.promo_banner_how_to_claim_min_error')];
            }
            if (count($termsConditions) < 1) {
                $businessErrors['terms_conditions'] = [__('ui.promo_banner_terms_conditions_min_error')];
            }
            if (!empty($businessErrors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($businessErrors);
            }

            // Create promo banner
            $banner = PromoBanner::create([
                'title'            => $validated['title'],
                'descriptions'     => $validated['descriptions'],
                'promo_code'       => $validated['promo_code'] ?? null,
                'how_to_claim'     => $howToClaim,
                'terms_conditions' => $termsConditions,
                'status'           => 1,
                'created_by'       => Auth::id(),
            ]);

            // Handle frontend banner image upload
            if ($request->hasFile('banner_image')) {
                $file = $request->file('banner_image');
                $fileName = 'promo_banner_' . $banner->idrec . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('promo_banners', $fileName, 'public');

                // Handle mobile banner image upload (optional, falls back to main image)
                $mobilePath = null;
                if ($request->hasFile('mobile_banner_image')) {
                    $mobileFile = $request->file('mobile_banner_image');
                    $mobileFileName = 'promo_banner_mobile_' . $banner->idrec . '_' . time() . '.' . $mobileFile->getClientOriginalExtension();
                    $mobilePath = $mobileFile->storeAs('promo_banners', $mobileFileName, 'public');
                }

                $image = PromoBannerImage::create([
                    'promo_banner_id' => $banner->idrec,
                    'image' => $path,
                    'mobile_image' => $mobilePath,
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
                'how_to_claim_titles'   => 'nullable|array',
                'how_to_claim_titles.*' => 'nullable|string|max:255',
                'how_to_claim_descs'    => 'nullable|array',
                'how_to_claim_descs.*'  => 'nullable|string|max:500',
                'terms_conditions'   => 'nullable|array',
                'terms_conditions.*' => 'nullable|string|max:500',
                'banner_image'   => 'nullable|image|mimes:jpeg,jpg,gif|max:5120',
                'mobile_banner_image' => 'nullable|image|mimes:jpeg,jpg,gif|max:5120',
            ]);

            /* Zip parallel title/desc arrays into [{title, desc}, ...] (mirrors store()) */
            $howToClaim = $this->zipHowToClaim(
                $validated['how_to_claim_titles'] ?? [],
                $validated['how_to_claim_descs'] ?? []
            );

            $termsConditions = !empty($validated['terms_conditions'])
                ? array_values(array_filter($validated['terms_conditions'], fn($v) => $v !== null && trim($v) !== ''))
                : [];

            $businessErrors = [];
            if (count($howToClaim) < 2) {
                $businessErrors['how_to_claim_titles'] = [__('ui.promo_banner_how_to_claim_min_error')];
            }
            if (count($termsConditions) < 1) {
                $businessErrors['terms_conditions'] = [__('ui.promo_banner_terms_conditions_min_error')];
            }
            if (!empty($businessErrors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($businessErrors);
            }

            $banner->update([
                'title'            => $validated['title'],
                'descriptions'     => $validated['descriptions'],
                'promo_code'       => $validated['promo_code'] ?? null,
                'how_to_claim'     => $howToClaim,
                'terms_conditions' => $termsConditions,
                'updated_by'       => Auth::id(),
            ]);

            // Handle frontend banner image upload
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

            // Handle mobile banner image upload
            if ($request->hasFile('mobile_banner_image') && $banner->primaryImage) {
                $mobileFile = $request->file('mobile_banner_image');
                $mobileFileName = 'promo_banner_mobile_' . $banner->idrec . '_' . time() . '.' . $mobileFile->getClientOriginalExtension();
                // Delete old mobile image if exists
                if ($banner->primaryImage->mobile_image) {
                    Storage::disk('public')->delete($banner->primaryImage->mobile_image);
                }
                $mobilePath = $mobileFile->storeAs('promo_banners', $mobileFileName, 'public');
                $banner->primaryImage->update(['mobile_image' => $mobilePath]);
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

            /* Normalize how_to_claim to canonical [{title, desc}] shape for the frontend.
               Legacy banners stored as ["string", ...] get auto-titled "Langkah N". */
            $bannerData = $banner->toArray();
            $bannerData['how_to_claim'] = self::normalizeHowToClaim($banner->how_to_claim);

            return response()->json([
                'success' => true,
                'data' => $bannerData
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

    /**
     * Zip parallel `how_to_claim_titles[]` + `how_to_claim_descs[]` arrays from the request
     * into a list of `{title, desc}` objects. Drops any pair where either field is empty after trim.
     */
    private function zipHowToClaim(array $titles, array $descs): array
    {
        $out = [];
        $count = max(count($titles), count($descs));
        for ($i = 0; $i < $count; $i++) {
            $title = isset($titles[$i]) ? trim((string) $titles[$i]) : '';
            $desc = isset($descs[$i]) ? trim((string) $descs[$i]) : '';
            if ($title !== '' && $desc !== '') {
                $out[] = ['title' => $title, 'desc' => $desc];
            }
        }
        return $out;
    }

    /**
     * Normalize a stored `how_to_claim` value to the canonical `[{title, desc}, ...]` shape so
     * frontend / view modal / edit modal all see the same structure regardless of when the banner
     * was last saved. Legacy banners stored as `["string", ...]` get auto-titled "Langkah N".
     *
     * Returns `[]` for null/empty values so callers can rely on Array.isArray() checks.
     */
    public static function normalizeHowToClaim($raw): array
    {
        if (empty($raw) || !is_array($raw)) {
            return [];
        }
        $out = [];
        foreach (array_values($raw) as $i => $step) {
            if (is_array($step) && (isset($step['title']) || isset($step['desc']))) {
                $out[] = [
                    'title' => isset($step['title']) ? (string) $step['title'] : '',
                    'desc' => isset($step['desc']) ? (string) $step['desc'] : '',
                ];
            } elseif (is_string($step) && trim($step) !== '') {
                /* Legacy shape — promote to {title, desc} with generic numbered title */
                $out[] = [
                    'title' => 'Langkah ' . ($i + 1),
                    'desc' => $step,
                ];
            }
        }
        return $out;
    }
}
