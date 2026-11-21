<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsService
{
    protected string $gatewayUrl;

    protected string $apiKey;

    protected string $senderId;

    protected string $registerTemplateId;

    protected string $otpTemplateId;

    public function __construct()
    {
        $this->gatewayUrl = (string) config('sms.gateway_url', 'https://sms.textifydigitals.in/vb/apikey.php');
        $this->apiKey = (string) config('sms.api_key', 'oyiF8hqbfiPYNddW');
        $this->senderId = (string) config('sms.sender_id', 'ZIVOFS');
        $this->registerTemplateId = (string) config('sms.templates.register.id', '1777179516697440701');
        $this->otpTemplateId = (string) config('sms.templates.otp.id', '1777179507319570437');
    }

    /**
     * Send Login OTP SMS via DLT Gateway.
     *
     * Template:
     * Dear User, Your OTP is {#num#} for login to ZIVO APP. This OTP is valid for 5 minutes. Do not share it with anyone. ZIVO APP is operated by ZIVO E-COMMERCE & FINANCIAL SERVICES. Visit: https://zivopay.net
     */
    public function sendLoginOtp(string $mobile, string $otp): bool
    {
        $message = "Dear User, Your OTP is {$otp} for login to ZIVO APP. This OTP is valid for 5 minutes. Do not share it with anyone. ZIVO APP is operated by ZIVO E-COMMERCE & FINANCIAL SERVICES. Visit: https://zivopay.net";

        return $this->sendSms($mobile, $message, $this->otpTemplateId);
    }

    /**
     * Send Registration Welcome SMS with User ID and Password via DLT Gateway.
     *
     * Template:
     * Dear {#alp#}, Congratulations! Your registration with ZIVO PAY, operated by ZIVO E-COMMERCE & FINANCIAL SERVICES, has been successfully completed on {#alp#}. Your User ID is {#alp#} and your Temporary Password is {#alp#}. Please keep your login credentials confidential. Thank you for choosing ZIVO PAY.
     */
    public function sendRegistrationWelcome(string $mobile, string $name, string $date, string $userId, string $password): bool
    {
        $message = "Dear {$name}, Congratulations! Your registration with ZIVO PAY, operated by ZIVO E-COMMERCE & FINANCIAL SERVICES, has been successfully completed on {$date}. Your User ID is {$userId} and your Temporary Password is {$password}. Please keep your login credentials confidential. Thank you for choosing ZIVO PAY.";

        return $this->sendSms($mobile, $message, $this->registerTemplateId);
    }

    /**
     * Send HTTP GET request to Textify Digitals SMS Gateway.
     */
    public function sendSms(string $mobile, string $message, string $templateId): bool
    {
        $cleanMobile = $this->filterMobileNumber($mobile);
        if (! $cleanMobile) {
            Log::warning('[SMS] Invalid Indian mobile number rejected for delivery', [
                'raw_mobile' => $mobile,
                'template_id' => $templateId,
            ]);

            return false;
        }

        if (empty($this->apiKey) || empty($this->gatewayUrl)) {
            Log::error('[SMS] Gateway credentials missing in configuration.');

            return false;
        }

        try {
            $url = $this->gatewayUrl.'?apikey='.urlencode($this->apiKey)
                .'&senderid='.urlencode($this->senderId)
                .'&templateid='.urlencode($templateId)
                .'&number='.urlencode($cleanMobile)
                .'&message='.urlencode($message);

            $response = Http::timeout(10)->get($url);

            Log::info('[SMS] Dispatch request sent to Textify Digitals', [
                'mobile' => $cleanMobile,
                'template_id' => $templateId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return $response->successful();
        } catch (Throwable $e) {
            Log::error('[SMS] Failed to send SMS via Textify Digitals', [
                'mobile' => $cleanMobile,
                'template_id' => $templateId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Filter & validate 10-digit Indian mobile number (without +91).
     */
    public function filterMobileNumber(string $mobile): ?string
    {
        $cleaned = preg_replace('/\D/', '', $mobile);

        if (strlen($cleaned) === 12 && str_starts_with($cleaned, '91')) {
            $cleaned = substr($cleaned, 2);
        }

        if (preg_match('/^[6-9][0-9]{9}$/', $cleaned)) {
            return $cleaned;
        }

        return null;
    }
}
