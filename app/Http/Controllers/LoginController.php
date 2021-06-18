<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected $providers = [
        'github','facebook','google','twitter'
    ];

    

    public function index() 
    {   
        return view('auth.login');
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

                if(Auth::user()->role_id === 2){
                    return redirect()->route('vendor-dashboard');
                }

                if(Auth::user()->role_id === 3){
                    return redirect()->route('customer-dashboard');
                }
                
            } else {
                return redirect()->back()->with('error','given credential does not match');
            }
        } catch (\Throwable $th) {
            
           return redirect()->back()->with('error','given credential does not match');
        }
    }

    public function logout(Request $request)
    {   

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToProvider($driver)
    {
        
        if( ! $this->isProviderAllowed($driver) ) {
            return $this->sendFailedResponse("{$driver} is not currently supported");
        }

        try {
          
            return Socialite::driver('facebook')->redirect();
            //return Socialite::driver($driver)->redirect();
        } catch (Exception $e) {
            dd('false');
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }
    }

  
    public function handleProviderCallback( $driver )
    {
        
        try {
            $user = Socialite::driver($driver)->user();
        } catch (Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }

        // check for email in returned user
        return empty( $user->email )
            ? $this->sendFailedResponse("No email id returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    protected function sendSuccessResponse()
    {
        return redirect()->intended('home');
    }

    protected function sendFailedResponse($msg = null)
    {
        //return redirect()->route('social.login')
            //->withErrors(['error' => $msg ?: 'Unable to login, try with another provider to login.']);
            return redirect()->back()->with('error','Unable to login, try with another provider to login.'.$msg);
    }

    protected function loginOrCreateAccount($providerUser, $driver)
    {
        // check for already has account
        $user = User::where('email', $providerUser->getEmail())->first();

        // if user already found
        if( $user ) {
            // update the avatar and provider that might have changed
            $user->update([
                'avatar' => $providerUser->avatar,
                'provider' => $driver,
                'provider_id' => $providerUser->id,
                'access_token' => $providerUser->token
            ]);
        } else {
            // create a new user
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'avatar' => $providerUser->getAvatar(),
                'provider' => $driver,
                'provider_id' => $providerUser->getId(),
                'access_token' => $providerUser->token,
                // user can use reset password to create a password
                'password' => ''
            ]);
        }

        // login the user
        Auth::login($user, true);

        return $this->sendSuccessResponse();
    }

    private function isProviderAllowed($driver)
    {
        //dd($driver,config()->has("services.{$driver}"));
        return in_array($driver, $this->providers); //&& config()->has("services.{$driver}");
    }
}
