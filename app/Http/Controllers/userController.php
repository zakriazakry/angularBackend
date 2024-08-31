<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class userController extends Controller
{
    public function index(){
        $users = User::all();
        foreach ($users as $user) {
            # code...
            $user->plan = "Normal";
            $user->ip = "192.168.1.21";
            $user->name = $user->first_name.' '. $user->Last_name;
            $user->last_login = DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->value('created_at');
        }
        return  $users;
    }
}
