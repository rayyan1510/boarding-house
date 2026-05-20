<?php

return [
    'serverKey' => env('MIDTRANS_SERVER_KEY', ''),
    'isProduction' => env('MIDTRANS_IS_PRODUCTION', false),
    'isSanitize' => env('MIDTRANS_IS_SANITIZE', true),
    'is3ds' => env('MIDTRANS_IS_3DS', true),
];
