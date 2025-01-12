<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "status" => false,
                "errors" => [
                    "email" => [
                        "email already registered"
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            "status" => true,
            "data" => new UserResource($user)
        ], 201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "status" => false,
                "errors" => [
                    "message" => [
                        "email or password wrong"
                    ]
                ]
            ], 400));
        }

        $user->auth_token = Str::uuid()->toString();
        $user->save();

        return response()->json([
            "status" => true,
            "data" => new UserResource($user)
        ], 200);
    }

    public function profile(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            "status" => true,
            "data" => new UserResource($user)
        ], 200);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        return response()->json([
            "status" => true,
            "data" => new UserResource($user)
        ], 200);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->auth_token = null;
        $user->save();

        return response()->json([
            "status" => true,
            "data" => true
        ], 200);
    }
}
