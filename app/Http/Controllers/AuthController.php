<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:admin,vendor,customer'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email, password, or account type.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role === 'vendor') {
            return redirect()->route('vendor.products.index');
        } elseif ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function apiMe()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function apiLogout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }
}
