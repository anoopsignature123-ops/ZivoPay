<?php


namespace Database\Seeders;

use App\Models\InsuranceCategory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InsuranceDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {

            /*
            |--------------------------------------------------------------------------
            | 1. Insurance Categories
            |--------------------------------------------------------------------------
            */

            $categoryDefinitions = [
                'life-insurance' => [
                    'name' => 'Life Insurance',
                    'description' => 'Financial protection, savings and long-term life cover.',
                    'order' => 10,
                ],
                'health-insurance' => [
                    'name' => 'Health Insurance',
                    'description' => 'Medical expenses, hospitalization and healthcare protection.',
                    'order' => 20,
                ],
                'general-insurance' => [
                    'name' => 'General Insurance',
                    'description' => 'Vehicle, travel, home and other non-life insurance.',
                    'order' => 30,
                ],
            ];

            $categories = [];

            foreach ($categoryDefinitions as $slug => $data) {
                $categories[$slug] = InsuranceCategory::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $data['name'],
                        'description' => $data['description'],
                        'display_order' => $data['order'],
                        'is_active' => true,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Insurance Companies
            |--------------------------------------------------------------------------
            | Demo company names are used here. Replace with actual insurer names
            | before using these records for real customer applications.
            */

            $companyDefinitions = [
                // Life Insurance
                'life-insurance' => [
                    'zivo-life-assurance' => [
                        'Zivo Life Assurance',
                        'Life insurance and financial protection provider.',
                    ],
                    'secure-life-insurance' => [
                        'Secure Life Insurance',
                        'Term insurance and savings plans provider.',
                    ],
                ],

                // Health Insurance
                'health-insurance' => [
                    'zivo-health-assurance' => [
                        'Zivo Health Assurance',
                        'Individual and family health insurance provider.',
                    ],
                    'care-health-insurance' => [
                        'Care Health Insurance',
                        'Health and medical insurance provider.',
                    ],
                ],

                // General Insurance
                'general-insurance' => [
                    'zivo-general-assurance' => [
                        'Zivo General Assurance',
                        'Motor, travel and property insurance provider.',
                    ],
                    'secure-general-insurance' => [
                        'Secure General Insurance',
                        'General insurance and personal protection provider.',
                    ],
                ],
            ];

            $companies = [];

            foreach ($companyDefinitions as $categorySlug => $definitions) {
                foreach ($definitions as $slug => [$name, $description]) {
                    $companies[$slug] = InsuranceCompany::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'insurance_category_id' => $categories[$categorySlug]->id,
                            'name' => $name,
                            'description' => $description,
                            'display_order' => count($companies) * 10 + 10,
                            'is_active' => 'yes',
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Insurance Products
            |--------------------------------------------------------------------------
            | Each company has its own products.
            */

            $productDefinitions = [

                // LIFE INSURANCE
                'zivo-life-assurance' => [
                    'term-life-insurance' => [
                        'Term Life Insurance',
                        'Financial protection for the nominee in case of the policyholder’s death.',
                    ],
                    'whole-life-insurance' => [
                        'Whole Life Insurance',
                        'Long-term life coverage for the policyholder.',
                    ],
                    'endowment-plan' => [
                        'Endowment Plan',
                        'Life cover combined with a savings benefit.',
                    ],
                    'money-back-plan' => [
                        'Money Back Plan',
                        'Life insurance with periodic survival benefits.',
                    ],
                    'ulip-plan' => [
                        'Unit Linked Insurance Plan (ULIP)',
                        'Life cover combined with market-linked investment.',
                    ],
                    'child-insurance-plan' => [
                        'Child Insurance Plan',
                        'Financial planning for a child’s future needs.',
                    ],
                    'retirement-pension-plan' => [
                        'Retirement Pension Plan',
                        'Long-term retirement and pension planning.',
                    ],
                ],

                'secure-life-insurance' => [
                    'pure-term-plan' => [
                        'Pure Term Plan',
                        'Affordable term-based life protection.',
                    ],
                    'limited-pay-term-plan' => [
                        'Limited Pay Term Plan',
                        'Life coverage with a limited premium payment period.',
                    ],
                    'guaranteed-savings-plan' => [
                        'Guaranteed Savings Plan',
                        'Life protection with guaranteed savings benefits.',
                    ],
                    'joint-life-plan' => [
                        'Joint Life Insurance',
                        'Life coverage for two individuals under one policy.',
                    ],
                ],

                // HEALTH INSURANCE
                'zivo-health-assurance' => [
                    'individual-health-plan' => [
                        'Individual Health Insurance',
                        'Medical and hospitalization coverage for one person.',
                    ],
                    'family-floater-plan' => [
                        'Family Floater Health Insurance',
                        'Shared health coverage for family members.',
                    ],
                    'senior-citizen-health-plan' => [
                        'Senior Citizen Health Insurance',
                        'Healthcare protection designed for senior citizens.',
                    ],
                    'critical-illness-plan' => [
                        'Critical Illness Insurance',
                        'Financial protection against specified critical illnesses.',
                    ],
                    'personal-accident-health-plan' => [
                        'Personal Accident Insurance',
                        'Coverage for accidental injury and disability.',
                    ],
                    'top-up-health-plan' => [
                        'Health Insurance Top-Up',
                        'Additional medical coverage above a specified deductible.',
                    ],
                ],

                'care-health-insurance' => [
                    'care-supreme-plan' => [
                        'Comprehensive Health Plan',
                        'Comprehensive hospitalization and medical coverage.',
                    ],
                    'maternity-health-plan' => [
                        'Maternity Health Insurance',
                        'Coverage for eligible maternity-related medical expenses.',
                    ],
                    'group-health-plan' => [
                        'Group Health Insurance',
                        'Medical coverage for employees and eligible groups.',
                    ],
                    'disease-specific-plan' => [
                        'Disease Specific Health Plan',
                        'Coverage designed for specified medical conditions.',
                    ],
                ],

                // GENERAL INSURANCE
                'zivo-general-assurance' => [
                    'private-car-insurance' => [
                        'Private Car Insurance',
                        'Comprehensive and third-party car insurance coverage.',
                    ],
                    'two-wheeler-insurance' => [
                        'Two Wheeler Insurance',
                        'Insurance protection for motorcycles and scooters.',
                    ],
                    'commercial-vehicle-insurance' => [
                        'Commercial Vehicle Insurance',
                        'Coverage for eligible commercial vehicles.',
                    ],
                    'travel-insurance-plan' => [
                        'Travel Insurance',
                        'Medical emergencies, baggage and travel-related coverage.',
                    ],
                    'home-property-insurance' => [
                        'Home Insurance',
                        'Protection for residential property and eligible contents.',
                    ],
                    'marine-insurance' => [
                        'Marine Insurance',
                        'Coverage for eligible goods and transit risks.',
                    ],
                    'shop-insurance' => [
                        'Shop Insurance',
                        'Protection for eligible shop property and business assets.',
                    ],
                ],

                'secure-general-insurance' => [
                    'third-party-motor-insurance' => [
                        'Third Party Motor Insurance',
                        'Mandatory third-party liability cover for eligible vehicles.',
                    ],
                    'own-damage-motor-insurance' => [
                        'Own Damage Motor Insurance',
                        'Coverage for eligible damage to insured vehicles.',
                    ],
                    'international-travel-insurance' => [
                        'International Travel Insurance',
                        'Protection for eligible international travel risks.',
                    ],
                    'domestic-travel-insurance' => [
                        'Domestic Travel Insurance',
                        'Coverage for eligible domestic travel risks.',
                    ],
                    'fire-insurance' => [
                        'Fire Insurance',
                        'Protection against specified fire-related property losses.',
                    ],
                    'personal-liability-insurance' => [
                        'Personal Liability Insurance',
                        'Coverage for specified personal liability risks.',
                    ],
                ],
            ];

            $products = [];

            foreach ($productDefinitions as $companySlug => $definitions) {
                $company = $companies[$companySlug];

                foreach ($definitions as $slug => [$name, $description]) {
                    $products[$slug] = InsuranceProduct::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'insurance_category_id' => $company->insurance_category_id,
                            'insurance_company_id' => $company->id,
                            'name' => $name,
                            'description' => $description,
                            'display_order' => count($products) * 10 + 10,
                            'is_active' => 'yes',
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 4. General Insurance Product Types
            |--------------------------------------------------------------------------
            | Product types are created only for General Insurance products
            | where a further classification is required.
            */

            $generalProductTypes = [
                'private-car-insurance' => [
                    'private-car' => ['Private Car', 'Insurance for privately owned cars.'],
                    'electric-car' => ['Electric Car', 'Insurance for electric cars.'],
                ],

                'two-wheeler-insurance' => [
                    'motorcycle' => ['Motorcycle', 'Insurance for motorcycles.'],
                    'scooter' => ['Scooter', 'Insurance for scooters.'],
                ],

                'commercial-vehicle-insurance' => [
                    'goods-carrier' => ['Goods Carrier', 'Insurance for goods transport vehicles.'],
                    'passenger-vehicle' => ['Passenger Vehicle', 'Insurance for passenger transport vehicles.'],
                    'taxi-cab' => ['Taxi / Cab', 'Insurance for taxis and cabs.'],
                ],

                'travel-insurance-plan' => [
                    'domestic-travel' => ['Domestic Travel', 'Travel within the country.'],
                    'international-travel' => ['International Travel', 'Travel outside the country.'],
                    'student-travel' => ['Student Travel', 'Insurance for eligible student travel.'],
                ],

                'home-property-insurance' => [
                    'building-insurance' => ['Building Insurance', 'Coverage for residential buildings.'],
                    'home-contents' => ['Home Contents Insurance', 'Coverage for eligible household contents.'],
                ],

                'marine-insurance' => [
                    'marine-cargo' => ['Marine Cargo', 'Coverage for goods during transit.'],
                    'marine-hull' => ['Marine Hull', 'Coverage for eligible vessels.'],
                ],

                'shop-insurance' => [
                    'small-shop' => ['Small Shop', 'Insurance for eligible small retail shops.'],
                    'commercial-shop' => ['Commercial Shop', 'Insurance for commercial shop properties.'],
                ],
            ];

            foreach ($generalProductTypes as $productSlug => $types) {
                $product = $products[$productSlug];

                foreach ($types as $slug => [$name, $description]) {
                    ProductType::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'insurance_category_id' => $product->insurance_category_id,
                            'insurance_company_id' => $product->insurance_company_id,
                            'insurance_product_id' => $product->id,
                            'name' => $name,
                            'description' => $description,
                            'display_order' => count($types) * 10,
                            'is_active' => 'yes',
                        ]
                    );
                }
            }
        });
    }
}