<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use App\Models\User;
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
   // protected $redirectTo = RouteServiceProvider::HOME;

     public function redirectTo()
    {

        if (Auth::user()->role_id==1) {
           if (url('/') != env('APP_URL')) {
            Auth::logout();
            $this->redirectTo=env('APP_URL').'/login';
            return $this->redirectTo;
           }else{
            $this->redirectTo=env('APP_URL').'/admin/dashboard';
            return $this->redirectTo;
           } 
           
        }
        elseif (Auth::user()->role_id==2) {
           $url= Auth::user()->user_domain->full_domain;
           if (str_replace('www.','',url('/')) != $url) {
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
          if (Auth::user()->status==3) {
            $this->redirectTo=env('APP_URL').'/merchant/dashboard';
            return $this->redirectTo;
          }
          elseif (Auth::user()->status === 0 || Auth::user()->status == 2) {
            $this->redirectTo=env('APP_URL').'/suspended';
            return $this->redirectTo;
          }
          elseif (url('/') != $url && Auth::user()->status != 3) {
             Auth::logout();
             return  $this->redirectTo=$url.'/login';
           }
           else{
             if(url('/') != $url){
               Auth::logout();
               return  $this->redirectTo=$url.'/login';
             }
            return $this->redirectTo=$url.'/seller/dashboard';
           }
          
           
       }
       $this->middleware('guest')->except('logout');
   }
}
