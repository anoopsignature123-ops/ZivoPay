<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationGeneralSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_detail_id', 'product_type_id', 'vehicle_type', 'registration_number', 'property_number', 'brand', 'model', 'variant',
        'manufacturing_year', 'registration_year', 'engine_number', 'chassis_number', 'fuel_type', 'vehicle_usage', 'seating_capacity',
        'load_capacity', 'rto_location', 'permit_number', 'fitness_certificate_expiry', 'previous_policy_number', 'previous_insurer',
        'previous_policy_start_date', 'renewal_date', 'claim_history', 'ncb_percentage', 'existing_policy_type', 'insurance_type',
        'property_type', 'ownership_type', 'property_address', 'construction_year', 'built_up_area', 'construction_type', 'number_of_floors',
        'building_value', 'contents_value', 'occupancy_status', 'fire_safety_details', 'travel_type', 'destination_country', 'trip_type',
        'travel_start_date', 'travel_end_date', 'travellers_count', 'passport_number', 'visa_type', 'travel_purpose', 'occupation',
        'employment_type', 'risk_category', 'disability_coverage', 'accidental_death_coverage', 'additional_remarks',
    ];

    protected function casts(): array
    {
        return [
            'fitness_certificate_expiry' => 'date',
            'previous_policy_start_date' => 'date',
            'renewal_date' => 'date',
            'travel_start_date' => 'date',
            'travel_end_date' => 'date',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_detail_id');
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }
}
