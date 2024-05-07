<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;


class UserRepository implements UserRepositoryContract
{
    //login function
    public function login($request)
    {
        $credentials = $request->only('email', 'password');

        // Check if the credentials are valid
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Define the custom claims to be included in the token payload
            $customClaims = ['userID' => $user->id];

            // Generate a JWT token with custom payload
            $token = JWTAuth::claims($customClaims)->fromUser($user);

            //return user details and token
            return response()->json(['user' => $user, 'token' => $token], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    //register function
    public function register($request)
    {
        // Validate the request data with custom error messages
        $validatedData = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phoneNumber' => 'required | unique:users',
            'address1' => 'required',
            'address2' => 'required',
        ], [

            // Custom error messages
            'name.required' => 'The name field is required.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'The username has already been taken.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'phoneNumber.required' => 'The phone number field is required.',
            'phoneNumber.unique' => 'The phone number has already been taken.',
            'address1.required' => 'The address line 1 field is required.',
            'address2.required' => 'The address line 2 field is required.',
        ]);

        // Hash the password before storing it
        $validatedData['password'] = Hash::make($validatedData['password']);
        // Create the user
        $user = User::create($validatedData);

        // Check if user creation was successful
        if ($user) {
            return response()->json(['message' => 'User created successfully'], 201);
        }
        return response()->json(['errorMessage' => 'User could not be created'], 400);
    }


    //logout function
    public function logout($request)
    {
        // Retrieve the JWT token from the request headers
        $token = $request->bearerToken();

        // Revoke the token
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
            // Clear the user's session
            Auth::logout();

            return response()->json(['message' => 'User logged out'], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    //check if user exist in the database (for adding user to project purpose)
    public function checkUserExist($request)
    {
        //get user email from request
        $email = $request->email;

        //get user details
        $user = User::where('email', $email)->first();

        //return user details
        if ($user) {
            return response()->json(['userID' => $user->id], 200);
        }
        return response()->json(['message' => 'User not found'], 200);
    }
}
