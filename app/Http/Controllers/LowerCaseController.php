<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LowerCaseController extends Controller
{
    //
    

    public function setup(){
        foreach(User::all() as $user){
         
            User::find($user->id)->update([
                'email' => strtolower($user->email)
            ]);
        }

        return response()->json([
            'message' => 'Emails converted to lowercase'
        ]);
    }
}
