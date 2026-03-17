<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\ApiController;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends ApiController
{
    /**
     * Get all banners or filter by status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Banner::query();
            
            // Add filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Order by created_at (newest first)
            $query->orderBy('created_at', 'desc');
            
            // Check if pagination is requested
            if ($request->has('limit') && $request->has('page')) {
                $banners = $query->paginate($request->limit);
                
                return $this->respondWithPagination($banners, [
                    'data' => $banners->items()
                ]);
            }
            
            // Get all banners
            $banners = $query->get();
            
            return $this->respond([
                'data' => $banners
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
    
    /**
     * Get a specific banner by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $banner = Banner::find($id);
            
            if (!$banner) {
                return $this->respondNotFound('Banner not found');
            }
            
            return $this->respond([
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
    
    /**
     * Create a new banner
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'attachment' => 'required|string',
                'descriptions' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                'created_by' => 'required|integer'
            ]);
            
            $banner = Banner::create($validated);
            
            return $this->respondCreated('Banner created successfully');
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }
    
    /**
     * Update an existing banner
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $banner = Banner::find($id);
            
            if (!$banner) {
                return $this->respondNotFound('Banner not found');
            }
            
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'attachment' => 'sometimes|string',
                'descriptions' => 'nullable|string',
                'status' => 'sometimes|in:active,inactive',
                'updated_by' => 'required|integer'
            ]);
            
            $banner->update($validated);
            
            return $this->respond([
                'message' => 'Banner updated successfully',
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }
    
    /**
     * Delete a banner
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $banner = Banner::find($id);
            
            if (!$banner) {
                return $this->respondNotFound('Banner not found');
            }
            
            $banner->delete();
            
            return $this->respondDeleted('Banner deleted successfully');
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
} 