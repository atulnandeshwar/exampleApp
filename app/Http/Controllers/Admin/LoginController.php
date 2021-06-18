<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index() 
    {   
        if(Auth::check()){
            return view('admin.home');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request) 
    {   
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            
            if (Auth::attempt($credentials)) {
                
                $request->session()->regenerate();

                return redirect()->route('admin-dashboard');

            } else {
                return redirect()->back()->with('error','Admin credential does not match');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Admin credential does not match');
        }
    }    

    public function logout(Request $request)
    {
        
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}
