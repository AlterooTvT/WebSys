<?php
// Status Constants
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');
define('STATUS_PAID', 'paid');
define('STATUS_COMPLETED', 'completed');

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CLIENT', 'client');

// Service Types
define('SERVICE_PHOTOBOOTH', 'photobooth');
define('SERVICE_360BOOTH', '360booth');
define('SERVICE_MAGAZINEBOOTH', 'magazinebooth');
define('SERVICE_PARTYCART', 'partycart');

// Payment Types
define('PAYMENT_FULL', 'full');
define('PAYMENT_DOWN', 'down_payment');

// Time Slots
define('TIME_SLOTS', [
    '09:00:00' => '9:00 AM',
    '13:00:00' => '1:00 PM',
    '17:00:00' => '5:00 PM'
]);

// Event Types
define('EVENT_TYPES', [
    'wedding' => 'Wedding',
    'birthday' => 'Birthday',
    'corporate' => 'Corporate Event',
    'debut' => 'Debut',
    'other' => 'Other'
]);