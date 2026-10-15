<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Services\Shop\ShopPaymentConfiguration;
use App\Services\Shop\ShopRazorpayService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function configuration(ShopPaymentConfiguration $configuration): JsonResponse
    {
        return $this->successResponse($configuration->forApp(), 'Shop payment configuration retrieved.');
    }

    public function webhook(Request $request, ShopRazorpayService $razorpay): JsonResponse
    {
        $eventId = (string) $request->header('X-Razorpay-Event-Id');
        if (! $razorpay->processWebhook(
            $request->getContent(),
            (string) $request->header('X-Razorpay-Signature'),
            $eventId,
        )) {
            return response()->json(['message' => 'Invalid Shop payment webhook signature or payload.'], 400);
        }

        return response()->json(['message' => 'Shop payment webhook accepted.']);
    }
}
