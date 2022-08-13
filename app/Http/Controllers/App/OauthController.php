<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

/**
 * @group #Auth Social
 */
class OauthController extends Controller
{
    protected $allowedProviders = [
        'google',
    ];

    /**
     * Redirect to Provider
     *
     * Handle oauth authentication attempt
     *
     * @urlParam provider string required Provider authentication. Example: google
     */
    public function redirect($provider)
    {
        if (! in_array($provider, $this->allowedProviders)) {
            return response()->json([
                'message' => 'Providers not allowed',
            ], 403);
        }
        // $provider = 'google';
        return Socialite::driver($provider)
      // ->redirectUrl('http://localhost:5000/service/auth/google/callback')
      ->redirect();
    }

    /**
     * Handle Callback Provider
     *
     * Handle oauth authentication attempt
     *
     * @urlParam provider string required Provider authentication. Example: google
     */
    public function callback($provider)
    {
        if (request()->get('state') === null) {
            return response()->json([
                'message' => 'Token Invalid',
            ], 403);
        }
        if (! in_array($provider, $this->allowedProviders)) {
            return response()->json([
                'message' => 'Providers not allowed',
            ], 403);
        }
        $user = Socialite::driver($provider)->user();
        if (! str()->endsWith($user->email, '@mojokertokota.go.id')) {
            return response()->view('oauth_failed', [
                'message' => 'Your email must ends with @mojokertokota.go.id',
                'provider' => $provider,
            ]);
        }
        $newUser = User::firstOrCreate([
            'email' => $user->getEmail(),
        ], [
            'email_verified_at' => now(),
            'name' => $user->getName(),
            // 'avatar' => $user->getAvatar(),
        ]);

        $newUser->update(['avatar' => $user->getAvatar()]);
        $urlQuery = http_build_query([
            'access_token' => $newUser->createToken('auth_token')->plainTextToken,
            'access_user' => json_encode($newUser->only(['name', 'email', 'avatar', 'jabatan', 'nip', 'unit_kerja'])),
        ]);

        return redirect()->to(env('APP_URL').'/oauth?'.$urlQuery);
        // return response()->view('oauth_success',  [
        //   'message'       => "Welcome $newUser->name",
        //   'user'          => $newUser,
        //   'access_token'  => $newUser->createToken('auth_token')->plainTextToken,
        //   'token_type'    => 'Bearer',
        // ]);
    }
}
