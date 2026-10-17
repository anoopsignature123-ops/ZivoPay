<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmailTemplate::query()->updateOrCreate(
            ['key' => 'password-reset'],
            [
                'name' => 'Password Reset Link',
                'subject' => 'Reset your {{ site_name }} password',
                'body' => <<<'HTML'
<div style="margin:0;padding:32px 12px;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:560px;margin:auto;background:#ffffff;border:1px solid #d1fae5;border-radius:18px;overflow:hidden;">
        <div style="padding:24px;text-align:center;background:#042718;color:#ffffff;">
            <h1 style="margin:0;color:#34d399;font-size:24px;letter-spacing:2px;">{{ site_name }}</h1>
            <p style="margin:8px 0 0;color:#bbf7d0;font-size:13px;">Password reset request</p>
        </div>
        <div style="padding:28px 26px;">
            <p style="font-size:15px;line-height:1.6;">Hello {{ name }},</p>
            <p style="font-size:14px;line-height:1.7;color:#334155;">We received a request to reset your password. Use the secure button below within {{ expires_minutes }} minutes.</p>
            <p style="margin:28px 0;text-align:center;"><a href="{{ reset_url }}" style="display:inline-block;padding:14px 26px;border-radius:10px;background:#059669;color:#ffffff;text-decoration:none;font-weight:bold;">Reset password</a></p>
            <p style="font-size:12px;line-height:1.7;color:#64748b;">If you did not request this, you can ignore this email. The link can only be used once and expires shortly. Never share this link or your password.</p>
        </div>
        <div style="padding:16px;text-align:center;background:#f8fafc;color:#64748b;font-size:11px;">Need help? Contact {{ support_email }}</div>
    </div>
</div>
HTML,
                'variables' => ['site_name', 'name', 'reset_url', 'expires_minutes', 'support_email'],
                'is_active' => true,
            ],
        );
    }
}
