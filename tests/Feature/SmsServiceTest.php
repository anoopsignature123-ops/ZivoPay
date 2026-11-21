<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['id' => 1], ['name' => 'Admin', 'slug' => 'admin']);
        Role::firstOrCreate(['id' => 2], ['name' => 'User', 'slug' => 'user']);
    }

    public function test_sms_service_sends_login_otp_with_dlt_template(): void
    {
        Http::fake([
            'sms.textifydigitals.in/*' => Http::response('SUCCESS: Message Sent', 200),
        ]);

        $smsService = app(SmsService::class);
        $result = $smsService->sendLoginOtp('9876543210', '123456');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sms.textifydigitals.in/vb/apikey.php')
                && str_contains($request->url(), 'apikey=oyiF8hqbfiPYNddW')
                && str_contains($request->url(), 'senderid=ZIVOFS')
                && str_contains($request->url(), 'templateid=1777179507319570437')
                && str_contains($request->url(), 'number=9876543210')
                && str_contains($request->url(), '123456');
        });
    }

    public function test_sms_service_sends_registration_welcome_with_dlt_template(): void
    {
        Http::fake([
            'sms.textifydigitals.in/*' => Http::response('SUCCESS: Message Sent', 200),
        ]);

        $smsService = app(SmsService::class);
        $result = $smsService->sendRegistrationWelcome('9876543210', 'Rahul Sharma', '21-11-2026', 'ZIVO-123456', 'Pass@123');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'templateid=1777179516697440701')
                && str_contains($request->url(), 'number=9876543210')
                && str_contains($request->url(), urlencode('Rahul Sharma'))
                && str_contains($request->url(), urlencode('ZIVO-123456'));
        });
    }

    public function test_api_send_otp_and_verify_otp_flow(): void
    {
        Http::fake([
            'sms.textifydigitals.in/*' => Http::response('SUCCESS', 200),
        ]);

        $phone = '98'.rand(10000000, 99999999);
        $user = User::create([
            'role_id' => 2,
            'name' => 'OTP Member',
            'email' => 'otp_'.uniqid().'@test.com',
            'mobile' => $phone,
            'referral_code' => User::generateReferralCode(),
            'password' => bcrypt('password123'),
        ]);

        $sendResponse = $this->postJson('/api/login/send-otp', [
            'mobile' => $phone,
        ]);

        $sendResponse->assertOk();
        $this->assertTrue($sendResponse->json('success'));

        $cachedOtp = Cache::get('otp_'.$phone);
        $this->assertNotEmpty($cachedOtp);

        $verifyResponse = $this->postJson('/api/login/verify-otp', [
            'mobile' => $phone,
            'otp' => $cachedOtp,
        ]);

        $verifyResponse->assertOk();
        $this->assertTrue($verifyResponse->json('success'));
        $this->assertNotEmpty($verifyResponse->json('data.token'));
    }

    public function test_registration_triggers_dlt_welcome_sms(): void
    {
        Http::fake([
            'sms.textifydigitals.in/*' => Http::response('SUCCESS', 200),
        ]);

        $phone = '98'.rand(10000000, 99999999);
        $email = 'reg_'.uniqid().'@test.com';

        $response = $this->post('/user/register', [
            'name' => 'New User SMS Test',
            'email' => $email,
            'mobile' => $phone,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'sponsor_id' => 'ZIVO-0000001',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'mobile' => $phone,
        ]);

        Http::assertSent(function ($request) use ($phone) {
            return str_contains($request->url(), 'templateid=1777179516697440701')
                && str_contains($request->url(), "number={$phone}");
        });
    }

    public function test_api_registration_triggers_dlt_welcome_sms(): void
    {
        Http::fake([
            'sms.textifydigitals.in/*' => Http::response('SUCCESS', 200),
        ]);

        $phone = '98'.rand(10000000, 99999999);
        $email = 'apireg_'.uniqid().'@test.com';

        $regResponse = $this->postJson('/api/register', [
            'name' => 'API Registered Member',
            'email' => $email,
            'mobile' => $phone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'sponsor_code' => 'ZIVO-0000001',
        ]);

        $regResponse->assertStatus(201);
        $this->assertTrue($regResponse->json('success'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'mobile' => $phone,
        ]);

        Http::assertSent(function ($request) use ($phone) {
            return str_contains($request->url(), 'templateid=1777179516697440701')
                && str_contains($request->url(), "number={$phone}");
        });
    }
}
