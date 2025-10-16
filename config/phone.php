<?php

return [
    // Default region used by libphonenumber when parsing numbers without a leading +
    // Set via env PHONE_DEFAULT_REGION (e.g., 'PH' for Philippines)
    'default_region' => env('PHONE_DEFAULT_REGION', 'PH'),
];
