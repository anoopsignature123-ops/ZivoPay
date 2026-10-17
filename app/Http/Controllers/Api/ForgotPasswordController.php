<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailTemplateService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use ApiResponse;

    public function sendLink(Request $request, EmailTemplateService $emails): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        try {
            Password::broker()->sendResetLink(
                ['email' => trim($data['email'])],
                function ($user, string $token) use ($emails): string {
                    $resetUrl = route('password.reset.form', [
                        'token' => $token,
                        'email' => $user->getEmailForPasswordReset(),
                    ]);
                    $recipient = $user->getEmailForPasswordReset();
                    $recipientName = $user->name;
                    $userId = $user->id;
                    $expiresMinutes = (int) config('auth.passwords.users.expire', 5);

                    defer(function () use ($emails, $recipient, $recipientName, $resetUrl, $expiresMinutes, $userId): void {
                        $sent = $emails->send('password-reset', $recipient, [
                            'name' => $recipientName,
                            'reset_url' => $resetUrl,
                            'expires_minutes' => $expiresMinutes,
                        ]);

                        if (! $sent) {
                            Log::error('Password reset email could not be delivered.', ['user_id' => $userId]);
                        }
                    });

                    return Password::RESET_LINK_SENT;
                }
            );
        } catch (\Throwable $exception) {
            Log::error('Password reset link creation or delivery failed.', [
                'exception' => $exception->getMessage(),
            ]);
        }

        return $this->successResponse(
            null,
            'If an account exists for that email, a password reset link will be sent shortly.'
        );
    }
}
