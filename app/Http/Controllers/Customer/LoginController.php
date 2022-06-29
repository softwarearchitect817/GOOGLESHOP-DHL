<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Auth;
use Session;
use App\Models\AbandonedCart;

class LoginController extends Controller
{
   	public function __construct()
    {
       if(env('MULTILEVEL_CUSTOMER_REGISTER') != true || url('/') == env('APP_URL')){
        abort(404);
       }


    }
   	use ThrottlesLogins;

   	/**
    * Max login attempts allowed.
    */
   	public $maxAttempts = 5;

    /**
    * Number of minutes to lock the login.
    */
    public $decayMinutes = 3;

    /**
     * Login the admin.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|max:100|email',
            'password' => 'required',
        ]);

    	//check if the user has too many login attempts.
    	if ($this->hasTooManyLoginAttempts($request)){
            //Fire the lockout event.
    		$this->fireLockoutEvent($request);

            //redirect the user back after lockout.
    		return $this->sendLockoutResponse($request);
    	}

    	if(Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password, 'created_by' => domain_info('user_id')],$request->filled('remember'))){
            //Authentication passed...
            if (Session::has('cartId')) {
    	   	    $cartId = Session::get('cartId');
    	   	    $cart = AbandonedCart::find($cartId);
    	   	    if ($cart) {
    	   	        $cart->customer_id = Auth::guard('customer')->id();
    	   	        $cart->is_guest = 0;
    	   	        $cart->save();
    	   	    }
    	   	}
    	   	
    		return redirect()
    		    ->intended(url('/user/dashboard'))
    		    ->with('status','You are Logged in as Admin!');
    	}

    	//keep track of login attempts from the user.
    	$this->incrementLoginAttempts($request);

       //Authentication failed...
    	return $this->loginFailed();
    }

     /**
     * Username used in ThrottlesLogins trait
     * 
     * @return string
     */
    public function username(){
        return 'email';
    }

    /**
     * Logout the admin.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
    	Auth::guard('customer')->logout();
    	return redirect('/');
    }

    /**
     * Validate the form data.
     * 
     * @param \Illuminate\Http\Request $request
     * @return 
     */
    private function validator(Request $request)
    {
      //validate the form...
    }

    /**
     * Redirect back after a failed login.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    private function loginFailed()
    {

      return redirect()
        ->back()
        ->withInput()
        ->with('error','Login failed, please try again!');
    }
}