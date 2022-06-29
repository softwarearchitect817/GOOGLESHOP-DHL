<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Userplanmeta;
use App\Models\Customer;
use App\Models\AbandonedCart;
use Hash;
use App\Order;
use Cache;
use App\Useroption;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;
use Session;

class UserController extends Controller
{
    public function __construct()
    {
        if(env('MULTILEVEL_CUSTOMER_REGISTER') != true || url('/') == env('APP_URL')){
            abort(404);
        }
    }

    public function login(){

        if(Auth::check() == true){
            Auth::logout();
        }
   	    
   	    if(Auth::guard('customer')->check() == true){
   		    return redirect('/user/dashboard');
   	    }
        
        if(Cache::has(domain_info('user_id').'seo')){
            $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
        }
        else{
            $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
            $seo=json_decode($data->value ?? '');
        }
        
        if(!empty($seo)){
            JsonLdMulti::setTitle('Login - '.$seo->title ?? env('APP_NAME'));
            JsonLdMulti::setDescription($seo->description ?? null);
            JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

            SEOMeta::setTitle('Login - '.$seo->title ?? env('APP_NAME'));
            SEOMeta::setDescription($seo->description ?? null);
            SEOMeta::addKeyword($seo->tags ?? null);
            
            SEOTools::setTitle('Login - '.$seo->title ?? env('APP_NAME'));
            SEOTools::setDescription($seo->description ?? null);
            SEOTools::setCanonical($seo->canonical ?? url('/'));
            SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
            SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
            SEOTools::twitter()->setTitle('Login - '.$seo->title ?? env('APP_NAME'));
            SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
            SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
        }
       
   	    return view(user_template_path().'.account.login');
    }

    public function register(){
        if(Auth::check()){
            Auth::logout();
        }
   	    if(Auth::guard('customer')->check()){
   		    return redirect('/user/dashboard');
   	    }

        if(Cache::has(domain_info('user_id').'seo')){
            $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
        }
        else{
            $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
            $seo=json_decode($data->value ?? '');
        }
        
        if(!empty($seo)){
            JsonLdMulti::setTitle('Register - '.$seo->title ?? env('APP_NAME'));
            JsonLdMulti::setDescription($seo->description ?? null);
            JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

            SEOMeta::setTitle('Register - '.$seo->title ?? env('APP_NAME'));
            SEOMeta::setDescription($seo->description ?? null);
            SEOMeta::addKeyword($seo->tags ?? null);
            
            SEOTools::setTitle('Register - '.$seo->title ?? env('APP_NAME'));
            SEOTools::setDescription($seo->description ?? null);
            SEOTools::setCanonical($seo->canonical ?? url('/'));
            SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
            SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
            SEOTools::twitter()->setTitle('Register - '.$seo->title ?? env('APP_NAME'));
            SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
            SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
        }
   	    return view(user_template_path().'.account.register');
    }

    public function settings(){
        SEOTools::setTitle('Settings');
        return view(user_template_path().'.account.account');
    }

   public function settings_update(Request $request){
        $validatedData = $request->validate([
            'name' =>  'required|max:255',
            'email'  => 'required|email|unique:customers,email,'.Auth::guard('customer')->user()->id
        ]);

      if ($request->password) {
         $validatedData = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
         ]);         
      }

      $user=Customer::find(Auth::guard('customer')->user()->id);
      $user->name=$request->name;
      $user->email=$request->email;
      if ($request->password) {
         $check=Hash::check($request->password_current,auth()->user()->password);
         if ($check==true) {
            $user->password= Hash::make($request->password);
         }
         else{
            $returnData['errors']['password']=array(0=>"Enter Valid Password");
            $returnData['message']="given data was invalid.";            
            return response()->json($returnData, 401);
         }
      }
      $user->save();

      return response()->json(['Profile Updated Successfully']);  
   }

   public function orders(){
      SEOTools::setTitle('Orders');
      $orders=Order::where('customer_id',Auth::guard('customer')->user()->id)->where('user_id',domain_info('user_id'))->with('payment_method')->latest()->paginate(20);
      return view(user_template_path().'.account.orders',compact('orders'));
   }

   public function order_view($id){
      $id=request()->route()->parameter('id');
      $info=Order::where('customer_id',Auth::guard('customer')->user()->id)->where('user_id',domain_info('user_id'))->with('order_item_with_file','order_content','shipping_info','payment_method')->findorFail($id);
      $order_content=json_decode($info->order_content->value);
       SEOTools::setTitle('Order No '.$info->order_no);
      return view(user_template_path().'.account.order_view',compact('info','order_content'));
   }

    public function register_user(Request $request){
	   	$validated = $request->validate([
	   		'email' => 'required|email|max:100',
	   		'name' => 'required|max:100',
	   		'password' => 'required|confirmed|min:8|max:50',
	   	]);
	   	$domain_id=domain_info('domain_id');
        $user_id=domain_info('user_id');

      
        $user_limit=domain_info('customer_limit',0);
        $total_customers=Customer::where('created_by',$user_id)->count();
       
        if($user_limit <= $total_customers){
            \Session::flash('user_limit','Opps something wrong please contact with us..!!');
            return back();
        }
        
        $check=Customer::where([['created_by',$user_id],['email',$request->email]])->first();
        if(!empty($check)){
            \Session::flash('user_limit','Opps the email address already exists...!!');
            return back();
        }
	   	
	   	$user= new Customer();
	   	$user->email=$request->email;
	   	$user->name=$request->name;
	   	$user->password=Hash::make($request->password);
	   	$user->domain_id=$domain_id;
	   	$user->created_by=$user_id;	   	
	   	$user->save();
	   	Auth::guard('customer')->loginUsingId($user->id);
	   	
	   	if (Session::has('cartId')) {
	   	    $cartId = Session::get('cartId');
	   	    $cart = AbandonedCart::find($cartId);
	   	    if ($cart) {
	   	        $cart->customer_id = $user->id;
	   	        $cart->is_guest = 0;
	   	        $cart->save();
	   	    }
	   	}
	   	return redirect('/user/dashboard');
   }

    public function dashboard(){   
   	    if(Auth::guard('customer')->check()){
            SEOTools::setTitle('Dashboard');
   	        return view(user_template_path().'.account.dashboard');
   	    }
   	    
        return redirect('/user/login');   	
    }
}