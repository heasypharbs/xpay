<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Register User
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        try {
            // Generate a unique GUID (UUID)
            $guid = Str::uuid()->toString();

            // Generate a standard user_id (e.g., numeric ID prefixed with 'CP')
            $latestUser = User::latest('id')->first();
            $nextId = $latestUser ? $latestUser->id + 1 : 1;
            $userId = 'CP' . str_pad($nextId, 6, '0', STR_PAD_LEFT); // Example: CP000001

            // Create a new user
            $user = User::create([
                'user_id' => $userId,
                'guid' => $guid,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'userId' => $user->user_id,
               
            ], 201);

        } catch (\Exception $e) {
            Log::error("User Registration Failed: " . $e->getMessage());
            return response()->json(['message' => 'Registration failed, please try again'], 500);
        }
    }

    // Login User
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => Auth::user()
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not create token'], 500);
        }
    }
}
