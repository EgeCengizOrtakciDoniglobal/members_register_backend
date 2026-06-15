<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberController extends Controller
{
    /**
     * Tüm üyeleri listele.
     */
    public function index(): JsonResource
    {
        return JsonResource::collection(Member::all());
    }

    /**
     * Yeni üye ekle.
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create($request->validated());

        return response()->json($member, 201);
    }

    /**
     * Tek üye getir.
     */
    public function show(Member $member): Member
    {
        return $member;
    }

    /**
     * Üye güncelle.
     */
    public function update(UpdateMemberRequest $request, Member $member): Member
    {
        $member->update($request->validated());

        return $member;
    }

    /**
     * Üye sil.
     */
    public function destroy(Member $member): JsonResponse
    {
        $member->delete();

        return response()->json(['message' => 'Üye silindi.']);
    }
}
