<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Sadece yönetici (admin) rolündeki kullanıcıların geçmesine izin verir.
     *
     * Kimliği doğrulanmış kullanıcının `role` alanı 'admin' değilse 403 döner.
     * Bu middleware her zaman `auth:sanctum`'dan sonra kullanılmalıdır.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Bu işlem için yönetici yetkisi gerekir.'], 403);
        }

        return $next($request);
    }
}
