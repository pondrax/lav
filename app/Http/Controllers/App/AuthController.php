<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\AuthLoginRequest;
use App\Http\Requests\App\AuthRegisterRequest;
use App\Models\User;

/**
 * @group #Auth Bearer
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * Handle authentication attempt
     */
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->validated();

        if (! auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }
        $user = User::where('email', $request->get('email'))->firstOrFail();

        return response()->json([
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout
     *
     * Handle authentication attempt
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'successfully logout',
        ]);
    }

    /**
     * Register
     *
     * Handle new registration
     */
    public function register(AuthRegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return response()->json([
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }
}
