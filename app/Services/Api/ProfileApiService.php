<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileApiService
{
    /**
     * Update user profile information, image, and/or password.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $imageFile = null): User
    {
        $updateData = [];

        if (array_key_exists('name', $data) && $data['name'] !== null) {
            $updateData['name'] = trim((string) $data['name']);
        }

        if (array_key_exists('mobile', $data) && $data['mobile'] !== null) {
            $updateData['mobile'] = trim((string) $data['mobile']);
        }

        if (array_key_exists('wallet_address', $data) && $data['wallet_address'] !== null) {
            $updateData['wallet_address'] = trim((string) $data['wallet_address']);
        }

        // Handle Profile Image Upload
        if ($imageFile instanceof UploadedFile) {
            // Delete old profile image if exists
            if (! empty($user->image) && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Store new image file in public disk
            $imagePath = $imageFile->store('profile_images', 'public');
            $updateData['image'] = $imagePath;
        }

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        return $user->fresh();
    }

    /**
     * Change user account password.
     *
     * @throws ValidationException
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Your current password does not match our records.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Revoke all API tokens so user is logged out automatically across all sessions
        $user->tokens()->delete();
    }
}
