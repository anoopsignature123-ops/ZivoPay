<?php

return [
    /*
    |--------------------------------------------------------------------------
    | A1Topup Recharge API Configuration
    |--------------------------------------------------------------------------
    */

    'username' => env('A1TOPUP_USERNAME', '500011'),
    'password' => env('A1TOPUP_PASSWORD', '123'),
    'base_url' => env('A1TOPUP_BASE_URL', 'https://business.a1topup.com'),

    /*
    |--------------------------------------------------------------------------
    | Optional 3rd Party HLR / MNP Operator Lookup API Settings
    |--------------------------------------------------------------------------
    */
    'hlr_api_url' => env('HLR_API_URL', null),
    'hlr_api_key' => env('HLR_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Master Operators List Grouped By Category
    |--------------------------------------------------------------------------
    */
    'operators' => [
        'mobile' => [
            ['name' => 'Airtel', 'code' => 'A', 'type' => 'Mobile', 'icon' => 'airtel'],
            ['name' => 'Vodafone', 'code' => 'V', 'type' => 'Mobile', 'icon' => 'vi'],
            ['name' => 'BSNL - TOPUP', 'code' => 'BT', 'type' => 'Mobile', 'icon' => 'bsnl'],
            ['name' => 'RELIANCE - JIO', 'code' => 'RC', 'type' => 'Mobile', 'icon' => 'jio'],
            ['name' => 'Idea', 'code' => 'I', 'type' => 'Mobile', 'icon' => 'idea'],
            ['name' => 'BSNL - STV', 'code' => 'BR', 'type' => 'Mobile', 'icon' => 'bsnl'],
            ['name' => 'BSNL Recharge', 'code' => 'BS', 'type' => 'Mobile', 'icon' => 'bsnl'],
            ['name' => 'MTNL - Recharge', 'code' => 'MTR', 'type' => 'Mobile', 'icon' => 'mtnl'],
            ['name' => 'MTNL - TOPUP', 'code' => 'MTT', 'type' => 'Mobile', 'icon' => 'mtnl'],
            ['name' => 'Idea Lapu', 'code' => 'RI', 'type' => 'Mobile', 'icon' => 'idea'],
        ],
        'dth' => [
            ['name' => 'Airtel Digital DTH TV', 'code' => 'ATV', 'type' => 'DTH', 'icon' => 'airtel_dth'],
            ['name' => 'SUNDIRECT DTH TV', 'code' => 'STV', 'type' => 'DTH', 'icon' => 'sun_direct'],
            ['name' => 'TATASKY DTH TV', 'code' => 'TTV', 'type' => 'DTH', 'icon' => 'tata_play'],
            ['name' => 'VIDEOCON DTH TV', 'code' => 'VTV', 'type' => 'DTH', 'icon' => 'videocon_d2h'],
            ['name' => 'DISH TV', 'code' => 'DTV', 'type' => 'DTH', 'icon' => 'dish_tv'],
        ],
        'postpaid' => [
            ['name' => 'Airtel Postpaid', 'code' => 'PAT', 'type' => 'PostPaid'],
            ['name' => 'Idea Postpaid', 'code' => 'IP', 'type' => 'PostPaid'],
            ['name' => 'Vodafone Postpaid', 'code' => 'VP', 'type' => 'PostPaid'],
            ['name' => 'Tata Docomo Postpaid', 'code' => 'DP', 'type' => 'PostPaid'],
            ['name' => 'BSNL Postpaid', 'code' => 'BP', 'type' => 'PostPaid'],
            ['name' => 'Bsnl Landline', 'code' => 'LBS', 'type' => 'PostPaid'],
            ['name' => 'MTNL Delhi Landline', 'code' => 'LMT', 'type' => 'PostPaid'],
            ['name' => 'Airtel Landline', 'code' => 'LAT', 'type' => 'PostPaid'],
            ['name' => 'JIO POSTPAID', 'code' => 'JPP', 'type' => 'PostPaid'],
        ],
        'electricity' => [
            ['name' => 'North Bihar Electricity', 'code' => 'NBE', 'type' => 'Electricity'],
            ['name' => 'JBVNL - JHARKHAND', 'code' => 'JBVNL', 'type' => 'Electricity'],
            ['name' => 'Assam Power Distribution Company Ltd (RAPDR)', 'code' => 'APDCLR', 'type' => 'Electricity'],
            ['name' => 'Mangalore Electricity Supply Co. Ltd (MESCOM) - RAPDR', 'code' => 'MESCOMR', 'type' => 'Electricity'],
            ['name' => 'APDCL (Non-RAPDR) - ASSAM', 'code' => 'APDCLN', 'type' => 'Electricity'],
            ['name' => 'Mangalore Electricity Supply Co. Ltd (Non) - RAPDR', 'code' => 'MESCOMNR', 'type' => 'Electricity'],
            ['name' => 'BSES Rajdhani Power Limited - Delhi', 'code' => 'BSES', 'type' => 'Electricity'],
            ['name' => 'BSES Yamuna Power Limited - Delhi', 'code' => 'BSESY', 'type' => 'Electricity'],
            ['name' => 'Tata Power Delhi Limited - Delhi', 'code' => 'TPD', 'type' => 'Electricity'],
            ['name' => 'Tata Power - MUMBAI', 'code' => 'TPDM', 'type' => 'Electricity'],
            ['name' => 'Hubli Electricity Supply Company Ltd. (HESCOM)', 'code' => 'HESCOM', 'type' => 'Electricity'],
            ['name' => 'South Bihar Electricity', 'code' => 'SBE', 'type' => 'Electricity'],
            ['name' => 'BEST Mumbai', 'code' => 'BEST', 'type' => 'Electricity'],
            ['name' => 'Ajmer Vidyut Vitran Nigam - RAJASTHAN', 'code' => 'AJV', 'type' => 'Electricity'],
            ['name' => 'Bangalore Electricity Supply Company', 'code' => 'BESCOM', 'type' => 'Electricity'],
            ['name' => 'CESC - WEST BENGAL', 'code' => 'CESC', 'type' => 'Electricity'],
            ['name' => 'Jaipur Vidyut Vitran Nigam - RAJASTHAN', 'code' => 'JVV', 'type' => 'Electricity'],
            ['name' => 'Jodhpur Vidyut Vitran Nigam - RAJASTHAN', 'code' => 'JDVV', 'type' => 'Electricity'],
            ['name' => 'MP Madhaya Kshetra Vidyut Vitaran -Urban', 'code' => 'MKV', 'type' => 'Electricity'],
            ['name' => 'MSEDC - MAHARASHTRA', 'code' => 'MSEDC', 'type' => 'Electricity'],
            ['name' => 'Noida Power - NOIDA', 'code' => 'NP', 'type' => 'Electricity'],
            ['name' => 'Paschim Kshetra Vitaran - MADHYA PRADESH', 'code' => 'PKV', 'type' => 'Electricity'],
            ['name' => 'Southern Power - ANDHRA PRADESH', 'code' => 'SPA', 'type' => 'Electricity'],
            ['name' => 'Southern Power - TELANGANA', 'code' => 'SPT', 'type' => 'Electricity'],
            ['name' => 'Torrent Power agra', 'code' => 'TRP', 'type' => 'Electricity'],
            ['name' => 'Central Power Distribution Company of Andhra Pradesh Ltd', 'code' => 'APCPDCL', 'type' => 'Electricity'],
            ['name' => 'Department of Power Arunachal Pradesh', 'code' => 'ARPDOP', 'type' => 'Electricity'],
            ['name' => 'Western Electricity supply co. Of orissa ltd.', 'code' => 'WESCO', 'type' => 'Electricity'],
            ['name' => 'Paschim Gujarat Vij Company Ltd', 'code' => 'PGVCL', 'type' => 'Electricity'],
            ['name' => 'BharatpurelectricityServicesLtd', 'code' => 'BHES', 'type' => 'Electricity'],
            ['name' => 'Muzaffarpur Vidyut Vitran', 'code' => 'MVV', 'type' => 'Electricity'],
            ['name' => 'Madhya Gujarat Vij Company Ltd', 'code' => 'MGVCL', 'type' => 'Electricity'],
            ['name' => 'MEPDCL - MEGHALAYA', 'code' => 'MEPDCL', 'type' => 'Electricity'],
            ['name' => 'KEDL - KOTA', 'code' => 'KEDL', 'type' => 'Electricity'],
            ['name' => 'Dakshin Gujarat Vij Company Ltd', 'code' => 'DGVCL', 'type' => 'Electricity'],
            ['name' => 'WBSEDCL - WEST BENGAL', 'code' => 'WBSEDCL', 'type' => 'Electricity'],
            ['name' => 'SNDL Power - NAGPUR', 'code' => 'SNDL', 'type' => 'Electricity'],
            ['name' => 'Bikaner Electricity Supply Limited', 'code' => 'BESL', 'type' => 'Electricity'],
            ['name' => 'India Power - WEST BENGAL', 'code' => 'IPWB', 'type' => 'Electricity'],
            ['name' => 'BrihanMumbaiElectricitySupplyandTransportUndertaking', 'code' => 'BMESTU', 'type' => 'Electricity'],
            ['name' => 'APEPDCL - ANDHRA PRADESH', 'code' => 'APEPDCL', 'type' => 'Electricity'],
            ['name' => 'TNEB - TAMIL NADU', 'code' => 'TNEB', 'type' => 'Electricity'],
            ['name' => 'UPPCL (URBAN) - UTTAR PRADESH', 'code' => 'UPPCLU', 'type' => 'Electricity'],
            ['name' => 'Uttar Pradesh Power Corporation Limited(Rular)', 'code' => 'UPPCLR', 'type' => 'Electricity'],
            ['name' => 'DakshinHaryanaBijliVitranNigam', 'code' => 'DHBVN', 'type' => 'Electricity'],
            ['name' => 'TSNPDCL Telangana northern power', 'code' => 'TSNPDCL', 'type' => 'Electricity'],
            ['name' => 'DNHPowerDistributionCompanyLimited', 'code' => 'DDCL', 'type' => 'Electricity'],
            ['name' => 'GulbargaElectricitySupplyCompanyLimited', 'code' => 'GESCL', 'type' => 'Electricity'],
            ['name' => 'IndiaPowerCorporationLimited', 'code' => 'IPCL', 'type' => 'Electricity'],
            ['name' => 'JamshedpurUtilitiesandServicesCompanyLimited', 'code' => 'JUSCL', 'type' => 'Electricity'],
            ['name' => 'Chhattisgarh State Power Distribution Company Ltd. (CSPDCL)', 'code' => 'CSPDCL', 'type' => 'Electricity'],
            ['name' => 'Goa Electricity', 'code' => 'GOAELC', 'type' => 'Electricity'],
            ['name' => 'UttarGujarat Vij Company Ltd', 'code' => 'UGVCL', 'type' => 'Electricity'],
            ['name' => 'Torrent Power Surat', 'code' => 'TORRENTSUR', 'type' => 'Electricity'],
            ['name' => 'Torrent Power Ahemdabad', 'code' => 'TORRENTAHM', 'type' => 'Electricity'],
            ['name' => 'Gift Power Company Limited', 'code' => 'GPCL', 'type' => 'Electricity'],
            ['name' => 'Himachal Pradesh State Electricity Board Ltd', 'code' => 'HPSEBL', 'type' => 'Electricity'],
            ['name' => 'Jammu & Kashmir power Development department', 'code' => 'JKPDD', 'type' => 'Electricity'],
            ['name' => 'Chamundeshwari Electricity Supply Corporation Ltd. (Cesc,Mysore)', 'code' => 'CESCOM', 'type' => 'Electricity'],
            ['name' => 'NorthDelhiPowerLimited', 'code' => 'NDPL', 'type' => 'Electricity'],
            ['name' => 'MUNICIPALCORPORATIONOFGURUGRAM', 'code' => 'MCG', 'type' => 'Electricity'],
            ['name' => 'Punjab State Power Corporation Limted', 'code' => 'PSPCL', 'type' => 'Electricity'],
            ['name' => 'TripuraStateElectricityCorporationLtd', 'code' => 'TSECL', 'type' => 'Electricity'],
            ['name' => 'UttarHaryanaBijliVitranNigam', 'code' => 'UHBV', 'type' => 'Electricity'],
            ['name' => 'UttarakhandPowerCorporationLimited', 'code' => 'UKPCL', 'type' => 'Electricity'],
            ['name' => 'Kerala State Electricity Board Ltd.', 'code' => 'KSEB', 'type' => 'Electricity'],
            ['name' => 'kannan devan hills power', 'code' => 'KDHPCPL', 'type' => 'Electricity'],
            ['name' => 'Lakshadweep Electricity Department', 'code' => 'LED', 'type' => 'Electricity'],
            ['name' => 'MP Poorv Kshetra Vidyut Vitaran - Jabalpur', 'code' => 'MPPKVVCLPU', 'type' => 'Electricity'],
            ['name' => 'Madhya Pradesh Madhya Kshetra Vidyut Vitaran-RURAL', 'code' => 'MPPKVVCLMR', 'type' => 'Electricity'],
            ['name' => 'Madhya Pradesh Poorv Kshetra Vidyut Vitaran-URBAN', 'code' => 'MPPKVVCL', 'type' => 'Electricity'],
            ['name' => 'Reliance Energy', 'code' => 'RELIANCE', 'type' => 'Electricity'],
            ['name' => 'Torrent Power SHIL', 'code' => 'TORRENTSHI', 'type' => 'Electricity'],
            ['name' => 'Torrent Power Bhivandi', 'code' => 'TORRENTBHI', 'type' => 'Electricity'],
            ['name' => 'Adani power', 'code' => 'AEML', 'type' => 'Electricity'],
            ['name' => 'Manipur State Power Distribution Company Limited (Prepaid)', 'code' => 'MSPCLPR', 'type' => 'Electricity'],
            ['name' => 'Power & Electricity Department - Mizoram', 'code' => 'MPED', 'type' => 'Electricity'],
            ['name' => 'Department of Power, Nagaland', 'code' => 'NDOP', 'type' => 'Electricity'],
            ['name' => 'New Delhi Municipal Council (NDMC) - Electricity', 'code' => 'NDMC', 'type' => 'Electricity'],
            ['name' => 'NESCO Odisha', 'code' => 'NESCO', 'type' => 'Electricity'],
            ['name' => 'SOUTHCO Odisha', 'code' => 'SOUTHCO', 'type' => 'Electricity'],
            ['name' => 'TP central odisha distribution limited', 'code' => 'TPCODL', 'type' => 'Electricity'],
            ['name' => 'Government of Puducherry Electricity Department', 'code' => 'PGPED', 'type' => 'Electricity'],
            ['name' => 'TP Ajmer Distribution Ltd', 'code' => 'TPADL', 'type' => 'Electricity'],
            ['name' => 'Sikkim Power Rural', 'code' => 'SPR', 'type' => 'Electricity'],
            ['name' => 'Sikkim Power Urban', 'code' => 'SPU', 'type' => 'Electricity'],
            ['name' => 'Kanpur Electricity Supply Company', 'code' => 'KESCO', 'type' => 'Electricity'],
            ['name' => 'Torrent Power Dahej', 'code' => 'TORRENTDAH', 'type' => 'Electricity'],
            ['name' => 'Madhyanchal Vidyut Vitran Nigam Limited', 'code' => 'MVVNL', 'type' => 'Electricity'],
        ],
        'gas' => [
            ['name' => 'Mahanagar Gas', 'code' => 'MG', 'type' => 'Gas'],
            ['name' => 'Adani Gas', 'code' => 'AG', 'type' => 'Gas'],
            ['name' => 'Gujarat Gas', 'code' => 'GG', 'type' => 'Gas'],
            ['name' => 'Indraprastha Gas', 'code' => 'IG', 'type' => 'Gas'],
            ['name' => 'Hindustan Petroleum Corporation Ltd', 'code' => 'HPCLGC', 'type' => 'Gas'],
        ],
        'insurance' => [
            ['name' => 'ICICI Prudential Insurance', 'code' => 'ICP', 'type' => 'Insurance'],
            ['name' => 'Tata AIA Insurance', 'code' => 'TAI', 'type' => 'Insurance'],
        ],
        'fastag' => [
            ['name' => 'Jammu And Kashmir Bank Fastag', 'code' => 'JKF', 'type' => 'FASTag'],
            ['name' => 'Kotak Mahindra Bank - Fastag', 'code' => 'KMF', 'type' => 'FASTag'],
            ['name' => 'Indusind Bank Fastag', 'code' => 'INDF', 'type' => 'FASTag'],
            ['name' => 'Indian Highways Management Company Ltd Fastag', 'code' => 'IHMCF', 'type' => 'FASTag'],
            ['name' => 'Idfc First Bank- Fastag', 'code' => 'IFF', 'type' => 'FASTag'],
            ['name' => 'Icici Bank Fastag', 'code' => 'ICF', 'type' => 'FASTag'],
            ['name' => 'Hdfc Bank - Fastag', 'code' => 'HDF', 'type' => 'FASTag'],
            ['name' => 'Equitas Fastag Recharge', 'code' => 'EFF', 'type' => 'FASTag'],
            ['name' => 'Bank Of Baroda - Fastag', 'code' => 'BBF', 'type' => 'FASTag'],
            ['name' => 'Axis Bank Fastag', 'code' => 'AXF', 'type' => 'FASTag'],
            ['name' => 'Federal Bank - Fastag', 'code' => 'FDF', 'type' => 'FASTag'],
            ['name' => 'Paytm Payments Bank Fastag', 'code' => 'PTF', 'type' => 'FASTag'],
            ['name' => 'Airtel Payments Bank', 'code' => 'APB', 'type' => 'FASTag'],
            ['name' => 'Idbi Bank Fastag', 'code' => 'IBF', 'type' => 'FASTag'],
            ['name' => 'Sbi Bank Fastag', 'code' => 'SBF', 'type' => 'FASTag'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Categorized Mobile & DTH Recharge Plans (Google Pay / PhonePe Style)
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'RC' => [ // Jio
            [
                'amount' => 239,
                'validity' => '28 Days',
                'description' => '1.5 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 299,
                'validity' => '28 Days',
                'description' => '2.0 GB/day · Unlimited Calls + 100 SMS/day + Disney+ Hotstar',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 666,
                'validity' => '84 Days',
                'description' => '1.5 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 719,
                'validity' => '84 Days',
                'description' => '2.0 GB/day · Unlimited Calls + 100 SMS/day + OTT Pack',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 19,
                'validity' => 'Active Plan',
                'description' => '1 GB High Speed Data Pack',
                'category' => 'Data Packs',
            ],
            [
                'amount' => 29,
                'validity' => 'Active Plan',
                'description' => '2 GB High Speed Data Booster',
                'category' => 'Data Packs',
            ],
            [
                'amount' => 148,
                'validity' => '28 Days',
                'description' => '10 GB Data + JioCinema Premium / OTT Access',
                'category' => 'Entertainment / OTT',
            ],
            [
                'amount' => 2999,
                'validity' => '365 Days',
                'description' => '2.5 GB/day · Unlimited Calls + 100 SMS/day (Annual Plan)',
                'category' => 'Annual Plans',
            ],
        ],
        'A' => [ // Airtel
            [
                'amount' => 265,
                'validity' => '28 Days',
                'description' => '1.0 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 299,
                'validity' => '28 Days',
                'description' => '1.5 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 719,
                'validity' => '84 Days',
                'description' => '1.5 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 49,
                'validity' => '1 Day',
                'description' => '6 GB Data Pack',
                'category' => 'Data Packs',
            ],
            [
                'amount' => 2999,
                'validity' => '365 Days',
                'description' => '2.0 GB/day · Unlimited Calls + 100 SMS/day (Annual Plan)',
                'category' => 'Annual Plans',
            ],
        ],
        'V' => [ // Vi
            [
                'amount' => 299,
                'validity' => '28 Days',
                'description' => '1.5 GB/day · Unlimited Calls + Weekend Rollover Data',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 719,
                'validity' => '84 Days',
                'description' => '1.5 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 2899,
                'validity' => '365 Days',
                'description' => '1.5 GB/day · Unlimited Calls (Annual Plan)',
                'category' => 'Annual Plans',
            ],
        ],
        'BT' => [ // BSNL
            [
                'amount' => 199,
                'validity' => '30 Days',
                'description' => '2.0 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 599,
                'validity' => '84 Days',
                'description' => '3.0 GB/day · Unlimited Calls + 100 SMS/day',
                'category' => 'Recommended Plans',
            ],
            [
                'amount' => 1999,
                'validity' => '365 Days',
                'description' => '600 GB Total Data + Unlimited Calls (Annual Plan)',
                'category' => 'Annual Plans',
            ],
        ],
    ],

    'dth_quick_amounts' => [150, 250, 500, 1000],

    /*
    |--------------------------------------------------------------------------
    | Master Circle Codes List
    |--------------------------------------------------------------------------
    */
    'circles' => [
        ['name' => 'Punjab', 'code' => '1'],
        ['name' => 'West Bengal', 'code' => '2'],
        ['name' => 'Mumbai', 'code' => '3'],
        ['name' => 'Maharashtra', 'code' => '4'],
        ['name' => 'Delhi', 'code' => '5'],
        ['name' => 'Kolkata', 'code' => '6'],
        ['name' => 'Chennai', 'code' => '7'],
        ['name' => 'Tamil Nadu', 'code' => '8'],
        ['name' => 'Karnataka', 'code' => '9'],
        ['name' => 'Uttar Pradesh East', 'code' => '10'],
        ['name' => 'Uttar Pradesh West', 'code' => '11'],
        ['name' => 'Gujarat', 'code' => '12'],
        ['name' => 'Andhra Pradesh', 'code' => '13'],
        ['name' => 'Kerala', 'code' => '14'],
        ['name' => 'Madhya Pradesh', 'code' => '16'],
        ['name' => 'Bihar', 'code' => '17'],
        ['name' => 'Rajasthan', 'code' => '18'],
        ['name' => 'Haryana', 'code' => '20'],
        ['name' => 'Himachal Pradesh', 'code' => '21'],
        ['name' => 'Jharkhand', 'code' => '22'],
        ['name' => 'Orissa', 'code' => '23'],
        ['name' => 'Assam', 'code' => '24'],
        ['name' => 'Jammu and Kashmir', 'code' => '25'],
        ['name' => 'North East', 'code' => '26'],
        ['name' => 'Chhattisgarh', 'code' => '27'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile App Dashboard Service Statuses (active vs coming_soon)
    |--------------------------------------------------------------------------
    */
    'service_statuses' => [
        'mobile_recharge' => 'active',
        'dth_recharge' => 'active',
        'fastag_recharge' => 'active',
        'gaming_topup' => 'coming_soon',
        'credit_card_bill' => 'coming_soon',
        'electricity_bill' => 'active',
        'gas_bill' => 'active',
        'broadband_bill' => 'coming_soon',
        'train_booking' => 'coming_soon',
        'flight_booking' => 'coming_soon',
        'bus_ticket' => 'coming_soon',
        'ott_subscriptions' => 'coming_soon',
        'insurance' => 'active',
        'money_transfer' => 'coming_soon',
    ],
];
