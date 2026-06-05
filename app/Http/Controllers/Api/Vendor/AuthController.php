<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'nullable|string|max:30',
            'address'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'vendor',
        ]);

        Vendor::create([
            'name'       => $user->name . ' Store',
            'owner_name' => $user->name,
            'email'      => $user->email,
            'phone'      => $request->phone ?? '0000000000',
            'address'    => $request->address ?? 'Default Vendor Address',
            'status'     => 'active',
            'user_id'    => $user->id,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status'  => 'success',
            'message' => 'Vendor registered successfully',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid email or password',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not create token. Please try again.',
            ], 500);
        }

        $user = auth('api')->user();

        if ($user->role !== 'vendor') {
            JWTAuth::invalidate($token);
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. Only vendors can access this panel.',
            ], 403);
        }

        return response()->json([
            'status'     => 'success',
            'message'    => 'Login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status'  => 'success',
                'message' => 'Logged out successfully',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to logout',
            ], 500);
        }
    }

    public function me()
    {
        $user = auth('api')->user();

        return response()->json([
            'status' => 'success',
            'user'   => $user,
        ]);
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'token'  => $newToken,
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not refresh token',
            ], 401);
        }
    }
}
