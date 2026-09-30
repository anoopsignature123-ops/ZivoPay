<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DepositRequest;
use App\Http\Resources\Api\DepositResource;
use App\Services\Api\DepositApiService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class DepositController extends Controller
{
    use ApiResponse;

    /**
     * Get Add Fund / Deposit History & Statistics API.
     */
    public function index(Request $request, DepositApiService $depositApiService): JsonResponse
    {
        try {
            $user = $request->user();
            $filters = $request->only(['search', 'status', 'from_date', 'to_date']);
            $perPage = (int) $request->get('per_page', 15);

            $data = $depositApiService->getDepositHistory($user, $filters, $perPage);

            /** @var LengthAwarePaginator $deposits */
            $deposits = $data['deposits'];

            return $this->successResponse([
                'deposits' => DepositResource::collection($deposits->items()),
                'stats' => $data['stats'],
                'pagination' => [
                    'current_page' => $deposits->currentPage(),
                    'last_page' => $deposits->lastPage(),
                    'per_page' => $deposits->perPage(),
                    'total' => $deposits->total(),
                ],
            ], 'Deposit history and statistics retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch deposit history: '.$e->getMessage(), 500);
        }
    }

    /**
     * Submit Add Fund / Deposit Request API.
     */
    public function store(DepositRequest $request, DepositApiService $depositApiService): JsonResponse
    {
        try {
            $user = $request->user();
            $proofFile = $request->file('proof_file');

            $deposit = $depositApiService->storeDeposit(
                $user,
                $request->validated(),
                $proofFile
            );

            $freshUser = $user->fresh();

            return $this->successResponse([
                'deposit' => new DepositResource($deposit),
                'deposit_wallet_balance' => (float) $freshUser->deposit_wallet,
            ], 'Success! ₹'.number_format((float) $deposit->final_amount, 2).' has been credited to your Fund Wallet.', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Add fund request failed validation.');
        } catch (\Exception $e) {
            return $this->errorResponse('Add fund request failed: '.$e->getMessage(), 500);
        }
    }
}
