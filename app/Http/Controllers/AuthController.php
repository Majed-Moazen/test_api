<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signUp(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|min:10|unique:users',
            'address' => 'nullable|string',
            'account_type' => 'required|string',
            'is_active' => 'required|boolean',
            'type' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);
        $token = $user->createToken('API Token')->plainTextToken;


        return response()->json(['message' => 'User created successfully', 'user' => $user,'token'=>$token], 200);
    }
    function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|min:10',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('phone', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('login-token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }
}
