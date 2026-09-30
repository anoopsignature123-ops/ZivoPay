<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\Api\UserResource;
use App\Services\Api\ProfileApiService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * Get Authenticated User Profile API.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('sponsor');

        return $this->successResponse([
            'user' => new UserResource($user),
        ], 'User profile details retrieved successfully.');
    }

    /**
     * Update Authenticated User Profile & Profile Avatar Image API.
     */
    public function update(UpdateProfileRequest $request, ProfileApiService $profileService): JsonResponse
    {
        try {
            $imageFile = $request->file('image') ?? $request->file('profile_image');

            $updatedUser = $profileService->updateProfile(
                $request->user(),
                $request->validated(),
                $imageFile
            );

            return $this->successResponse([
                'user' => new UserResource($updatedUser),
            ], 'Profile details updated successfully!');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Profile update failed.');
        } catch (\Exception $e) {
            return $this->errorResponse('Profile update failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Change Account Password API.
     */
    public function changePassword(ChangePasswordRequest $request, ProfileApiService $profileService): JsonResponse
    {
        try {
            $profileService->changePassword(
                $request->user(),
                $request->input('current_password'),
                $request->input('password')
            );

            return $this->successResponse(null, 'Password changed successfully! You have been logged out. Please log in again with your new password.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Password change failed.');
        } catch (\Exception $e) {
            return $this->errorResponse('Password change failed: '.$e->getMessage(), 500);
        }
    }
}
