<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Loan\SubmitLoanApplicationRequest;
use App\Http\Resources\Api\Loan\LoanApplicationResource;
use App\Http\Resources\Api\Loan\LoanCategoryResource;
use App\Models\LoanApplication;
use App\Models\LoanCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LoanController extends Controller
{
    use ApiResponse;

    public function categories(): JsonResponse
    {
        $categories = LoanCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get([
            'id', 'name', 'slug', 'description', 'icon', 'indicative_interest_rate', 'minimum_amount', 'maximum_amount', 'maximum_tenure_years',
        ]);

        return $this->successResponse(LoanCategoryResource::collection($categories)->resolve(), $categories->isEmpty() ? 'No active loan categories are available right now.' : 'Loan categories retrieved successfully.');
    }

    public function submit(SubmitLoanApplicationRequest $request): JsonResponse
    {
        $paths = [];
        try {
            $application = DB::transaction(function () use ($request, &$paths): LoanApplication {
                $data = $request->validated();
                $category = LoanCategory::query()->findOrFail($data['category_id']);
                $application = LoanApplication::query()->create([
                    'application_number' => $this->applicationNumber(), 'user_id' => $request->user()->getAuthIdentifier(),
                    'loan_category_id' => $category->id, 'applicant_name' => $data['applicant_name'],
                    'date_of_birth' => $data['date_of_birth'], 'mobile' => $data['mobile'], 'email' => $data['email'],
                    'address' => $data['address'], 'city' => $data['city'], 'state' => $data['state'], 'pin_code' => $data['pin_code'],
                    'pan_number' => strtoupper($data['pan_number']), 'aadhaar_number' => $data['aadhaar_number'],
                    'employment_type' => $data['employment_type'], 'monthly_income' => $data['monthly_income'],
                    'employer_name' => $data['employer_name'], 'existing_monthly_emi' => $data['existing_monthly_emi'] ?? null,
                    'credit_score' => $data['credit_score'] ?? null, 'preferred_lender' => $data['preferred_lender'],
                    'loan_amount' => $data['loan_amount'], 'tenure_years' => $data['tenure_years'], 'purpose' => $data['purpose'] ?? null,
                    'specifics' => array_merge($data['specifics'] ?? [], array_filter([
                        'property_location' => $data['property_location'] ?? null,
                        'property_value' => $data['property_value'] ?? null,
                    ], fn ($value) => $value !== null)),
                    'declaration_accepted' => true, 'status' => 'submitted', 'submitted_at' => now(),
                ]);
                $application->statusHistories()->create(['old_status' => null, 'new_status' => 'submitted', 'changed_by' => $request->user()->getAuthIdentifier(), 'remarks' => 'Application submitted through mobile API.']);

                foreach (['pan', 'aadhaar', 'income_proof', 'photo'] as $type) {
                    $file = $data['documents'][$type];
                    $path = $file->store('loans/applications/'.$application->application_number, 'local');
                    if ($path === false) {
                        throw new \RuntimeException('Unable to store '.$type.' document.');
                    }
                    $paths[] = $path;
                    $application->documents()->create([
                        'document_type' => $type, 'file_path' => $path, 'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(), 'verification_status' => 'pending',
                    ]);
                }

                return $application->load('category:id,name');
            });

            return $this->successResponse([
                'application_number' => $application->application_number,
                'status' => $application->status,
                'submitted_at' => $application->submitted_at,
                'category' => ['id' => $application->category->id, 'name' => $application->category->name],
            ], 'Loan application submitted successfully.', 201);
        } catch (Throwable $exception) {
            foreach ($paths as $path) {
                Storage::disk('local')->delete($path);
            }
            Log::error('Unable to submit loan application.', ['exception' => $exception]);

            return $this->errorResponse('Unable to submit your loan application right now. Please try again.', 500);
        }
    }

    public function myApplications(Request $request): JsonResponse
    {
        $data = $request->validate(['per_page' => ['nullable', 'integer', 'min:1', 'max:50'], 'page' => ['nullable', 'integer', 'min:1']]);
        $applications = LoanApplication::query()->where('user_id', $request->user()->getAuthIdentifier())
            ->with('category:id,name')->latest('created_at')->paginate($data['per_page'] ?? 10, ['*'], 'page', $data['page'] ?? 1);

        return $this->successResponse([
            'items' => $applications->getCollection()->map(fn (LoanApplication $application) => $this->applicationSummary($application)),
            'pagination' => ['current_page' => $applications->currentPage(), 'last_page' => $applications->lastPage(), 'per_page' => $applications->perPage(), 'total' => $applications->total(), 'has_more_pages' => $applications->hasMorePages()],
        ], $applications->total() === 0 ? 'You have not submitted any loan applications yet.' : 'Your loan applications were retrieved successfully.');
    }

    public function show(Request $request): JsonResponse
    {
        $data = $request->validate(['application_id' => ['required', 'integer']]);
        $application = LoanApplication::query()->where('user_id', $request->user()->getAuthIdentifier())->whereKey($data['application_id'])
            ->with(['category:id,name', 'documents:id,loan_application_id,document_type,verification_status', 'statusHistories:id,loan_application_id,old_status,new_status,remarks,created_at'])->first();
        if ($application === null) {
            return $this->errorResponse('Loan application not found.', 404);
        }

        return $this->successResponse(LoanApplicationResource::make($application)->resolve(), 'Loan application details retrieved successfully.');
    }

    public function tracking(Request $request): JsonResponse
    {
        $data = $request->validate(['application_id' => ['required', 'integer']]);
        $application = LoanApplication::query()->where('user_id', $request->user()->getAuthIdentifier())->whereKey($data['application_id'])
            ->with('statusHistories:id,loan_application_id,old_status,new_status,remarks,created_at')->first();
        if ($application === null) {
            return $this->errorResponse('Loan application not found.', 404);
        }

        return $this->successResponse([
            'application_number' => $application->application_number, 'current_status' => $application->status,
            'history' => $application->statusHistories->map(fn ($history) => ['status' => $history->new_status, 'remarks' => $history->remarks, 'changed_at' => $history->created_at]),
        ], 'Loan application tracking retrieved successfully.');
    }

    private function applicationNumber(): string
    {
        do {
            $number = 'ZIVO-LN-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (LoanApplication::query()->where('application_number', $number)->exists());

        return $number;
    }

    /** @return array<string, mixed> */
    private function applicationSummary(LoanApplication $application): array
    {
        return ['id' => $application->id, 'application_number' => $application->application_number, 'category' => ['id' => $application->category->id, 'name' => $application->category->name], 'loan_amount' => $application->loan_amount, 'tenure_years' => $application->tenure_years, 'status' => $application->status, 'submitted_at' => $application->submitted_at];
    }
}
