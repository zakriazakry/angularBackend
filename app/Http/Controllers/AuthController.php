<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first()
            ], 400);
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
            'roles' => UserRole::where('user_id', $user->id)->pluck('role_id')->toArray()
        ]);
    }

    public function signup(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'phone' => $request->phone,
                'Last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image'=> $this->generateImage()
            ]);

            if (!$user->addRole(2)) {
                return response()->json([
                    'status' => true,
                    'msg' => "User creation failed! - permission"
                ], 400);
            }
            $user->save();

            DB::commit();
            return response()->json([
                'status' => true,
                'msg' => "User created successfully!"
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'msg' => "User creation failed! Error: " . $e->getMessage()
            ], 500);
        }
    }

    private function generateImage()
    {
        $images  = [
            'https://i.pinimg.com/736x/92/b4/e7/92b4e7c57de1b5e1e8c5e883fd915450.jpg',
            'https://i.pinimg.com/564x/1b/a2/e6/1ba2e6d1d4874546c70c91f1024e17fb.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png',
            'https://i.pinimg.com/474x/d8/70/20/d87020c70b0bf5eec4918874fa7d0f9f.jpg',
            'https://mir-s3-cdn-cf.behance.net/project_modules/disp/64623a33850498.56ba69ac2a6f7.png'
        ];
        return $images[random_int(0, count($images) - 1)];
    }
}
