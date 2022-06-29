<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use App\User;
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
   // protected $redirectTo = 'admin/dashboard';


    public function redirectTo()
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
       $this->middleware('guest')->except('logout');
   }
}
