<?php

namespace App\Services;

use App\Models\LoginAudit;
use App\Models\User;
use Illuminate\Http\Request;

class LoginAuditRecorder
{
    public function recordSuccessfulLogin(User $user, Request $request, string $channel, string $loginMethod): void
    {
        LoginAudit::query()->create([
            'user_id' => $user->id,
            'channel' => $channel,
            'login_method' => $loginMethod,
            'ip_address' => $request->ip(),
            'device_name' => $this->headerValue($request, 'X-Device-Name', 255),
            'device_model' => $this->headerValue($request, 'X-Device-Model', 255),
            'platform' => $this->headerValue($request, 'X-Platform', 100),
            'os_version' => $this->headerValue($request, 'X-OS-Version', 100),
            'app_version' => $this->headerValue($request, 'X-App-Version', 50),
            'user_agent' => $this->headerValue($request, 'User-Agent', 2000),
            'logged_in_at' => now(),
        ]);
    }

    private function headerValue(Request $request, string $header, int $maxLength): ?string
    {
        $value = trim((string) $request->header($header, ''));

        return $value === '' ? null : mb_substr($value, 0, $maxLength);
    }
}
