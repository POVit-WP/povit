<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CloseFriendController extends Controller
{
    public function index(){
        $user = Auth::user();
        $closeFriends = $user->closefriends();
        // dd($closeFriends);
        return view('main.closefriend', ['closeFriends' => $closeFriends]);
    }
}
