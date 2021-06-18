<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VendorDetail;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Mail;

class VendorsController extends Controller
{
    public function index() 
    {
        $users = User::where('role_id', '=', 2)->paginate(15);
        
        return view('admin.vendors.index', compact('users'));
    }

     public function list(Request $request) 
    {
       
        if ($request->ajax()) {
            //$users = User::where('role_id', '=', 3)->paginate(15);
            $data =  User::with('details')->where('role_id', '=', 2)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="'.route('admin-vendors-edit', $row->id).'" class="edit btn btn-success btn-sm">Edit</a> <a href="'.route('admin-vendors-delete', $row->id).'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
    }

    public function create() 
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'address' => 'required|max:255',
        ]);

         

        try {
            DB::beginTransaction();

            $user_name = $request->first_name .' '.$request->last_name;
            $password = Hash::make(Str::random(10));


            $user = User::create([
                'username' => $user_name,
                'first_name' =>$request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' =>  $password,
                'role_id' => 2,
                'address' => $request->address,
            ]);

            $details = new VendorDetail();
            $details->user_id = $user->id;
            $details->store_name = $request->store_name;
            $details->save();

            $user = $user->toArray();
            $user['password'] = $password;

            Mail::send('welcome_email', $user, function ($message) use ($user) {
                $message->to($user['email'], $user['username'], $user['password'])
                    ->subject('Welcome to MyNotePaper');
                    //->from('info@mynotepaper.com', 'MyNotePaper');
            });
            DB::commit();
            return redirect()->route('admin-vendors')->with('success', 'vendor create successfully');  

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error','vendor not created');
        }
    }

    public function edit($id) 
    {
         $user = User::with('details')->find($id);
        return view('admin.vendors.edit', compact('user'));
    }

    public function update(Request $request, $id) 
    {
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            //'email' => 'required|max:255|unique:users',
            'address' => 'required|max:255',
        ]);

        
        try {
            $user = User::with('details')->find($id);
            $user_name = $request->first_name .' '.$request->last_name;
            //$password = Hash::make(Str::random(10));
            $user->username = $user_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            //$user->email = $request->email;
            $user->address = $request->address;
           
            $user->details->user_id = $user->id;
            $user->details->store_name = $request->store_name;
            $user->push();

            return redirect()->route('admin-vendors')->with('success', 'venodrs updated successfully');  
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','venodrs not updated');
        }
    }

    public function destroy($id) 
    {
        $user = User::find($id)->delete();
        return response()->json(['status' => true, 'message' => 'Vendor deleted successfully'], 200);
    }
}
