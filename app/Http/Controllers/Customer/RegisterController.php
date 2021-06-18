<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function index() 
    {   
        return view('customer.register');
    }

     public function store(Request $request) 
    {   
        
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|confirmed',
            'address' => 'required|max:255',
        ]);

        try {

            $user_name = $request->first_name .' '.$request->last_name;

            $user = User::create([
                'username' => $user_name,
                'first_name' =>$request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
                'role_id' => 3,
                'address' => $request->address,
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error','customer not registerd');
        }
    
        return redirect()->back()->with('success', 'customer registerd successfully');   

    }
}