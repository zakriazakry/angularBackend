<?php

namespace App\Http\Controllers;

use App\Models\roles;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    function getAllRole()
    {
        return roles::all();
    }
    function getUserRole($user_id)
    {
        return UserRole::with(['user', 'role'])->where("user_id", $user_id)->get();
    }
    function setUserRole(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'rolesIDs' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 422);
        }
        $rolesIDs = $request->input('rolesIDs');
        $roles = roles::whereIn('id', explode(',', $rolesIDs))->get();

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        $user->roles()->sync($rolesIDs);
        
        return response()->json([
            'success' => 'Roles assigned successfully'
        ], 200); // OK status code
    }
}
