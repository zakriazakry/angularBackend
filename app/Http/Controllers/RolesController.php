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
            'roles' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 422);
        }
        $rolesIDs = $request->input('rolesIDs');
        $roles = roles::whereIn('id', explode(',', $rolesIDs))->get();
        $user = User::find($user_id);
        // delelte all roles
        UserRole::where('user_id', $user_id)->delete();
        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        foreach ($roles as $value) {
            $user->addRole($value->id);
        }
        return response()->json([
            'success' => 'Roles assigned successfully'
        ], 200);
    }

    // addUserRole...
    // removeUserRole...
}
