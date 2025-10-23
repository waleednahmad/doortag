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
        'service_type' => 'FEDEX_GROUND',
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
        // Express Services
        'FIRST_OVERNIGHT' => 'FedEx First Overnight®',
        'PRIORITY_OVERNIGHT' => 'FedEx Priority Overnight®',
        'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight®',
        'FEDEX_2_DAY' => 'FedEx 2Day®',
        'FEDEX_2_DAY_AM' => 'FedEx 2Day® AM',
        'FEDEX_EXPRESS_SAVER' => 'FedEx Express Saver®',
        
        // Ground Services
        'FEDEX_GROUND' => 'FedEx Ground®',
        'GROUND_HOME_DELIVERY' => 'FedEx Home Delivery®',
        'SMART_POST' => 'FedEx Ground® Economy',
        
        // International Services
        'FEDEX_INTERNATIONAL_PRIORITY_EXPRESS' => 'FedEx International Priority® Express',
        'INTERNATIONAL_FIRST' => 'FedEx International First®',
        'FEDEX_INTERNATIONAL_PRIORITY' => 'FedEx International Priority®',
        'INTERNATIONAL_ECONOMY' => 'FedEx International Economy®',
        'FEDEX_INTERNATIONAL_CONNECT_PLUS' => 'FedEx International Connect Plus®',
        
        // Freight Services
        'FEDEX_FREIGHT_PRIORITY' => 'FedEx LTL Freight Priority',
        'FEDEX_FREIGHT_ECONOMY' => 'FedEx LTL Freight Economy',
        'FEDEX_FIRST_FREIGHT' => 'FedEx First Overnight® Freight',
        'FEDEX_1_DAY_FREIGHT' => 'FedEx 1Day® Freight',
        'FEDEX_2_DAY_FREIGHT' => 'FedEx 2Day® Freight',
        'FEDEX_3_DAY_FREIGHT' => 'FedEx 3Day® Freight',
        'INTERNATIONAL_PRIORITY_FREIGHT' => 'FedEx International Priority® Freight',
        'INTERNATIONAL_ECONOMY_FREIGHT' => 'FedEx International Economy® Freight',
        'FEDEX_INTERNATIONAL_DEFERRED_FREIGHT' => 'FedEx® International Deferred Freight',
        
        // Distribution Services
        'INTERNATIONAL_PRIORITY_DISTRIBUTION' => 'FedEx International Priority DirectDistribution®',
        'INTERNATIONAL_DISTRIBUTION_FREIGHT' => 'FedEx International Priority DirectDistribution® Freight',
        'INTL_GROUND_DISTRIBUTION' => 'International Ground® Distribution (IGD)',
        
        // Same Day Services
        'SAME_DAY' => 'FedEx SameDay®',
        'SAME_DAY_CITY' => 'FedEx SameDay® City',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Pickup Types
    |--------------------------------------------------------------------------
    */
    
    'pickup_types' => [
        // Ship API Pickup Types
        'CONTACT_FEDEX_TO_SCHEDULE' => 'Contact FedEx to Schedule Pickup',
        'DROPOFF_AT_FEDEX_LOCATION' => 'Drop off at FedEx Location',
        'USE_SCHEDULED_PICKUP' => 'Use Scheduled Pickup',
        'TAG' => 'Tag Pickup (Return Shipping Label)',
        
        // Pickup API Pickup Types
        // 'ON_CALL' => 'On Call Pickup',
        // 'PACKAGE_RETURN_PROGRAM' => 'FedEx Ground Package Returns Program',
        // 'REGULAR_STOP' => 'Regular Pickup Stop',
    ],
];