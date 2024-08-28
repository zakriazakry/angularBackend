<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => ['required','string','email','exists:users,email'],
            'password' => ['required','string','min:8']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first()
            ],400);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'msg' => "The email or password is incorrect!"
            ], 400);
        }
        $token =  $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'msg' => $token,
            'role' => $user->role,
        ]);
    }
    public function signup(Request $request){
        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first()
            ], 400);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'Last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
        ]);

        if ($user) {
            return response()->json([
                'status' => false,
                'msg' => "user not created!"
            ], 200);
        }
    }
}
