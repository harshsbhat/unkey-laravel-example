<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthWithUnkey
{
    protected $unkeyApiUrl = 'https://api.unkey.dev/v1/keys.verifyKey';

    public function handle(Request $request, Closure $next, string $permission)
    {
        $key = $request->header('Authorization');
        if (!$key || !str_starts_with($key, 'Bearer ')) {
            return response('Unauthorized', 401);
        }

        $key = explode(' ', $key)[1];

        $payload = [
            'apiId' => env('UNKEY_API_ID'),
            'key' => $key,
            'authorization' => ['permissions' => $permission],
        ];


        $response = $this->sendCurlRequest($payload);

        if (isset($response['error'])) {
            return response('Unauthorized', 401);
        }

        if (!$response['valid']) {
            return response("Unauthorized", 401);
        }

        return $next($request);
    }

    protected function sendCurlRequest(array $payload)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->unkeyApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('UNKEY_ROOT_KEY'),
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ['error' => $err];
        }

        return json_decode($response, true);
    }
}
