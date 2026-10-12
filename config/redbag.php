<?php

return [
    // Configure these only in the production .env. The volunteer portal stays
    // disabled unless both values are present.
    'volunteer_email' => env('REDBAG_VOLUNTEER_EMAIL'),
    'volunteer_password_hash' => env('REDBAG_VOLUNTEER_PASSWORD_HASH'),
];
