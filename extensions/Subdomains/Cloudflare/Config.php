<?php

use App\Models\Extension;

$extension_model = Extension::class;

try {
    $email = $extension_model::where([['extension', 'Cloudflare'], ['key', 'email']])->value('value');
    $api_key = $extension_model::where([['extension', 'Cloudflare'], ['key', 'api_key']])->value('value');
} catch (\Throwable $err) {
    $email = null;
    $api_key = null;
}

return [
    'email' => $email,
    'api_key' => $api_key,
];
