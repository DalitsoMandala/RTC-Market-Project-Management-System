<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LowerCaseController extends Controller
{
    //
    

    public function setup(){
        foreach(User::all() as $user){
            $user->email = strtolower($user->email);
            $user->save();
        }

        return response()->json([
            'message' => 'Emails converted to lowercase'
        ]);
    }
}
