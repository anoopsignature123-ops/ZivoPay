<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    /**
     * Send an active database email template using Zepto Mail or Laravel's configured mailer.
     *
     * @param  array<string, scalar|null>  $variables
     */
    public function send(string $templateKey, string $recipient, array $variables = []): bool
    {
        $template = EmailTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            Log::warning('Email template is missing or inactive.', ['template' => $templateKey]);

            return false;
        }

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Email template recipient is invalid.', ['template' => $templateKey]);

            return false;
        }

        $variables = array_merge([
            'site_name' => config('app.name', 'ZIVO PAY'),
            'support_email' => config('mail.from.address'),
        ], $variables);
        $subject = $this->render($template->subject, $variables);
        $body = $this->render($template->body, $variables);

        if ($this->sendWithZeptoMail($recipient, $subject, $body, $templateKey)) {
            return true;
        }

        try {
            Mail::html($body, function ($message) use ($recipient, $subject): void {
                $message->to($recipient)->subject($subject);
            });

            return true;
        } catch (\Throwable $exception) {
            Log::error('Email template delivery failed through the configured mailer.', [
                'template' => $templateKey,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /** @param array<string, scalar|null> $variables */
    private function render(string $content, array $variables): string
    {
        return (string) preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.-]+)\s*\}\}/', function (array $match) use ($variables): string {
            if (! Arr::has($variables, $match[1])) {
                return $match[0];
            }

            return e((string) Arr::get($variables, $match[1]));
        }, $content);
    }

    private function sendWithZeptoMail(string $recipient, string $subject, string $body, string $templateKey): bool
    {
        $apiKey = (string) config('services.zeptomail.api_key', '');
        $fromAddress = (string) (config('services.zeptomail.from_address') ?: config('mail.from.address'));
        $apiBase = (string) config('services.zeptomail.api_base', 'https://api.zeptomail.in/v1.1/email');

        if ($apiKey === '' || $fromAddress === '') {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-enczapikey '.$apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)
                ->withOptions(['verify' => (bool) config('services.zeptomail.verify_ssl', true)])
                ->post($apiBase, [
                    'from' => [
                        'address' => $fromAddress,
                        'name' => config('services.zeptomail.from_name', config('app.name', 'ZIVO PAY')),
                    ],
                    'to' => [[
                        'email_address' => ['address' => $recipient],
                    ]],
                    'subject' => $subject,
                    'htmlbody' => $body,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Zepto Mail rejected the message; trying Laravel mailer fallback.', [
                'template' => $templateKey,
                'status' => $response->status(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Zepto Mail delivery failed; trying Laravel mailer fallback.', [
                'template' => $templateKey,
                'exception' => $exception->getMessage(),
            ]);
        }

        return false;
    }
}
