<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VendorDetail;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    public function edit($id) 
    {   
        $user = User::with('details')->find($id);
        return view('vendor.edit', compact('user'));
    }

     public function update(Request $request, $id) 
    {   
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            //'email' => 'required|max:255',
            'store_address' => 'required|max:255',
            'store_description' => 'required',
            'contact_number' => 'required',
            'profile_picture' => 'required',
            'banner_picture' => 'required',
            'phone_number' => 'required|regex:/[0-9]{9}/'
        ]);
         
         try {
             $destinationPath = 'uploads';
            //Profile
            $profile = $request->file('profile_picture');
            $profile_name =  $profile->getClientOriginalName();
            $profile_new_name = time().'.'.$profile->extension();  
            $profile->move($destinationPath.'/profile',$profile_new_name);


            //Banner
            $banner = $request->file('banner_picture');
            $file_name =  $banner->getClientOriginalName();
            $file_new_name = time().'.'.$banner->extension();  
            $banner->move($destinationPath.'/banner',$file_new_name);
            $user_name = $request->first_name .' '.$request->last_name;

            $user = User::with('details')->find($id);

            $user->username = $user_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->address = $request->store_address;
            

            $user->details->store_description = $request->store_description;
            $user->details->user_id = $user->id;
            $user->details->contact_number = $request->contact_number;
            $user->details->profile_picture_name = $profile_name;
            $user->details->profile_picture_path = $profile_new_name;
            $user->details->banner_picture_name = $file_name;
            $user->details->banner_picture_path = $file_new_name;
            $user->details->phone_number = $file_new_name;
            $user->push();
            

           

            return redirect()->route('vendor-dashboard')->with('success', 'vendor  profile updated successfully');   
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Vendorr profile not updated');
        }
    }

    public function changePassword(Request $request, $id)
    {
         return view('vendor.change');   
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
            return redirect()->route('vendor-dashboard')->with('success', 'vendor  password change successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Vendorr password not change');
        }
        
            

    }


}
