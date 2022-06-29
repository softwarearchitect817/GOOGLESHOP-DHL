<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use App\User;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
   // protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

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