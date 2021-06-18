<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\VendorDetail;

class RegisterController extends Controller
{
    public function index() 
    {   
        return view('vendor.register');
    }

    public function store(Request $request) 
    {   
        
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|confirmed',
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
            $user = User::create([
                'username' => $user_name,
                'first_name' =>$request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
                'role_id' => 2,
                'address' => $request->store_address,
            ]);

            
            $VendorDetail = new VendorDetail();

            $VendorDetail->user_id = $user->id;
            $VendorDetail->store_description = $request->store_description;

            $VendorDetail->contact_number = $request->contact_number;
            $VendorDetail->profile_picture_name = $profile_name;
            $VendorDetail->profile_picture_path = $profile_new_name;
            $VendorDetail->banner_picture_name = $file_name;
            $VendorDetail->banner_picture_path = $file_new_name;
            $VendorDetail->phone_number = $file_new_name;
            $VendorDetail->save();

            return redirect()->back()->with('success', 'vendor registerd successfully');   
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Vendorr not registerd');
        }
    }
}
