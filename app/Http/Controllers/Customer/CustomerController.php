<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;


class CustomerController extends Controller
{
    public function edit($id) 
    {   
        $user = User::find($id);
        return view('customer.edit', compact('user'));
    }

    public function update(Request $request, $id) 
    {   
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            //'email' => 'required|max:255',
            'store_address' => 'required|max:255',
        ]);
         
         try {
            
           $user = User::find($id);
            $user_name = $request->first_name .' '.$request->last_name;
            $user->username = $user_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->address = $request->store_address;
            $user->save();
            
            return redirect()->route('customer-dashboard')->with('success', 'Customer  profile updated successfully');   
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Customer profile not updated');
        }
    }
    
     public function changePassword(Request $request, $id)
    {
         return view('customer.change');   
    }

    public function updatePassword(Request $request, $id)
    {
        $validated = $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => 'required',
            'password_confirmation' => ['same:new_password'],
        ]);

        try {
           User::find($id)
            ->update(['password'=> Hash::make($request->new_password)]);
            return redirect()->route('customer-dashboard')->with('success', 'Customer  password change successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Customer password not change');
        }
    }
}
