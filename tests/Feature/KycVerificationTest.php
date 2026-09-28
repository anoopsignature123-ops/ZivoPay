<?php

namespace Tests\Feature;

use App\Models\Kyc;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KycVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected int $adminRoleId;

    protected int $userRoleId;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['id' => 1], ['name' => 'Admin', 'slug' => 'admin']);
        $userRole = Role::firstOrCreate(['id' => 2], ['name' => 'User', 'slug' => 'user']);

        $this->adminRoleId = $adminRole->id;
        $this->userRoleId = $userRole->id;
    }

    public function test_unverified_user_cannot_place_withdrawal_request(): void
    {
        $user = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Unverified Member',
            'email' => 'unverified@zivopay.com',
            'referral_code' => 'ZIVO-UNV01',
            'kyc_status' => 'unverified',
            'earning_wallet' => 5000.00,
        ]);

        $response = $this->actingAs($user)->post(route('user.withdrawal.store'), [
            'from_wallet' => 'earning_wallet',
            'amount' => 1000,
            'payment_method' => 'Bank Transfer',
            'account_details' => 'Bank: SBI, A/C: 1234567890, IFSC: SBIN0001234',
        ]);

        $response->assertSessionHasErrors(['kyc']);
        $this->assertDatabaseMissing('withdrawals', [
            'user_id' => $user->id,
            'amount' => 1000,
        ]);
    }

    public function test_user_can_submit_kyc_documents(): void
    {
        Storage::fake('public');

        $user = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'John Doe',
            'email' => 'johndoe@zivopay.com',
            'referral_code' => 'ZIVO-KYC01',
            'kyc_status' => 'unverified',
        ]);

        $frontFile = UploadedFile::fake()->create('id_front.jpg', 500);

        $response = $this->actingAs($user)->post(route('user.kyc.store'), [
            'document_type' => 'aadhaar',
            'document_number' => '123456789012',
            'full_name' => 'John Doe',
            'dob' => '1995-05-15',
            'bank_name' => 'HDFC Bank',
            'account_number' => '9876543210',
            'ifsc_code' => 'HDFC0001234',
            'account_holder' => 'John Doe',
            'upi_id' => 'johndoe@upi',
            'front_image' => $frontFile,
        ]);

        $response->assertRedirect(route('user.kyc.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'kyc_status' => 'pending',
        ]);

        $this->assertDatabaseHas('kycs', [
            'user_id' => $user->id,
            'document_type' => 'aadhaar',
            'document_number' => '123456789012',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_kyc_and_unverified_user_can_then_withdraw(): void
    {
        $admin = User::create([
            'role_id' => $this->adminRoleId,
            'name' => 'Super Admin',
            'email' => 'admin_kyc@zivopay.com',
            'referral_code' => 'ZIVO-ADM01',
        ]);

        $user = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Jane Smith',
            'email' => 'janesmith@zivopay.com',
            'referral_code' => 'ZIVO-KYC02',
            'kyc_status' => 'pending',
            'earning_wallet' => 5000.00,
        ]);

        $kyc = Kyc::create([
            'user_id' => $user->id,
            'document_type' => 'pan',
            'document_number' => 'ABCDE1234F',
            'full_name' => 'Jane Smith',
            'bank_name' => 'ICICI Bank',
            'account_number' => '1122334455',
            'ifsc_code' => 'ICIC0001122',
            'account_holder' => 'Jane Smith',
            'front_image' => 'uploads/kyc/test.jpg',
            'status' => 'pending',
        ]);

        // Admin approves KYC
        $approveResponse = $this->actingAs($admin)->post(route('admin.kyc.approve', $kyc->id));
        $approveResponse->assertSessionHas('success');

        $user = $user->fresh();
        $this->assertEquals('approved', $user->kyc_status);
        $this->assertEquals('approved', $kyc->fresh()->status);

        // Approved user places withdrawal
        $wthResponse = $this->actingAs($user)->post(route('user.withdrawal.store'), [
            'from_wallet' => 'earning_wallet',
            'amount' => 1000,
            'payment_method' => 'Bank Transfer',
            'account_details' => 'Bank: ICICI, A/C: 1122334455, IFSC: ICIC0001122',
        ]);

        $wthResponse->assertRedirect(route('user.withdrawal.index'));
        $wthResponse->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $user->id,
            'amount' => 1000,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_reject_kyc_with_reason(): void
    {
        $admin = User::create([
            'role_id' => $this->adminRoleId,
            'name' => 'Super Admin 2',
            'email' => 'admin_kyc2@zivopay.com',
            'referral_code' => 'ZIVO-ADM02',
        ]);

        $user = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Test Member',
            'email' => 'testmember@zivopay.com',
            'referral_code' => 'ZIVO-KYC03',
            'kyc_status' => 'pending',
        ]);

        $kyc = Kyc::create([
            'user_id' => $user->id,
            'document_type' => 'aadhaar',
            'document_number' => '999988887777',
            'full_name' => 'Test Member',
            'front_image' => 'uploads/kyc/blur.jpg',
            'status' => 'pending',
        ]);

        $rejectResponse = $this->actingAs($admin)->post(route('admin.kyc.reject', $kyc->id), [
            'rejection_reason' => 'Document image is too blurry. Please upload a clear photo.',
        ]);

        $rejectResponse->assertSessionHas('success');

        $this->assertEquals('rejected', $user->fresh()->kyc_status);
        $this->assertEquals('rejected', $kyc->fresh()->status);
        $this->assertEquals('Document image is too blurry. Please upload a clear photo.', $kyc->fresh()->rejection_reason);
    }
}
