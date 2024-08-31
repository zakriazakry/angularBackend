<?php

namespace App\Http\Controllers;

use App\Models\roles;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    function getAllRoles()
    {
        return roles::all();
    }

    public function getUserRoles($user_id)
    {
        $userRoles = UserRole::with('role')
            ->where('user_id', $user_id)
            ->get()
            ->pluck('role');

        $allRoles = $this->getAllRoles();
        foreach ($allRoles as $role) {
            $role->active = $userRoles->contains($role);
        }
        return $allRoles;
    }

    function setUserRole(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'rolesIDs' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first(),
            ], 422);
        }
    
        $rolesIDs = explode(',', $request->input('rolesIDs'));
    
        foreach ($rolesIDs as $roleID) {
            if (!is_numeric($roleID)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Invalid role ID: ' . $roleID,
                ], 422);
            }
        }
    
        $roles = roles::whereIn('id', $rolesIDs)->get();
    
        if ($roles->isEmpty()) {
            return response()->json([
                'status' => false,
                'msg' => 'No valid roles found for the provided IDs',
            ], 404);
        }
    
        $user = User::find($user_id);
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'msg' => 'User not found',
            ], 404);
        }
    
        UserRole::where('user_id', $user_id)->delete();
    
        foreach ($roles as $role) {
            $user->addRole($role->id);
        }
    
        return response()->json([
            'status' => true,
            'msg' => 'Roles assigned successfully',
        ], 200);
    }
    

    // addUserRole...
    // removeUserRole...
}
