<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\ParkingFee;
use Illuminate\Http\Request;

class ParkingFeeController extends ApiController
{
    // GET /api/v1/parking-fee
    public function index(Request $request)
    {
        try {
            $query = ParkingFee::query();

            if ($request->has('property_id')) {
                $query->where('property_id', $request->property_id);
            }

            if ($request->has('parking_type')) {
                $query->where('parking_type', $request->parking_type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $parkingFees = $query->get();

            return $this->respond([
                'data' => $parkingFees
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // GET /api/v1/parking-fee/{id}
    public function show($id)
    {
        try {
            $parkingFee = ParkingFee::find($id);

            if (!$parkingFee) {
                return $this->respondNotFound('Parking fee not found');
            }

            return $this->respond([
                'data' => $parkingFee
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // GET /api/v1/parking-fee/property/{property_id}
    public function byPropertyId($property_id)
    {
        try {
            $parkingFees = ParkingFee::where('property_id', $property_id)->get();

            return $this->respond([
                'data' => $parkingFees
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // POST /api/v1/parking-fee
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'property_id' => 'required|integer',
                'parking_type' => 'required|string',
                'fee' => 'required|numeric',
                'capacity' => 'nullable|integer',
                'status' => 'nullable|string',
                'created_by' => 'nullable|integer',
                'updated_by' => 'nullable|integer',
            ]);

            $parkingFee = ParkingFee::create($data);

            return $this->setStatusCode(201)->respond([
                'message' => 'Parking fee created successfully',
                'data' => $parkingFee
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->respondBadRequest('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // PUT/PATCH /api/v1/parking-fee/{id}
    public function update(Request $request, $id)
    {
        try {
            $parkingFee = ParkingFee::find($id);

            if (!$parkingFee) {
                return $this->respondNotFound('Parking fee not found');
            }

            $data = $request->validate([
                'property_id' => 'sometimes|integer',
                'parking_type' => 'sometimes|string',
                'fee' => 'sometimes|numeric',
                'capacity' => 'nullable|integer',
                'status' => 'nullable|string',
                'updated_by' => 'nullable|integer',
            ]);

            $parkingFee->update($data);

            return $this->respond([
                'message' => 'Parking fee updated successfully',
                'data' => $parkingFee
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->respondBadRequest('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // DELETE /api/v1/parking-fee/{id}
    public function destroy($id)
    {
        try {
            $parkingFee = ParkingFee::find($id);

            if (!$parkingFee) {
                return $this->respondNotFound('Parking fee not found');
            }

            $parkingFee->delete();

            return $this->respond([
                'message' => 'Parking fee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
}
