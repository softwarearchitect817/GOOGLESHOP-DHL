<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Customer;
use Hash;
use Auth;
class ResetPasswordController extends Controller
{
    /**
     * This will do all the heavy lifting
     * for resetting the password.
     */
   // use ResetsPasswords;

     /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';

    /**
     * Only guests for "admin" guard are allowed except
     * for logout.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the reset password form.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function otp(){
    	abort_if(!Session::has('customer_info'),404);
        return view('auth.customer.passwords.otp');
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker(){
        return Password::broker('customers');
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard(){
        return Auth::guard('customers');
    }

    public function resetPassword(Request $request)
    {
    	abort_if(!Session::has('customer_info'),404);

    	$data=Session::get('customer_info');

        if($request->otp_num == $data['otp']){
        	
        	$validated = $request->validate([
        		'password' => 'required|string|min:8|confirmed',
        	]);

        	$user=Customer::findorFail($data['id']);
        	$user->password=Hash::make($request->password);
        	$user->save();

        	Auth::guard('customer')->loginUsingId($data['id']);
        	Session::forget('customer_info');
        	return redirect('/user/dashboard');
        	
        }
        return back()->with('error','Please enter the correct otp to continue');
        
    }
}
