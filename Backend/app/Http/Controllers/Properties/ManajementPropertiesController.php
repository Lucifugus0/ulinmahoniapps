<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyFacility;
use Illuminate\Support\Facades\Storage;

class ManajementPropertiesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $statusFilter = $request->input('status', '1'); // Default menampilkan hanya yang aktif

        $query = Property::with(['creator', 'images', 'thumbnail'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan property_id user (kecuali super admin dan HO role)
        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            // User hanya bisa melihat property mereka sendiri
            $query->where('idrec', $accessiblePropertyId);
        }

        $generalFacilities = PropertyFacility::where('status', 1)->byCategory('general')->get();
        $securityFacilities = PropertyFacility::where('status', 1)->byCategory('security')->get();
        $amenitiesFacilities = PropertyFacility::where('status', 1)->byCategory('amenities')->get();

        $facilities = PropertyFacility::where('status', 1)
            ->get()
            ->groupBy('category');

        // Apply filters if present
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('province', 'like', "%{$request->search}%")
                    ->orWhere('city', 'like', "%{$request->search}%");
            });
        }

        // Filter status dengan default aktif
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $properties = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        // Process properties to include full image URLs
        $properties->transform(function ($property) {
            // Add full image URLs to each property
            $property->image_urls = $property->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                ];
            });

            // Add featured image URL
            $property->featured_image_url = $property->images->first() && $property->images->first()->image
                ? asset('storage/' . $property->images->first()->image)
                : null;

            return $property;
        });

        return view('pages.Properties.m-Properties.index', [
            'generalFacilities' => $generalFacilities,
            'securityFacilities' => $securityFacilities,
            'amenitiesFacilities' => $amenitiesFacilities,
            'facilities' => $facilities,
            'properties' => $properties,
            'per_page' => $perPage,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $search = $request->input('search');
        $status = $request->input('status', '1'); // Default aktif

        $query = Property::with(['creator', 'images', 'thumbnail'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan property_id user (kecuali super admin dan HO role)
        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            // User hanya bisa melihat property mereka sendiri
            $query->where('idrec', $accessiblePropertyId);
        }

        // Get all facilities data
        $generalFacilities = PropertyFacility::where('status', 1)->byCategory('general')->get();
        $securityFacilities = PropertyFacility::where('status', 1)->byCategory('security')->get();
        $amenitiesFacilities = PropertyFacility::where('status', 1)->byCategory('amenities')->get();

        // Get facilities grouped by category for view modal
        $facilities = PropertyFacility::where('status', 1)
            ->get()
            ->groupBy('category');

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('province', 'like', "%$search%")
                    ->orWhere('city', 'like', "%$search%");
            });
        }

        // Status Filter dengan default aktif
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Pagination
        $properties = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.Properties.m-Properties.partials.property_table', [
                'properties' => $properties,
                'per_page' => $perPage,
                'generalFacilities' => $generalFacilities,
                'securityFacilities' => $securityFacilities,
                'amenitiesFacilities' => $amenitiesFacilities,
                'facilities' => $facilities, // Tambahkan ini
            ])->render(),
            'pagination' => $perPage !== 'all'
                ? $properties->links()->toHtml()
                : ''
        ]);
    }

    public function toggleStatus(Request $request)
    {
        try {
            $property = Property::find($request->id);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            $property->status = $request->status;
            $property->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function updateStatus(Property $property, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);

        $property->update([
            'status' => $request->status,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Set all properties to active status
     */
    public function setActiveAll(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Property::query();

            // Filter berdasarkan akses user
            $accessiblePropertyId = $user->getAccessiblePropertyId();
            if ($accessiblePropertyId !== null) {
                $query->where('idrec', $accessiblePropertyId);
            }

            $updatedCount = $query->where('status', 0)->update([
                'status' => 1,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $updatedCount . ' properti berhasil diaktifkan',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengaktifkan properti: ' . $e->getMessage()
            ], 500);
        }
    }


    protected function generateInitials($name)
    {
        $words = preg_split('/\s+/', $name);
        $initials = '';

        foreach ($words as $word) {
            // Ambil huruf pertama jika kapital
            if (ctype_upper(substr($word, 0, 1))) {
                $initials .= substr($word, 0, 1);
            }
        }

        // Jika tidak ada huruf kapital, ambil semua huruf pertama
        if (empty($initials)) {
            foreach ($words as $word) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        return $initials;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_name' => 'required',
            'initial' => 'required|string|max:3',
            'property_type' => 'required',
            'gender' => 'nullable|string|in:male,female,mixed',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'village' => 'required|string',
            'postal_code' => 'nullable|string',
            'full_address' => 'required|string',
            'description' => 'nullable|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'property_images' => 'required|array|min:3|max:10', // Minimal 3 foto
            'property_images.*' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'facilities' => 'nullable|array',
            'thumbnail_index' => 'required|integer',
            'general_facilities' => 'nullable|array',
            'security_facilities' => 'nullable|array',
            'amenities_facilities' => 'nullable|array',
            'nearby_locations' => 'nullable|string',
        ]);

        // Ambil hanya fasilitas yang valid
        $facilitiesData = [
            'general' => array_reverse($request->input('general_facilities', [])),
            'security' => array_reverse($request->input('security_facilities', [])),
            'amenities' => array_reverse($request->input('amenities_facilities', [])),
        ];


        // Buat ID dan slug unik
        $idrec = Property::max('idrec') + 1;
        $tagShort = strtolower(substr($request->property_type, 0, 3));
        $nameShort = strtolower(collect(explode(' ', $request->property_name))->map(fn($w) => substr($w, 0, 1))->implode(''));
        $slug = $tagShort . '_' . $nameShort . '_' . $idrec;

        // Gunakan initial dari input user (uppercase)
        $initials = strtoupper($request->initial);

        $imagePaths = [];
        $thumbnailIndex = $request->thumbnail_index;

        foreach ($request->file('property_images') as $index => $file) {
            if (!$file->isValid()) {
                return back()->withErrors(['property_images' => 'Invalid file uploaded.']);
            }

            // Generate nama file unik
            $fileName = 'property_' . $idrec . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();

            // Simpan file ke storage/app/public/property_images
            $path = $file->storeAs('property_images', $fileName, 'public');

            // Simpan path untuk digunakan di database
            $imagePaths[] = $path;
        }

        // Simpan ke tabel `properties`
        $property = new Property();
        $property->idrec = $idrec;
        $property->slug = $slug;
        $property->tags = $request->property_type;
        $property->gender = ($request->property_type === 'Kos' && $request->gender) ? $request->gender : null;
        $property->name = $request->property_name;
        $property->initial = $initials;
        $property->province = $request->province;
        $property->city = $request->city;
        $property->subdistrict = $request->district;
        $property->village = $request->village;
        $property->postal_code = $request->postal_code;
        $property->address = $request->full_address;
        $property->description = $request->description;
        $property->location = $request->latitude . ',' . $request->longitude;
        $property->latitude = $request->latitude;
        $property->longitude = $request->longitude;
        $property->general = $facilitiesData['general'];
        $property->security = $facilitiesData['security'];
        $property->amenities = $facilitiesData['amenities'];
        $property->nearby_locations = $request->nearby_locations ? json_decode($request->nearby_locations, true) : [];
        $property->status = '1';
        $property->created_by = Auth::id();
        $property->save();

        // Simpan ke tabel m_property_images
        foreach ($imagePaths as $index => $imagePath) {
            $image = new PropertyImage();
            $image->property_id = $idrec;
            $image->image = $imagePath; // path ke file gambar di storage
            $image->caption = $request->file('property_images')[$index]->getClientOriginalName() ?? 'No caption';
            $image->thumbnail = ($index == $thumbnailIndex) ? 1 : 0;
            $image->created_by = Auth::user()->id;
            $image->created_at = now();
            $image->save();
        }

        return $request->wantsJson()
            ? response()->json(['status' => 'success', 'message' => 'Properti berhasil dibuat!', 'data' => $property], 201)
            : redirect()->route('properties.index')->with('success', 'Properti berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'initial' => 'required|string|max:3',
                'tags' => 'required|string',
                'gender' => 'nullable|string|in:male,female,mixed',
                'description' => 'required|string',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'province' => 'required|string',
                'city' => 'required|string',
                'subdistrict' => 'required|string',
                'village' => 'required|string',
                'postal_code' => 'nullable|string',
                'general' => 'nullable|array',
                'general.*' => 'integer',
                'security' => 'nullable|array',
                'security.*' => 'integer',
                'amenities' => 'nullable|array',
                'amenities.*' => 'integer',
                'property_images' => 'nullable|array',
                'property_images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'integer',
                'thumbnail_index' => 'required|integer|min:0',
                'existing_images' => 'nullable|array',
                'existing_images.*' => 'integer',
                'nearby_locations' => 'nullable|string',
            ]);

            $property = Property::findOrFail($id);

            // 1. Handle deletion of images marked for deletion
            $imagesToDelete = $request->input('delete_images', []);
            if (!empty($imagesToDelete)) {
                $imagesToDeleteRecords = PropertyImage::whereIn('idrec', $imagesToDelete)
                    ->where('property_id', $property->idrec)
                    ->get();

                foreach ($imagesToDeleteRecords as $image) {
                    if (Storage::disk('public')->exists($image->image)) {
                        Storage::disk('public')->delete($image->image);
                    }
                }

                PropertyImage::whereIn('idrec', $imagesToDelete)
                    ->where('property_id', $property->idrec)
                    ->delete();
            }

            // 2. Get all remaining existing images (after deletion)
            $existingImageIds = $request->input('existing_images', []);
            $existingImages = PropertyImage::whereIn('idrec', $existingImageIds)
                ->where('property_id', $property->idrec)
                ->orderBy('idrec')
                ->get();

            // 3. Reset all thumbnails first
            PropertyImage::where('property_id', $property->idrec)
                ->update(['thumbnail' => false]);

            // 4. Handle new image uploads
            $newImages = [];
            if ($request->hasFile('property_images')) {
                foreach ($request->file('property_images') as $index => $file) {
                    if (!$file->isValid()) continue;

                    $filename = 'property_' . $property->idrec . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('property_images', $filename, 'public');
                    $caption = $file->getClientOriginalName();

                    $newImage = PropertyImage::create([
                        'property_id' => $property->idrec,
                        'image' => $filePath,
                        'thumbnail' => false,
                        'caption' => $caption,
                        'created_by' => Auth::id(),
                        'created_at' => now()
                    ]);

                    $newImages[] = $newImage;
                }
            }

            // 5. Combine all images (existing + new)
            $allImages = collect($existingImages)->merge($newImages)->values();

            // 6. Set thumbnail based on thumbnail_index
            $thumbnailIndex = $request->input('thumbnail_index');
            if ($allImages->count() > 0 && isset($allImages[$thumbnailIndex])) {
                $allImages[$thumbnailIndex]->update(['thumbnail' => true]);
            }

            // 7. Update property data
            $property->update([
                'name' => $request->input('name'),
                'initial' => strtoupper($request->input('initial')),
                'tags' => $request->input('tags'),
                'gender' => ($request->input('tags') === 'Kos' && $request->input('gender')) ? $request->input('gender') : null,
                'description' => $request->input('description'),
                'address' => $request->input('address'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'location' => $request->input('latitude') . ',' . $request->input('longitude'),
                'province' => $request->input('province'),
                'city' => $request->input('city'),
                'subdistrict' => $request->input('subdistrict'),
                'village' => $request->input('village'),
                'postal_code' => $request->input('postal_code'),
                'general' => $request->input('general', []),
                'security' => $request->input('security', []),
                'amenities' => $request->input('amenities', []),
                'nearby_locations' => $request->nearby_locations ? json_decode($request->nearby_locations, true) : [],
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Properti berhasil diperbarui',
                'redirect' => route('properties.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function tablePartial(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = Property::with(['creator', 'images'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan property_id user (kecuali super admin dan HO role)
        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            // User hanya bisa melihat property mereka sendiri
            $query->where('idrec', $accessiblePropertyId);
        }

        $properties = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        return view('pages.Properties.m-Properties.partials.property_table', [
            'properties' => $properties,
            'per_page' => $perPage,
        ]);
    }

    // ```````````````````m_facility`````````````````````````````````
    public function indexFacility(Request $request)
    {
        $query = PropertyFacility::with(['createdBy', 'updatedBy'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('facility', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($q) use ($request) {
                // Convert status string to integer
                $status = $request->status === 'active' ? 1 : 0;
                $q->where('status', $status);
            })
            ->when($request->category, function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->orderBy('category', 'asc')
            ->orderBy('created_at', 'desc');

        $perPage = $request->per_page ?? 8;
        $facilities = $perPage === 'all'
            ? $query->get()
            : $query->paginate($perPage)->withQueryString();

        $categories = PropertyFacility::distinct()->pluck('category');

        // Jika request AJAX, kembalikan bagian table dan pagination
        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            $tableHtml = view('pages.Properties.Facility_properties.partials.facility-property_table', compact('facilities', 'categories'))->render();
            $paginationHtml = $facilities instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $facilities->appends($request->input())->links()->toHtml()
                : '';

            return '<div id="tableContainer">' . $tableHtml . '</div>'
                 . '<div id="paginationContainer">' . $paginationHtml . '</div>';
        }

        return view('pages.Properties.Facility_properties.index', compact('facilities', 'categories'));
    }

    public function storeFacility(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'facility' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:general,security,amenities',
            'status' => 'required|boolean', // Changed to boolean
        ]);

        try {
            // Create a new facility record
            $facility = PropertyFacility::create([
                'facility' => $validatedData['facility'],
                'icon' => $validatedData['icon'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'category' => $validatedData['category'],
                'status' => $validatedData['status'] ? 1 : 0, // Convert to 1/0
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facility created successfully',
                'data' => $facility,
                'redirect_url' => route('facilityProperty.index')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateFacility(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'facility' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:general,security,amenities',
            'status' => 'required|boolean'
        ]);

        try {
            // Ambil data facility berdasarkan ID
            $facility = PropertyFacility::find($id);

            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facility not found'
                ], 404);
            }

            // Update data facility
            $facility->update([
                'facility' => $validatedData['facility'],
                'icon' => $validatedData['icon'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'category' => $validatedData['category'],
                'status' => $validatedData['status'] ? 1 : 0,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facility updated successfully',
                'data' => $facility,
                'redirect_url' => route('facilityProperty.index')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleFacilityStatus(Request $request)
    {
        try {
            $facility = PropertyFacility::find($request->id);

            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facility not found'
                ], 404);
            }

            $facility->update([
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
        }
    }
}
