<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     // Get User Profile
     public function profile()
     {
         $user = Auth::user();
         return response()->json([
             'first_name' => $user->first_name,
             'last_name' => $user->last_name,
             'email' => $user->email,
             'virtual_account_number' => $user->virtual_account_number
         ]);
     }
 
     // Update User Profile
     public function updateProfile(Request $request)
     {
         $request->validate([
             'first_name' => 'sometimes|string',
             'last_name' => 'sometimes|string',
         ]);
 
         $user = Auth::user();
         $updated = $user->update($request->only('first_name', 'last_name'));
 
         if (!$updated) {
             return response()->json(['message' => 'Profile update failed'], 500);
         }
 
         return response()->json(['message' => 'Profile updated successfully']);
     }
}
