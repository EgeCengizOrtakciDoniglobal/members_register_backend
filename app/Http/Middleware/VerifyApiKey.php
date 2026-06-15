<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * WordPress eklentisi / harici sistemler için statik API Key doğrulaması.
     *
     * İstemci anahtarı `X-API-Key` header'ı ile gönderir; bu değer .env'deki
     * INTEGRATION_API_KEY ile zamanlama-güvenli (hash_equals) karşılaştırılır.
     * Eşleşmezse 401 döner.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = env('INTEGRATION_API_KEY');
        $provided = $request->header('X-API-Key');

        if (empty($expected) || ! is_string($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Geçersiz veya eksik API anahtarı.'], 401);
        }

        return $next($request);
    }
}
