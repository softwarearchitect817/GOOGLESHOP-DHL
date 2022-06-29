<?php

namespace App\Http\Controllers;
use App\Option;
use App\Category;
use App\Domain;
use App\Plan;
use App\Trasection;
use App\Term;
use App\Models\User;
use App\Models\Userplan;
use App\Models\Customer;
use Auth;
use Hash;
use App\Helper\Subscription\Paypal;
use App\Helper\Subscription\Toyyibpay;
use App\Helper\Subscription\Instamojo;
use App\Helper\Subscription\Stripe;
use App\Helper\Subscription\Mollie;
use App\Helper\Subscription\Paystack;
use App\Helper\Subscription\Mercado;
use Session;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminContactMail;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;
use App\Models\Userplanmeta;
use Illuminate\Http\Request;
use DB;
use Str;
use File;
use Storage;
use App\Models\Currency;
class FrontendController extends Controller
{
    public function welcome(Request $request)
    {   
        $url=$request->getHost();
        $url=str_replace('www.','',$url); 
        $this->set_session_curr_rate();
        if($url==env('APP_PROTOCOLESS_URL') || $url == 'localhost'){

        
        $seo=Option::where('key','seo')->first();
        $seo=json_decode($seo->value);

       JsonLdMulti::setTitle($seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/logo.png'));

       SEOMeta::setTitle($seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle($seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
       SEOTools::twitter()->setTitle($seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));

        
      $latest_gallery=Category::where('type','gallery')->with('preview')->where('is_admin',1)->latest()->take(15)->get();
      $features=Category::where('type','features')->with('preview','excerpt')->where('is_admin',1)->latest()->take(6)->get(); 
      
      $testimonials=Category::where('type','testimonial')->with('excerpt')->where('is_admin',1)->latest()->get(); 

      $brands=Category::where('type','brand')->with('preview')->where('is_admin',1)->latest()->get(); 

      $plans=Plan::where('status',1)->where('is_default',0)->latest()->take(3)->get();
      $header=Option::where('key','header')->first();
      $header=json_decode($header->value ?? '');

      $about_1=Option::where('key','about_1')->first();
      $about_1=json_decode($about_1->value ?? '');
       
        $about_2=Option::where('key','about_2')->first();
        $about_2=json_decode($about_2->value ?? '');

        $about_3=Option::where('key','about_3')->first();
        $about_3=json_decode($about_3->value ?? '');

        $ecom_features=Option::where('key','ecom_features')->first();
        $ecom_features=json_decode($ecom_features->value ?? '');

        $counter_area=Option::where('key','counter_area')->first();
        $counter_area=json_decode($counter_area->value ?? '');

      return view('welcome',compact('latest_gallery','plans','features','header','about_1','about_3','about_2','testimonials','brands','ecom_features','counter_area'));

       }


       return redirect('/check');
    }

    public function check(Request $request){
       $url=$request->getHost();
       $url=str_replace('www.','',$url);
       if($url==env('APP_PROTOCOLESS_URL') || $url == 'localhost'){
        return redirect(env('APP_URL'));
       }

       \Helper::domain($url,url('/'));

       return redirect(url('/'));

    }

   
    public function page($slug){
        $info=Term::where('slug',$slug)->with('content','excerpt')->where('is_admin',1)->first();
        if(empty($info)){
            abort(404);
        }
        JsonLdMulti::setTitle($info->title);
        JsonLdMulti::setDescription($info->excerpt->value ?? null);
        JsonLdMulti::addImage(asset('uploads/logo.png'));

        SEOMeta::setTitle($info->title);
        SEOMeta::setDescription($info->excerpt->value ?? null);
       

        SEOTools::setTitle($info->title);
        SEOTools::setDescription($info->excerpt->value ?? null);
       
        
        SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
        SEOTools::twitter()->setTitle($info->title);
       
        SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));
        return view('page',compact('info'));
    }

