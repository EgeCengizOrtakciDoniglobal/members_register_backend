<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
    /**
     * Tüm kullanıcıları listele.
     */
    public function index(): JsonResource
    {
        return JsonResource::collection(User::all());
    }

    /**
     * Yeni kullanıcı ekle. (Parola model üzerinde otomatik hash'lenir.)
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json($user, 201);
    }

    /**
     * Tek kullanıcı getir.
     */
    public function show(User $user): User
    {
        return $user;
    }

    /**
     * Kullanıcı güncelle.
     */
    public function update(UpdateUserRequest $request, User $user): User
    {
        $user->update($request->validated());

        return $user;
    }

    /**
     * Kullanıcı sil.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'Kullanıcı silindi.']);
    }
}
