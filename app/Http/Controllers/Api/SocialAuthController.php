<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        $request->validate(['id_token' => 'required|string']);

        // Verify token using Google Client
        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->id_token);
        if (!$payload) {
            return response()->json(['message' => 'Invalid Google token'], 401);
        }

        $user = User::updateOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'],
                'google_id' => $payload['sub'],
                'avatar' => $payload['picture'],
                'password' => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user->only('id','name','email','avatar')]);
    }

    public function facebookLogin(Request $request)
    {
        $request->validate(['access_token' => 'required|string']);

        // Verify token with Facebook Graph API
        $fb = new \Facebook\Facebook([
            'app_id' => env('FACEBOOK_CLIENT_ID'),
            'app_secret' => env('FACEBOOK_CLIENT_SECRET'),
        ]);
        $response = $fb->get('/me?fields=id,name,email,picture', $request->access_token);
        $fbUser = $response->getGraphUser();

        $user = User::updateOrCreate(
            ['email' => $fbUser['email']],
            [
                'name' => $fbUser['name'],
                'facebook_id' => $fbUser['id'],
                'avatar' => $fbUser['picture']['url'] ?? null,
                'password' => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user->only('id','name','email','avatar')]);
    }
}