    public function service(){
        $seo=Option::where('key','seo')->first();
        $seo=json_decode($seo->value);
        JsonLdMulti::setTitle('Our Service');
        JsonLdMulti::setDescription($seo->description ?? null);
        JsonLdMulti::addImage(asset('uploads/logo.png'));

        SEOMeta::setTitle('Our Service');
        SEOMeta::setDescription($seo->description ?? null);
       

        SEOTools::setTitle('Our Service');
        SEOTools::setDescription($seo->description ?? null);
       
        
        SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
        SEOTools::twitter()->setTitle('Our Service');
       
        SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));
        $features=Category::where('type','features')->with('preview','excerpt')->where('is_admin',1)->latest()->get(); 
        return view('service',compact('features'));
    } 

    public function priceing(){
        $this->set_session_curr_rate();
        $seo=Option::where('key','seo')->first();
        $seo=json_decode($seo->value);
        JsonLdMulti::setTitle('Priceing');
        JsonLdMulti::setDescription($seo->description ?? null);
        JsonLdMulti::addImage(asset('uploads/logo.png'));

        SEOMeta::setTitle('Priceing');
        SEOMeta::setDescription($seo->description ?? null);
       

        SEOTools::setTitle('Priceing');
        SEOTools::setDescription($seo->description ?? null);
       
        
        SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
        SEOTools::twitter()->setTitle('Priceing');
       
        SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));
        
        $plans=Plan::where('status',1)->where('is_default',0)->get();

        return view('priceing',compact('plans'));
    }



    public function contact(){
        $seo=Option::where('key','seo')->first();
        $seo=json_decode($seo->value);

       JsonLdMulti::setTitle('Contact Us');
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/logo.png'));

       SEOMeta::setTitle('Contact Us');
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle('Contact Us');
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
       SEOTools::twitter()->setTitle('Contact Us');
       SEOTools::twitter()->setSite('Contact Us');
       SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));
        return view('contact');
    }
    
    public function unsubscribe(){
        $seo=Option::where('key','seo')->first();
        $seo=json_decode($seo->value);

       JsonLdMulti::setTitle('On Unsubscribe');
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/logo.png'));

       SEOMeta::setTitle('On Unsubscribe');
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle('On Unsubscribe');
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
       SEOTools::twitter()->setTitle('On Unsubscribe');
       SEOTools::twitter()->setSite('On Unsubscribe');
       SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));
        return view('unsubscribe');
    }
    
    public function register_unsubscriber(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
        ]);
        
        $customer = Customer::where('email', $request->email)->first();
        if(isset($customer->id)){
            Customer::where('id', $customer->id)->update(['subscribe' => 0]);
            return response()->json('You unsubscribe successfully !!');
        }else{
            return response()->json('Your email does not exist !!');
        }
    }

    public function send_mail(Request $request){
        if(env('NOCAPTCHA_SITEKEY') != null){
           $messages = [
                'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
                'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
            ];

            $validator = \Validator::make($request->all(), [
                'g-recaptcha-response' => 'required|captcha'
            ], $messages);

            if ($validator->fails())
            {
                $msg['errors']['domain']=$validator->errors()->all()[0];
                return response()->json($msg,422); 
                
            }
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|max:300',
        ]);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message
        ];
        Mail::to(env('MAIL_TO'))->send(new AdminContactMail($data));

        return response()->json('Your message submitted successfully !!');
    }
    

    public function register_view($id)
    {
     
       $info=Plan::where('status',1)->where('is_trial',1)->findorFail($id);
      return view('marchant.register',compact('info'));
    }

    public function translate(Request $request){
        Session::put('locale',$request->local);
        \App::setlocale($request->local);
        return redirect('/');
    }
    

    public function register(Request $request,$id)
    {
      

          if(env('NOCAPTCHA_SITEKEY') != null){
           $messages = [
                'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
                'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
            ];

            $validator = \Validator::make($request->all(), [
                'g-recaptcha-response' => 'required|captcha'
            ], $messages);

            if ($validator->fails())
            {
                $msg['errors']['domain']=$validator->errors()->all()[0];
                return response()->json($msg,422); 
                
            }
        }
        
        

      $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|unique:users|email|max:255',
        'password' => 'required|min:8|confirmed|string',
        'domain' => 'required|max:20|string',
      ]);

      $info=Plan::where('status',1)->where('is_trial',1)->findorFail($id);
      
        $domain=strtolower(Str::slug($request->domain)).'.'.env('APP_PROTOCOLESS_URL');
        $input = trim($domain, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
             $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        $domain = preg_replace('/^www\./', '', $urlParts['host']);
        $full_domain=env('APP_PROTOCOL').$domain;
      

      $check_fulldomain=Domain::where('full_domain',$full_domain)->first();

      $check=Domain::where('domain',$domain)->first();
      if (!empty($check)) {
        $msg['errors']['domain']="Sorry Store Name Already Exists";
        return response()->json($msg,422); 
      }

      if (!empty($check_fulldomain)) {
        $msg['errors']['domain']="Sorry Store Name Already Exists";
        return response()->json($msg,422); 
      }

        DB::beginTransaction();
        try {
      $user=new User;
      $user->name=$request->name;
      $user->email=$request->email;
      $user->password=Hash::make($request->password);
      $user->role_id=3;
      $user->status=env('AUTO_APPROVED_DOMAIN') == true ? 1 :  3;
      $user->save();

        $exp_days =  $info->days;
        $expiry_date = \Carbon\Carbon::now()->addDays(($exp_days))->format('Y-m-d');

         $max_order=Userplan::max('id');
         $order_prefix=Option::where('key','order_prefix')->first();


         $order_no = $order_prefix->value.$max_order;

         $userplan = new Userplan;
         $userplan->order_no=$order_no;
         $userplan->amount=0;
         $userplan->tax=0;
         $userplan->trx=Str::random(15).$max_order;
         $userplan->will_expire=$expiry_date;
         $userplan->user_id=$user->id;
         $userplan->plan_id=$info->id;
         $userplan->category_id=2;
         $userplan->status=1;
         $userplan->payment_status=1;
         $userplan->save();

        

      $dom=new Domain;
      $dom->domain=$domain;
      $dom->full_domain=$full_domain;
      $dom->status=env('AUTO_APPROVED_DOMAIN') == true ? 1 :  3;
      $dom->user_id=$user->id;
        $dom->is_trial=1;
        $dom->type=1;
        $dom->data=$info->data;
        $dom->will_expire=$expiry_date;
        $dom->userplan_id=$userplan->id;
      $dom->save();


      $user=User::find($user->id);
      $user->domain_id=$dom->id;
      $user->save();

        $dom->orderlog()->create(['userplan_id'=>$userplan->id,'domain_id'=>$dom->id]);

        DB::commit();
      } catch (Exception $e) {
      DB::rollback();
     }
    
    $default_template_path = base_path('resources/views/frontend/default-templates/bigbag');
    $user_template_path = base_path('resources/views/frontend/'.$user->id.'/bigbag');
    
    if(!File::isDirectory($user_template_path)){
        File::copyDirectory($default_template_path, $user_template_path);
    }
    
    $default_asset_path = base_path('../frontend/default-templates/bigbag');
    $user_asset_path = base_path('../frontend/'.$user->id.'/bigbag');
    
    if(!File::isDirectory($user_asset_path)){
        File::copyDirectory($default_asset_path, $user_asset_path);
    }
    
     if ($dom->status == 1) {
        $dta['domain']=$dom->full_domain.'/login';
        $dta['redirect']=true;

     }
     else{
        Auth::loginUsingId($user->id);
        $dta['redirect']=true;
        $dta['domain']=route('merchant.dashboard');
     }
       $dta['msg']='Successfully Registered';


      return response()->json($dta);
      
    }


    public function dashboard()
    {
      return view('seller.dashboard');
    }

    public function settings()
    {
        return view('seller.settings');
    }



    public function make_payment($id)
    {
        if(Session::has('success')){
           Session::flash('success', 'Thank You For Subscribe After Review The Order You Will Get A Notification Mail From Admin');
           return redirect('merchant/plan'); 
        }
      $info=Plan::where('status',1)->where('is_default',0)->where('is_trial',0)->where('price','>',0)->findorFail($id);

      $getways=Category::where('type','payment_getway')->with('credentials')->where('featured',1)->where('slug','!=','cod')->with('preview')->get();

      $tax=Option::where('key','tax')->first();
      $tax= ($info->price / 100) * $tax->value;

      $currency=Option::where('key','currency_info')->first();
      $currency=json_decode($currency->value);
      $currency_name=$currency->currency_name;
      $price=$currency_name.' '.number_format($info->price+$tax,2);
      $main_price=$info->price;
      return view('seller.plan.payment',compact('info','getways','price','tax','main_price'));
    }

    public function make_charge(Request $request,$id)
    {

        $info=Plan::where('status',1)->where('is_default',0)->where('is_trial',0)->where('price','>',0)->findorFail($id);

        $getway=Category::where('type','payment_getway')->where('featured',1)->where('slug','!=','cod')->findorFail($request->mode);

        $currency=Option::where('key','currency_info')->first();
        $currency=json_decode($currency->value);
        $currency_name=$currency->currency_name;
        

        $tax=Option::where('key','tax')->first();
        $tax= ($info->price / 100) * $tax->value;

        $total=str_replace(',', '', number_format($info->price+$tax,2));
       
        $data['ref_id']=$id;
        $data['getway_id']=$request->mode;
        $data['amount']=$total;
        $data['email']=Auth::user()->email;
        $data['name']=Auth::user()->name;
        $data['phone']=$request->phone;
        $data['billName']=$info->name;
        $data['currency']=strtoupper($currency_name);
        Session::put('order_info',$data);
        if ($getway->slug=='paypal') {
           return Paypal::make_payment($data);
        }
        if ($getway->slug=='instamojo') {
           return Instamojo::make_payment($data);
        }
        if ($getway->slug=='toyyibpay') {
           return Toyyibpay::make_payment($data);
        }
        if ($getway->slug=='stripe') {
            $data['stripeToken']=$request->stripeToken;
           return Stripe::make_payment($data);
        }
        if ($getway->slug=='mollie') {
            return Mollie::make_payment($data);
        }
        if ($getway->slug=='paystack') {            
            return Paystack::make_payment($data);
        }
        if ($getway->slug=='mercado') {            
            return Mercado::make_payment($data);
        }
        
        if ($getway->slug=='razorpay') {
           return redirect('/merchant/payment-with/razorpay');
        }

        
    }

    public function success()
    {
        if (Session::has('payment_info')) {
            $data = Session::get('payment_info');
            $plan=Plan::findorFail($data['ref_id']);

            DB::beginTransaction();
            try {
            

            $exp_days =  $plan->days;
            $expiry_date = \Carbon\Carbon::now()->addDays($exp_days)->format('Y-m-d');

            $max_order=Userplan::max('id');
            $order_prefix=Option::where('key','order_prefix')->first();
            

            $order_no = $order_prefix->value.$max_order;
            $tax=Option::where('key','tax')->first();
            $tax= ($plan->price / 100) * $tax->value;

            $user=new Userplan;
            $user->order_no=$order_no;
            $user->amount=$data['amount'];
            $user->tax=$tax;
            $user->trx=$data['payment_id'];
            $user->will_expire=$expiry_date;
            $user->user_id=Auth::id();
            $user->plan_id=$plan->id;
            $user->category_id=$data['getway_id'];;


            if (isset($data['payment_status'])) {
              $transection->payment_status = $data['payment_status'];
            }
            else{
              $user->payment_status = 1;
            }

            $auto_order=Option::where('key','auto_order')->first();
            if($auto_order->value == 'yes'  && $user->payment_status == 1){
              $user->status=1;
            }

            $user->save();

            if($auto_order->value == 'yes' && $user->status == 1){
              $dom=Domain::where('user_id',Auth::id())->first();
              $dom->data=$plan->data;
              $dom->userplan_id=$user->id;
              $dom->will_expire=$expiry_date;
              $dom->is_trial=0;
              $dom->save();


              $dom->orderlog()->create(['userplan_id'=>$user->id,'domain_id'=>$dom->id]);
            }
            Session::flash('success', 'Thank You For Subscribe After Review The Order You Will Get A Notification Mail From Admin');



            $data['info']=$user;
            $data['to_admin']=env('MAIL_TO');
            $data['from_email']=Auth::user()->email;
            
            try {
                if(env('QUEUE_MAIL') == 'on'){
                    dispatch(new \App\Jobs\SendInvoiceEmail($data));
                }
                else{
                    \Mail::to(env('MAIL_TO'))->send(new OrderMail($data));
                }
            } catch (Exception $e) {
                
            }
            
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
            }

            return redirect('merchant/plan');
        }
       abort(404);
    }

    public function fail()
    {
        Session::forget('payment_info');
        Session::flash('fail', 'Transection Failed');
        return redirect('merchant/plan');
    }

    public function plans()
    {
         $posts=Plan::where('status',1)->where('is_default',0)->where('price','>',0)->get();
         return view('seller.plan.index',compact('posts'));
    }
    
    public function set_session_curr_rate() {
        $currencies=Option::where('key','currency_info')->first();
        $main_to_currency = json_decode($currencies->value ?? '')->currency_default->currency_name;
        if(!Session::has('main_to_currency') || !Session::has('main_rate_base')){
          Session::put('main_to_currency', $main_to_currency);
          $rate = Currency::where('currency_id', $main_to_currency)->first()->usd;
          Session::put('main_rate_base', $rate);
        }
    }

    
    public function set_to_currency(Request $request){
        $to = $request->to;
        $default = $request->default;
        $rate = Currency::where('currency_id', $to)->first();
        if($default == "USD") {
          $rate = $rate->usd;
        }else {
          $rate = $rate->eur;
        }
        Session::put("main_to_currency", $to);
        Session::put("main_rate_base", $rate);
        return redirect('/');
   }
   
   public function cmpCalculator(){
       return view('frontend.cmpcalc');
   }
   public function profitCalculator(){
       return view('frontend.profitCalculator');
   }
   public function policygenerator(){
       return view('frontend.policygenerator');
   }
   
}