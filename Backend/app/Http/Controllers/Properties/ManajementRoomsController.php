<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomPrices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\MRoomImage;
use App\Models\RoomFacility;
use App\Services\RoomPriceGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ManajementRoomsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 8);
        $statusFilter = $request->input('status', '1'); // Default menampilkan hanya yang aktif

        $query = Room::where('status', '!=', '2')
            ->with(['property', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filter by property based on user_type
        // user_type = 0 (HO): can see all rooms
        // user_type = 1 (Site): can only see rooms from their property_id
        if ($user->user_type == 1) {
            // Site user: must filter by their property_id
            if ($user->property_id) {
                $query->where('property_id', $user->property_id);
            } else {
                // Site user tanpa property_id tidak bisa melihat room apapun
                $query->whereRaw('1 = 0');
            }
        } else {
            // HO user (user_type = 0): can see all, but can filter by property if requested
            if ($request->has('property_id') && $request->property_id != '') {
                $query->where('property_id', $request->property_id);
            }
        }

        // Filter by status dengan default aktif
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('no', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('property', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $rooms = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        // Filter properties based on user_type
        // user_type = 0 (HO): can see all properties
        // user_type = 1 (Site): can only see their assigned property
        if ($user->user_type == 1) {
            // Site user: only show their assigned property
            $properties = Property::where('idrec', $user->property_id)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            // HO user (user_type = 0): can see all properties
            $properties = Property::orderBy('name', 'asc')->get();
        }

        // Pastikan ini mengembalikan Collection, bukan string
        $facilities = RoomFacility::where('status', 1)->get();

        $facilityData = $facilities->map(function ($facility) {
            return [
                'id' => $facility->idrec,
                'name' => $facility->facility,
                'description' => $facility->description,
                'icon' => $facility->icon,
            ];
        });

        // Jika request AJAX, kembalikan partial view
        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.Properties.m-Rooms.partials.room_table', [
                    'properties' => $properties,
                    'rooms' => $rooms,
                    'per_page' => $perPage,
                    'facilities' => $facilities,
                    'facilityData' => $facilityData,
                ])->render(),
                'pagination' => $rooms instanceof \Illuminate\Pagination\LengthAwarePaginator
                    ? $rooms->appends($request->input())->links()->toHtml()
                    : ''
            ]);
        }

        return view('pages.Properties.m-Rooms.index', [
            'facilities' => $facilities,
            'facilityData' => $facilityData,
            'rooms' => $rooms,
            'properties' => $properties,
            'per_page' => $perPage,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // **DEBUG: Log semua input yang diterima**
            \Log::info('Store Room Request Data:', [
                'all_request' => $request->all(),
                'files_count' => $request->hasFile('room_images') ? count($request->file('room_images')) : 0,
                'price_type' => $request->input('price_type'),
                'daily_price' => $request->input('daily_price'),
                'monthly_price' => $request->input('monthly_price'),
                'general_facilities' => $request->input('general_facilities', []),
            ]);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|numeric|exists:m_properties,idrec',
                'room_no' => 'required|string|max:255',
                'room_name' => 'required|string|max:255',
                'room_size' => 'required|numeric|min:0',
                'room_bed' => 'required|string|in:Single,Double,King,Queen,Twin',
                'room_capacity' => 'required|numeric|min:1',
                'description_id' => 'required|string',
                /* Multi-Tier Pricing: accept 'annual' price_type + weekday/weekend prices for daily */
                'price_type' => 'required|string|in:daily,monthly,annual',
                'daily_price' => 'required_if:price_type,daily|nullable|numeric|min:0',
                'monthly_price' => 'required_if:price_type,monthly|nullable|numeric|min:0',
                'annual_price' => 'required_if:price_type,annual|nullable|numeric|min:0',
                'weekday_price' => 'nullable|numeric|min:0',
                'weekend_price' => 'nullable|numeric|min:0',
                'general_facilities' => 'nullable|array',
                'general_facilities.*' => 'numeric', // Ubah dari string ke numeric karena value adalah idrec
                'room_images' => 'required|array|min:3|max:5',
                'room_images.*' => [
                    'image',
                    'mimes:jpeg,png,jpg,gif,webp',
                    'max:5120',
                    function ($attribute, $value, $fail) {
                        $extension = strtolower($value->getClientOriginalExtension());
                        $mimeType = strtolower($value->getMimeType());

                        $heicExtensions = ['heic', 'heif'];
                        $heicMimeTypes = ['image/heic', 'image/heif'];

                        if (in_array($extension, $heicExtensions) || in_array($mimeType, $heicMimeTypes)) {
                            $fail('Format HEIC/HEIF tidak didukung. Silakan gunakan JPEG, PNG, atau format gambar lainnya.');
                        }
                    }
                ],
                'thumbnail_index' => 'required|integer|min:0',
            ], [
                'property_id.required' => 'Properti harus dipilih',
                'room_no.required' => 'Nomor kamar harus diisi',
                'room_name.required' => 'Nama kamar harus diisi',
                'room_size.required' => 'Ukuran kamar harus diisi',
                'room_bed.required' => 'Jenis tempat tidur harus dipilih',
                'room_capacity.required' => 'Kapasitas kamar harus diisi',
                'description_id.required' => 'Deskripsi kamar harus diisi',
                'price_type.required' => 'Jenis harga harus dipilih',
                'daily_price.required_if' => 'Harga harian harus diisi ketika memilih harga harian',
                'monthly_price.required_if' => 'Harga bulanan harus diisi ketika memilih harga bulanan',
                'room_images.required' => 'Foto kamar harus diupload',
                'room_images.min' => 'Minimal :min foto harus diupload',
                'room_images.max' => 'Maksimal :max foto yang dapat diupload',
                'room_images.*.image' => 'File harus berupa gambar',
                'room_images.*.mimes' => 'Format gambar harus: jpeg, png, jpg, gif, webp',
                'room_images.*.max' => 'Ukuran gambar maksimal :max KB',
                'thumbnail_index.required' => 'Thumbnail harus dipilih',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Validasi tambahan untuk ukuran file
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $image) {
                    if ($image->getSize() > 5 * 1024 * 1024) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Validasi file gagal',
                            'errors' => [
                                'room_images' => ['File ' . $image->getClientOriginalName() . ' melebihi ukuran maksimal 5MB.']
                            ]
                        ], 422);
                    }
                }
            }

            // Get property data
            $property = Property::findOrFail($validated['property_id']);

            // Validasi duplikasi nomor kamar
            $existingRoom = Room::where('property_id', $validated['property_id'])
                ->where('no', $validated['room_no'])
                ->whereNull('deleted_at')
                ->first();

            if ($existingRoom) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'room_no' => ['Nomor kamar ' . $validated['room_no'] . ' sudah ada di properti ini.']
                    ]
                ], 422);
            }

            $facilityData = $request->input('general_facilities', []);

            // Generate ID dan slug
            $idrec = Room::max('idrec') + 1;
            $tagShort = strtolower(substr($property->tags, 0, 3));
            $nameShort = strtolower(collect(explode(' ', $validated['room_name']))->map(fn($w) => substr($w, 0, 1))->implode(''));
            $slug = $tagShort . '_' . $nameShort . '_' . $idrec;

            // Process and store images
            $imagePaths = [];
            $thumbnailIndex = $request->thumbnail_index;

            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $index => $file) {
                    if (!$file->isValid()) {
                        throw new \Exception('File ' . $file->getClientOriginalName() . ' tidak valid.');
                    }

                    // Generate unique filename
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'room_' . $idrec . '_' . time() . '_' . $index . '.' . $extension;

                    // Store image
                    $path = $file->storeAs('room_images', $filename, 'public');

                    $imagePaths[] = [
                        'filename' => $path,
                        'thumbnail' => ($index == $thumbnailIndex),
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
            }

            // **PERBAIKAN: Tentukan periode berdasarkan price_type yang dikirim dari frontend**
            $priceType = $validated['price_type'];
            $periode = [
                'daily' => ($priceType === 'daily') ? 1 : 0,
                'monthly' => ($priceType === 'monthly') ? 1 : 0,
                'annual' => ($priceType === 'annual') ? 1 : 0, /* Multi-Tier Pricing: annual period flag */
            ];

            $periode_daily = $periode['daily'];
            $periode_monthly = $periode['monthly'];
            $periode_annual = $periode['annual']; /* Multi-Tier Pricing */

            // Tentukan harga berdasarkan jenis
            $dailyPrice = $priceType === 'daily' ? ($validated['daily_price'] ?? 0) : 0;
            $monthlyPrice = $priceType === 'monthly' ? ($validated['monthly_price'] ?? 0) : 0;
            $annualPrice = $priceType === 'annual' ? ($validated['annual_price'] ?? 0) : 0; /* Multi-Tier Pricing */

            /* Multi-Tier Pricing: weekday/weekend prices for daily rooms */
            $weekdayPrice = ($priceType === 'daily') ? ($validated['weekday_price'] ?? $dailyPrice) : null;
            $weekendPrice = ($priceType === 'daily') ? ($validated['weekend_price'] ?? $dailyPrice) : null;

            // Harga utama untuk field price
            $mainPrice = $priceType === 'daily' ? $dailyPrice : $monthlyPrice;

            \Log::info('Price Calculation:', [
                'price_type' => $priceType,
                'daily_price' => $dailyPrice,
                'monthly_price' => $monthlyPrice,
                'main_price' => $mainPrice,
                'periode' => $periode
            ]);

            // Save to rooms table
            $room = new Room();
            $room->idrec = $idrec;
            $room->property_id = $validated['property_id'];
            $room->property_name = $property->name;
            $room->slug = $slug;
            $room->no = $validated['room_no'];
            $room->name = $validated['room_name'];
            $room->descriptions = $validated['description_id'];
            $room->size = $validated['room_size'];
            $room->bed_type = $validated['room_bed'];
            $room->capacity = $validated['room_capacity'];
            $room->periode = $periode; // Simpan sebagai array JSON
            $room->periode_daily = $periode_daily;
            $room->periode_monthly = $periode_monthly;
            $room->periode_annual = $periode_annual; /* Multi-Tier Pricing: annual period flag */
            $room->type = $property->type;
            $room->level = 1;
            $room->facility = $facilityData; // Simpan sebagai array
            $room->price = $mainPrice; // Harga utama berdasarkan jenis
            $room->discount_percent = 0;
            $room->price_original_daily = $dailyPrice;
            $room->price_original_monthly = $monthlyPrice;
            /* Multi-Tier Pricing: save annual + weekday/weekend prices */
            $room->price_original_annual = $annualPrice;
            $room->price_weekday = $weekdayPrice;
            $room->price_weekend = $weekendPrice;
            $room->created_by = Auth::id();
            $room->status = 1;
            $room->created_at = now();
            $room->save();

            \Log::info('Room saved:', [
                'id' => $room->idrec,
                'price' => $room->price,
                'daily_price' => $room->price_original_daily,
                'monthly_price' => $room->price_original_monthly
            ]);

            // Save to room_images table
            foreach ($imagePaths as $index => $imageData) {
                $image = new MRoomImage();
                $image->room_id = $idrec;
                $image->image = $imageData['filename'];
                $image->thumbnail = $imageData['thumbnail'] ? 1 : 0;
                $image->caption = $imageData['original_name'] ?? 'No caption';
                $image->created_by = Auth::id();
                $image->created_at = now();
                $image->save();
            }

            /* Multi-Tier Pricing: Create default pricing rules + generate per-date prices using service */
            if ($priceType === 'daily' && $dailyPrice > 0) {
                $priceGeneratorService = app(RoomPriceGeneratorService::class);

                /* Create weekday + weekend pricing rules */
                $priceGeneratorService->createDefaultRules(
                    $idrec,
                    $weekdayPrice ?? $dailyPrice,
                    $weekendPrice ?? $dailyPrice,
                    Auth::id()
                );

                /* Generate per-date prices for next 365 days using priority resolution */
                $priceGeneratorService->regenerateDailyPrices($idrec);
            }

            DB::commit();

            \Log::info('Room created successfully:', ['room_id' => $idrec]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ruangan berhasil dibuat!',
                'data' => [
                    'id' => $room->idrec,
                    'room_no' => $room->no,
                    'name' => $room->name,
                    'price' => $room->price,
                    'price_type' => $priceType
                ],
                'redirect' => route('rooms.index') // Sesuaikan dengan route Anda
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation Exception:', $e->errors());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Room creation error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['room_images'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkRoomNumber(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:m_properties,idrec',
            'room_no' => 'required|string|max:20'
        ]);

        // Cek exist tapi hanya yang belum di-soft delete (deleted_at = null)
        $exists = Room::where('property_id', $request->property_id)
            ->where('no', $request->room_no)
            ->whereNull('deleted_at')
            ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Nomor kamar ini sudah ada di properti yang dipilih.'
                : 'Nomor kamar tersedia.'
        ]);
    }


    public function update(Request $request, $idrec)
    {
        // Log request data untuk debugging
        Log::info('Update Room Request:', [
            'idrec' => $idrec,
            'all_data' => $request->except(['room_images']),
        ]);

        // Validate input - accept field names from the edit modal form
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|numeric|exists:m_properties,idrec',
            'room_no' => 'required|string|max:255',
            'room_name' => 'required|string|max:255',
            'room_bed' => 'required|string|in:Single,Double,King,Queen,Twin',
            'room_capacity' => 'required|numeric|min:1',
            'description' => 'required|string',
            'daily_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            /* Multi-Tier Pricing: annual + weekday/weekend price validation for update */
            'annual_price' => 'nullable|numeric|min:0',
            'weekday_price' => 'nullable|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'general_facilities' => 'nullable|array',
            'general_facilities.*' => 'numeric',
            'periode' => 'nullable|string',
            'room_images' => 'nullable|array|max:5',
            'room_images.*' => [
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = strtolower($value->getMimeType());

                    $heicExtensions = ['heic', 'heif'];
                    $heicMimeTypes = ['image/heic', 'image/heif'];

                    if (in_array($extension, $heicExtensions) || in_array($mimeType, $heicMimeTypes)) {
                        $fail('Format HEIC/HEIF tidak didukung. Silakan gunakan JPEG, PNG, atau format gambar lainnya.');
                    }
                }
            ],
            'thumbnail_index' => 'required|integer|min:0',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'integer',
        ], [
            'property_id.required' => 'Properti harus dipilih',
            'room_no.required' => 'Nomor kamar harus diisi',
            'room_name.required' => 'Nama kamar harus diisi',
            'room_bed.required' => 'Jenis tempat tidur harus dipilih',
            'room_capacity.required' => 'Kapasitas kamar harus diisi',
            'description.required' => 'Deskripsi kamar harus diisi',
            'room_images.max' => 'Maksimal :max foto yang dapat diupload',
            'room_images.*.image' => 'File harus berupa gambar',
            'room_images.*.mimes' => 'Format gambar harus: jpeg, png, jpg, gif, webp',
            'room_images.*.max' => 'Ukuran gambar maksimal :max KB',
            'thumbnail_index.required' => 'Thumbnail harus dipilih',
        ]);

        if ($validator->fails()) {
            Log::error('Update validation failed:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Find room and property
            $room = Room::findOrFail($idrec);
            $property = Property::findOrFail($validated['property_id']);

            // Calculate total images after update
            $existingImagesCount = count($validated['existing_images'] ?? []);
            $newImagesCount = $request->hasFile('room_images') ? count($request->file('room_images')) : 0;
            $totalImages = $existingImagesCount + $newImagesCount;

            // Validate minimum 3 images
            if ($totalImages < 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'room_images' => ['Minimal 3 foto harus ada. Saat ini hanya ada ' . $totalImages . ' foto.']
                    ]
                ], 422);
            }

            // Validate maximum 5 images
            if ($totalImages > 5) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'room_images' => ['Maksimal 5 foto yang dapat diupload. Saat ini ada ' . $totalImages . ' foto.']
                    ]
                ], 422);
            }

            // Prepare facility data - now received as array from general_facilities[]
            $facilitiesArray = $validated['general_facilities'] ?? [];

            // Parse periode from JSON string if provided
            $periodeData = [];
            if (!empty($validated['periode'])) {
                try {
                    $periodeData = json_decode($validated['periode'], true);
                } catch (\Exception $e) {
                    $periodeData = [];
                }
            }

            // Determine periode based on price values or periode data
            $hasDailyPrice = !empty($validated['daily_price']) && $validated['daily_price'] > 0;
            $hasMonthlyPrice = !empty($validated['monthly_price']) && $validated['monthly_price'] > 0;
            /* Multi-Tier Pricing: detect annual pricing */
            $hasAnnualPrice = !empty($validated['annual_price']) && $validated['annual_price'] > 0;

            // Use periode data from form if available, otherwise infer from prices
            $periodeDaily = $periodeData['daily'] ?? $hasDailyPrice;
            $periodeMonthly = $periodeData['monthly'] ?? $hasMonthlyPrice;
            $periodeAnnual = $periodeData['annual'] ?? $hasAnnualPrice; /* Multi-Tier Pricing */

            // Generate new slug
            $tagShort = strtolower(substr($property->tags ?? '', 0, 3));
            $nameShort = strtolower(collect(explode(' ', $validated['room_name']))
                ->map(fn($w) => substr($w, 0, 1))
                ->implode(''));
            $slug = $tagShort . '_' . $nameShort . '_' . $idrec;

            // Get daily, monthly, and annual prices
            $dailyPrice = $validated['daily_price'] ?? 0;
            $monthlyPrice = $validated['monthly_price'] ?? 0;
            $annualPrice = $validated['annual_price'] ?? 0; /* Multi-Tier Pricing */
            $weekdayPrice = $validated['weekday_price'] ?? ($hasDailyPrice ? $dailyPrice : null);
            $weekendPrice = $validated['weekend_price'] ?? ($hasDailyPrice ? $dailyPrice : null);

            // Determine main price
            $mainPrice = $dailyPrice > 0 ? $dailyPrice : ($monthlyPrice > 0 ? $monthlyPrice : $annualPrice);

            Log::info('Update Room Data:', [
                'room_no' => $validated['room_no'],
                'room_name' => $validated['room_name'],
                'daily_price' => $dailyPrice,
                'monthly_price' => $monthlyPrice,
                'facilities' => $facilitiesArray,
                'periode_daily' => $periodeDaily,
                'periode_monthly' => $periodeMonthly,
            ]);

            // Update room data
            $room->update([
                'property_id' => $validated['property_id'],
                'property_name' => $property->name,
                'slug' => $slug,
                'no' => $validated['room_no'],
                'name' => $validated['room_name'],
                'descriptions' => $validated['description'],
                'size' => $room->size ?? 0, // Keep existing size or default to 0
                'bed_type' => $validated['room_bed'],
                'capacity' => $validated['room_capacity'],
                'periode' => json_encode([
                    'daily' => $periodeDaily,
                    'monthly' => $periodeMonthly,
                    'annual' => $periodeAnnual, /* Multi-Tier Pricing */
                ]),
                'periode_daily' => $periodeDaily ? 1 : 0,
                'periode_monthly' => $periodeMonthly ? 1 : 0,
                'periode_annual' => $periodeAnnual ? 1 : 0, /* Multi-Tier Pricing */
                'type' => $property->type,
                'level' => 1,
                'facility' => $facilitiesArray,
                'price' => $mainPrice,
                'price_original_daily' => $dailyPrice,
                'price_original_monthly' => $monthlyPrice,
                /* Multi-Tier Pricing: save annual + weekday/weekend prices on update */
                'price_original_annual' => $annualPrice,
                'price_weekday' => $weekdayPrice,
                'price_weekend' => $weekendPrice,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            // Reset all thumbnails first
            MRoomImage::where('room_id', $idrec)->update(['thumbnail' => false]);

            // Handle image deletion
            if (!empty($validated['delete_images'])) {
                $imagesToDelete = MRoomImage::where('room_id', $idrec)
                    ->whereIn('idrec', $validated['delete_images'])
                    ->get();

                // Delete physical files from storage
                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image->image)) {
                        Storage::disk('public')->delete($image->image);
                    }
                }

                // Delete from database
                MRoomImage::where('room_id', $idrec)
                    ->whereIn('idrec', $validated['delete_images'])
                    ->delete();
            }

            // Get remaining existing images
            $existingImages = MRoomImage::where('room_id', $idrec)
                ->orderBy('idrec')
                ->get();

            // Handle new image uploads
            $newImageIds = [];
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $index => $file) {
                    if (!$file->isValid()) continue;

                    // Generate unique filename
                    $filename = 'room_' . $idrec . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();

                    // Store file in storage
                    $filePath = $file->storeAs('room_images', $filename, 'public');

                    $newImage = MRoomImage::create([
                        'room_id' => $idrec,
                        'image' => $filePath, // Store file path instead of base64
                        'caption' => $file->getClientOriginalName(),
                        'thumbnail' => false, // Will be set later if needed
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                    ]);

                    $newImageIds[] = $newImage->idrec;
                }
            }

            // Determine and set the thumbnail
            $thumbnailIndex = $validated['thumbnail_index'] ?? null;
            $totalImagesBeforeNew = $existingImages->count();

            if ($thumbnailIndex !== null) {
                if ($thumbnailIndex < $totalImagesBeforeNew) {
                    // Thumbnail is an existing image
                    $existingImages[$thumbnailIndex]->update(['thumbnail' => true]);
                } else {
                    // Thumbnail is a new image
                    $newImageIndex = $thumbnailIndex - $totalImagesBeforeNew;
                    if (isset($newImageIds[$newImageIndex])) {
                        MRoomImage::where('idrec', $newImageIds[$newImageIndex])
                            ->update(['thumbnail' => true]);
                    }
                }
            } else {
                // No thumbnail specified, set first existing image as thumbnail if available
                $firstImage = MRoomImage::where('room_id', $idrec)->first();
                if ($firstImage) {
                    $firstImage->update(['thumbnail' => true]);
                }
            }

            // PRICE HANDLING ==============================================

            /* Multi-Tier Pricing: Update pricing rules + regenerate per-date prices using service */
            if ($periodeDaily && $dailyPrice > 0) {
                $priceGeneratorService = app(RoomPriceGeneratorService::class);

                /* Update weekday + weekend pricing rules */
                $priceGeneratorService->createDefaultRules(
                    $idrec,
                    $weekdayPrice ?? $dailyPrice,
                    $weekendPrice ?? $dailyPrice,
                    Auth::id()
                );

                /* Regenerate per-date prices (preserves manual overrides + season/holiday rules) */
                $priceGeneratorService->regenerateDailyPrices($idrec);
            } else {
                /* Non-daily room: remove all per-date prices */
                RoomPrices::where('room_id', $idrec)->delete();
            }

            // Commit transaction
            DB::commit();

            Log::info('Room updated successfully:', ['room_id' => $idrec]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kamar berhasil diperbarui!',
                'data' => [
                    'id' => $room->idrec,
                    'room_no' => $room->no,
                    'name' => $room->name,
                    'price' => $room->price,
                ],
                'redirect' => route('rooms.index')
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation Exception:', $e->errors());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['room_images'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePriceIndex(Room $room)
    {
        $rooms = Room::where('status', '!=', '2')
            ->with('transactions', 'bookings', 'property', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        $properties = Property::orderBy('name', 'asc')->get();
        return view('pages.Properties.m-Rooms.edit-prices', compact('room'));
    }

    public function getPriceForDate(Request $request, $roomId)
    {
        $date = $request->query('date');
        // dd($date);
        $price = DB::table('m_room_prices')
            ->where('room_id', $roomId)
            ->where('date', $date)
            ->value('price');

        return response()->json(['price' => $price ?? 'Price not found.']);
    }

    public function updatePriceRange(Request $request, $roomId)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'price' => 'required|numeric|min:0',
            ]);

            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);

            // Build an inclusive collection of date strings between start and end
            $dates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $dates->push($date->toDateString());
            }
            // dd($dates);

            DB::beginTransaction();

            // Step 1: Delete existing records for room_id and each date
            DB::table('m_room_prices')
                ->where('room_id', $roomId)
                ->whereIn('date', $dates)
                ->delete();

            // Step 2: Insert new records
            $rows = $dates->map(fn($date) => [
                'room_id' => $roomId,
                'date' => $date,
                'price' => $validated['price'],
                'status' => '1',
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            DB::table('m_room_prices')->insert($rows->toArray());

            DB::commit();

            return response()->json(['message' => 'Harga berhasil diperbarui!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui harga.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoomPrices(Request $request, $roomId)
    {
        $year = $request->get('year');
        $month = $request->get('month');

        $start = Carbon::createFromDate($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        // dd($start, $end);

        /* Multi-Tier Pricing: return both price and price_type for calendar color coding */
        $prices = RoomPrices::where('room_id', $roomId)
            ->whereBetween('date', [$start, $end])
            ->get(['date', 'price', 'price_type'])
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->date)->toDateString() => [
                    'price' => $item->price,
                    'type' => $item->price_type ?? 'weekday',
                ]];
            });

        return response()->json($prices);
    }

    public function updateStatus(Room $room, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);

        /* Block status change if room has current or future bookings (check_out >= today) */
        $hasActiveBookings = $room->bookings()
            ->where('status', 1)
            ->whereHas('transaction', function ($q) {
                $q->where('check_out', '>=', now())
                  ->where('transaction_status', 'paid');
            })
            ->exists();

        if ($hasActiveBookings) {
            return response()->json([
                'success' => false,
                'message' => __('ui.room_status_has_bookings')
            ], 422);
        }

        $room->update([
            'status' => $request->status,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Set all rooms to active status
     */
    public function setActiveAll(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Room::where('status', '!=', '2'); // Exclude deleted rooms

            // Filter berdasarkan akses user
            if (!$user->isSuperAdmin() && $user->property_id) {
                $query->where('property_id', $user->property_id);
            }

            // Filter by property_id if provided
            if ($request->has('property_id') && $request->property_id != '') {
                $query->where('property_id', $request->property_id);
            }

            $updatedCount = $query->where('status', 0)->update([
                'status' => 1,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $updatedCount . ' kamar berhasil diaktifkan',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengaktifkan kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($idrec)
    {
        try {
            $room = Room::findOrFail($idrec);

            $room->update([
                'status' => '2',
                'deleted_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Room deleted successfully', ['room_id' => $idrec]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kamar berhasil dihapus!'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Room not found for deletion', ['room_id' => $idrec]);
            return response()->json([
                'status' => 'error',
                'message' => 'Kamar tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Room deletion error: ' . $e->getMessage(), [
                'room_id' => $idrec,
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus kamar: ' . $e->getMessage()
            ], 500);
        }
    }


    // `````````````m_Room Facility Management```````````````````````````

    public function indexFacility(Request $request)
    {
        $query = RoomFacility::with(['createdBy', 'updatedBy'])
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
            ->orderBy('created_at', 'desc');

        $perPage = $request->per_page ?? 5;
        $facilities = $perPage === 'all'
            ? $query->get()
            : $query->paginate($perPage)->withQueryString();

        // Jika request AJAX, kembalikan bagian table dan pagination
        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            $tableHtml = view('pages.Properties.Facility_rooms.partials.facility-room_table', compact('facilities'))->render();
            $paginationHtml = $facilities instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $facilities->appends($request->input())->links()->toHtml()
                : '';

            return '<div id="tableContainer">' . $tableHtml . '</div>'
                 . '<div id="paginationContainer">' . $paginationHtml . '</div>';
        }

        return view('pages.Properties.Facility_rooms.index', compact('facilities'));
    }

    public function storeFacility(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'facility' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean', // Changed to boolean
        ]);

        try {
            // Create a new facility record
            $facility = RoomFacility::create([
                'facility' => $validatedData['facility'],
                'icon' => $validatedData['icon'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'status' => $validatedData['status'] ? 1 : 0, // Convert to 1/0
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facility created successfully',
                'data' => $facility,
                'redirect_url' => route('facilityRooms.index')
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
            'status' => 'required|boolean'
        ]);

        try {
            // Ambil data facility berdasarkan ID
            $facility = RoomFacility::find($id);

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
                'status' => $validatedData['status'] ? 1 : 0,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facility updated successfully',
                'data' => $facility,
                'redirect_url' => route('facilityRooms.index')
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
            $facility = RoomFacility::find($request->id);

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
