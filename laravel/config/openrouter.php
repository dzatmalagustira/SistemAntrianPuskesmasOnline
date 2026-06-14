<?php

return [
    'api_key' => env('OPENROUTER_API_KEY'),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    'model' => env('OPENROUTER_MODEL', 'openrouter/auto'),
    'site_url' => env('APP_URL', 'https://antrianpuskesmas.fwh.is'),
    'site_name' => env('APP_NAME', 'PuskesmasAntrian'),
    'timeout' => (int) env('OPENROUTER_TIMEOUT', 30),
];
