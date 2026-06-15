<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\JsonResponse;

class IntegrationController extends Controller
{
    /**
     * WordPress entegrasyonu için güncel üye listesi.
     *
     * Yalnızca status = active olan üyeler JSON olarak döner. Mevcut `members`
     * tablosundan salt-okunur bir sorgudur; veri değiştirmez.
     */
    public function members(): JsonResponse
    {
        $members = Member::where('status', 'active')->get();

        return response()->json(['data' => $members]);
    }
}
