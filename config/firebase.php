<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options for Firebase Cloud
    | Messaging (FCM) push notifications.
    |
    */

    // Firebase Project ID
    'project_id' => env('FIREBASE_PROJECT_ID', 'celes-movie-booking-ticket'),

    // Path to Firebase credentials JSON file (relative to base_path)
    'credentials_path' => env('FIREBASE_CREDENTIALS_PATH', 'celes.json'),

    /*
    |--------------------------------------------------------------------------
    | Topic Definitions
    |--------------------------------------------------------------------------
    |
    | Define all FCM topics here for easy management and tracking.
    | Topics are used for sending notifications to groups of users.
    |
    */
    'topics' => [
        // Main topic for all users - used for broadcast notifications
        'all_users' => 'celes_all_users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Defaults
    |--------------------------------------------------------------------------
    |
    | Default values for notifications.
    |
    */
    'defaults' => [
        'sound' => 'default',
        'badge' => 1,
        'icon' => 'ic_notification',
        'color' => '#FF6B6B',
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
    ],
];
