<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KycController extends Controller
{
    /**
     * Display User KYC status & submission form.
     */
    public function index()
    {
        $user = Auth::user();
        $kyc = $user->kyc;

        return view('user.kyc.index', compact('user', 'kyc'));
    }

    /**
     * Store or update User KYC submission.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $existingKyc = $user->kyc;

        $rules = [
            'document_type' => 'required|string|in:aadhaar,pan,passport,voter_id',
            'document_number' => 'required|string|min:4|max:50',
            'full_name' => 'required|string|min:2|max:100',
            'dob' => 'nullable|date',
            'bank_name' => 'required|string|min:2|max:100',
            'account_number' => 'required|string|min:4|max:50',
            'ifsc_code' => 'required|string|min:4|max:20',
            'account_holder' => 'required|string|min:2|max:100',
            'upi_id' => 'nullable|string|max:100',
            'front_image' => $existingKyc ? 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120' : 'required|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'back_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ];

        $request->validate($rules, [
            'document_type.required' => 'Please select a valid document type.',
            'document_number.required' => 'Document number is required.',
            'full_name.required' => 'Full name as per document is required.',
            'bank_name.required' => 'Bank name is required for withdrawal processing.',
            'account_number.required' => 'Bank account number is required.',
            'ifsc_code.required' => 'IFSC Code is required.',
            'account_holder.required' => 'Account holder name is required.',
            'front_image.required' => 'Please upload the front image of your ID document.',
        ]);

        $uploadPath = public_path('uploads/kyc');
        if (! file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $frontImagePath = $existingKyc ? $existingKyc->front_image : '';
        if ($request->hasFile('front_image')) {
            $frontFile = $request->file('front_image');
            $frontFileName = 'kyc_front_'.$user->id.'_'.time().'_'.Str::random(6).'.'.$frontFile->getClientOriginalExtension();
            $frontFile->move($uploadPath, $frontFileName);
            $frontImagePath = 'uploads/kyc/'.$frontFileName;
        }

        $backImagePath = $existingKyc ? $existingKyc->back_image : null;
        if ($request->hasFile('back_image')) {
            $backFile = $request->file('back_image');
            $backFileName = 'kyc_back_'.$user->id.'_'.time().'_'.Str::random(6).'.'.$backFile->getClientOriginalExtension();
            $backFile->move($uploadPath, $backFileName);
            $backImagePath = 'uploads/kyc/'.$backFileName;
        }

        $kycData = [
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'document_number' => trim((string) $request->document_number),
            'full_name' => trim((string) $request->full_name),
            'dob' => $request->dob,
            'bank_name' => trim((string) $request->bank_name),
            'account_number' => trim((string) $request->account_number),
            'ifsc_code' => strtoupper(trim((string) $request->ifsc_code)),
            'account_holder' => trim((string) $request->account_holder),
            'upi_id' => $request->upi_id ? trim((string) $request->upi_id) : null,
            'front_image' => $frontImagePath,
            'back_image' => $backImagePath,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_at' => null,
        ];

        Kyc::updateOrCreate(
            ['user_id' => $user->id],
            $kycData
        );

        $user->update([
            'kyc_status' => 'pending',
            'wallet_address' => $request->upi_id ?: $request->account_number,
        ]);

        return redirect()->route('user.kyc.index')->with('success', 'KYC documents and banking details submitted successfully! Your verification status is now pending admin approval.');
    }
}
