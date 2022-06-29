<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Auth;
use App\User;
class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Auth::user()->role_id==1) {
            $this->redirectTo=env('APP_URL').'/admin/dashboard';
            return $this->redirectTo;
        }
        elseif (Auth::user()->role_id==2) {
           $url= Auth::user()->user_domain->full_domain;
           if (url('/') != $url) {
             Auth::logout();
             $this->redirectTo=$url.'/user/login';
           }
           else{
             $this->redirectTo=$url.'/user/dashboard';
           }
          
           return $this->redirectTo;
       }
       elseif (Auth::user()->role_id==3) {
          $url= Auth::user()->user_domain->full_domain;
          if (url('/') != $url) {
             Auth::logout();
             $this->redirectTo=$url.'/login';
           }
           else{
             $this->redirectTo=$url.'/seller/dashboard';
           }
          
           return $this->redirectTo;
       }
        $this->middleware('auth');
    }
}
