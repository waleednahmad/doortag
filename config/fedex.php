<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FedEx API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for FedEx API integration including credentials and
    | default settings for shipping services.
    |
    */

    'client_id' => env('FEDEX_CLIENT_ID', 'l7f6de69eda6c243fa95e9f43a444e5ad3'),
    'client_secret' => env('FEDEX_CLIENT_SECRET', '7db8633fbb044c98b890d6e08341893c'),
    'account_number' => env('FEDEX_ACCOUNT_NUMBER', '510087020'),
    
    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    */
    
    'sandbox_mode' => env('FEDEX_SANDBOX_MODE', true),
    
    'urls' => [
        'sandbox' => [
            'auth' => 'https://apis-sandbox.fedex.com/oauth/token',
            'rates' => 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes',
        ],
        'production' => [
            'auth' => 'https://apis.fedex.com/oauth/token',
            'rates' => 'https://apis.fedex.com/rate/v1/rates/quotes',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    
    'defaults' => [
        'pickup_type' => 'DROPOFF_AT_FEDEX_LOCATION',
        'service_type' => 'FEDEX_2_DAY',
        'rate_request_type' => ['PREFERRED'],
        'currency' => 'USD',
        'weight_unit' => 'LB',
        'dimension_unit' => 'IN',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Service Types
    |--------------------------------------------------------------------------
    */
    
    'service_types' => [
        'FEDEX_2_DAY' => 'FedEx 2Day',
        'FEDEX_2_DAY_AM' => 'FedEx 2Day AM',
        'FEDEX_EXPRESS_SAVER' => 'FedEx Express Saver',
        'FEDEX_GROUND' => 'FedEx Ground',
        'GROUND_HOME_DELIVERY' => 'FedEx Home Delivery',
        'FIRST_OVERNIGHT' => 'FedEx First Overnight',
        'PRIORITY_OVERNIGHT' => 'FedEx Priority Overnight',
        'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Pickup Types
    |--------------------------------------------------------------------------
    */
    
    'pickup_types' => [
        'DROPOFF_AT_FEDEX_LOCATION' => 'Drop off at FedEx Location',
        'CONTACT_FEDEX_TO_SCHEDULE' => 'Schedule FedEx Pickup',
        'USE_SCHEDULED_PICKUP' => 'Use Scheduled Pickup',
    ],
];