<?php

namespace App\Http\Controllers\Customer;
use Auth;
use Password;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\Customer;
use Session;
use App\Mail\Sendotp;
use Illuminate\Support\Facades\Mail;
class ForgotPasswordController extends Controller
{
	use SendsPasswordResetEmails;

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
     * Show the reset email form.
     * 
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm(){
    	// return view('auth.passwords.email',[
    	// 	'title' => 'Admin Password Reset',
    	// 	'passwordEmailRoute' => 'admin.password.email'
    	// ]);

        return view('auth.customer.passwords.email');
    }

    /**
     * password broker for admin guard.
     * 
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker(){
    	return Password::broker('customers');
    }

    /**
     * Get the guard to be used during authentication
     * after password reset.
     * 
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard(){
    	return Auth::guard('customers');
    }

    public function sendResetOtp(Request $request)
    {
       Session::forget('customer_info');
       $creator_id=domain_info('user_id');
       $customer=Customer::where([['email',$request->email],['created_by',$creator_id]])->first();
       if(empty($customer)){
        return redirect()->back()->with('error','We can\'t find a user with that email address.');
       }

       $userInfo['id']=$customer->id;
       $userInfo['otp']=rand(2000,1000000);

       Session::put('customer_info',$userInfo);
        $data = [
            'name' => $customer->name,
            'email' => $customer->email,
            'otp' => $userInfo['otp']
        ];
        Mail::to($customer->email)->send(new Sendotp($data));

       return redirect('/user/password/otp')->with('success','We sent an otp code on your mail'); 
    }

    
}
