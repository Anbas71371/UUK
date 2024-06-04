<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function getUser(Request $request)
    {
        $user = Auth::user(); // Get the currently logged-in user
        return response()->json(['user' => $user], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Retrieve user by name
        $user = User::where('nama', $credentials['nama'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            // Clear error message if authentication fails
            return response()->json(['status' => false, 'message' => 'Invalid name or password'], 401);
        }

        // Check if the user is approved
      

        // Authentication successful
        $token = $user->createToken('MyApp')->accessToken;

        // Log info about the type of user who logged in
        \Log::info('User with name '.$user->nama.' successfully logged in. User type: '.$user->type);

        return response()->json([
            'status' => true,
            'type' => $user->type,
            'message' => 'Welcome, you have successfully logged in as '.$user->type,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        // Ensure the user is authenticated before logging out
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Log out by revoking the user's token
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
