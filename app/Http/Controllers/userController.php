<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\user_transactions;
use Illuminate\Support\Facades\DB;

class userController extends Controller
{
    public function index()
    {
        $users = User::all();
        foreach ($users as $user) {
            # code...
            $user->plan = "Normal";
            $user->ip = "192.168.1.21";
            $user->name = $user->first_name . ' ' . $user->Last_name;
            $user->last_login = DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->value('created_at');
        }
        return  $users;
    }
    public function show($id)
    {
        return response()->json([
            'status' => false,
            'msg'=> 'User not found!'
        ],500);
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'msg'=> 'User not found!'
            ],404);
        }
        $roleCont = new RolesController();
        return [
            'details' => $user,
            'transactions' => user_transactions::where('user_id',$id)->get(),
            'roles' => $roleCont->getUserRoles($id),
        ];
    }
}
