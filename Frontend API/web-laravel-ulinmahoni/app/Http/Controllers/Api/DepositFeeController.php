<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\DepositFee;
use Illuminate\Http\Request;

class DepositFeeController extends ApiController
{
    // GET /api/v1/deposit-fee
    public function index(Request $request)
    {
        try {
            $query = DepositFee::query();

            if ($request->has('property_id')) {
                $query->where('property_id', $request->property_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $depositFees = $query->get();

            return $this->respond([
                'data' => $depositFees
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // GET /api/v1/deposit-fee/{id}
    public function show($id)
    {
        try {
            $depositFee = DepositFee::find($id);

            if (!$depositFee) {
                return $this->respondNotFound('Deposit fee not found');
            }

            return $this->respond([
                'data' => $depositFee
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // GET /api/v1/deposit-fee/property/{property_id}
    public function byPropertyId($property_id)
    {
        try {
            $depositFees = DepositFee::where('property_id', $property_id)->get();

            return $this->respond([
                'data' => $depositFees
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // POST /api/v1/deposit-fee
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'property_id' => 'required|integer',
                'amount' => 'required|numeric',
                'status' => 'nullable|string',
                'created_by' => 'nullable|integer',
                'updated_by' => 'nullable|integer',
            ]);

            $depositFee = DepositFee::create($data);

            return $this->setStatusCode(201)->respond([
                'message' => 'Deposit fee created successfully',
                'data' => $depositFee
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->respondBadRequest('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // PUT/PATCH /api/v1/deposit-fee/{id}
    public function update(Request $request, $id)
    {
        try {
            $depositFee = DepositFee::find($id);

            if (!$depositFee) {
                return $this->respondNotFound('Deposit fee not found');
            }

            $data = $request->validate([
                'property_id' => 'sometimes|integer',
                'amount' => 'sometimes|numeric',
                'status' => 'nullable|string',
                'updated_by' => 'nullable|integer',
            ]);

            $depositFee->update($data);

            return $this->respond([
                'message' => 'Deposit fee updated successfully',
                'data' => $depositFee
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->respondBadRequest('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // DELETE /api/v1/deposit-fee/{id}
    public function destroy($id)
    {
        try {
            $depositFee = DepositFee::find($id);

            if (!$depositFee) {
                return $this->respondNotFound('Deposit fee not found');
            }

            $depositFee->delete();

            return $this->respond([
                'message' => 'Deposit fee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
}
