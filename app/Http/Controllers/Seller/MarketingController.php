<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Useroption;
use App\Models\NewsLetter;
use App\Models\SystemTemplate;
use App\Models\Userplan;
use App\Models\Credits;
use App\Models\Customer;
use App\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class MarketingController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $plan=user_limit();
         
       if ($request->type=='google-analytics') {
           
        
          
            $google_analytics=filter_var($plan['google_analytics']);
            if($google_analytics == false){
               $msg='This module did not support your subscription.';
               $error['errors']['error']=$msg;
               return response()->json($error,401);
               
            } 


            $validatedData = $request->validate([
                'ga_measurement_id' => 'required|max:50',
                'analytics_view_id' => 'required|max:50',
                'file' => 'mimes:json|max:50',

            ]);

           $google= Useroption::where('user_id',seller_id())->where('key','google-analytics')->first();
           if (empty($google)) {
               $google = new Useroption;
               $google->user_id=seller_id();
               $google->key="google-analytics";
           }

           $data['ga_measurement_id']=$request->ga_measurement_id;
           $data['analytics_view_id']=$request->analytics_view_id;

           $google->value=json_encode($data);
           $google->status=$request->status;
           $google->save();

           if ($request->file) {
             $path='uploads/'.$google->user_id.'/';
             $fileName = 'service-account-credentials.'.$request->file->extension();
             $request->file->move($path,$fileName);
           }

           return response()->json(['Google Analytics Updated']);
       }

       if ($request->type=='tag-manager') {
             $google_analytics=filter_var($plan['gtm']);
            if($google_analytics == false){
               $msg='This module did not support your subscription.';
               $error['errors']['error']=$msg;
               return response()->json($error,401);
               
            } 

            $validatedData = $request->validate([
                'tag_id' => 'required|max:50',
            ]);

           $tag_manager= Useroption::where('user_id',seller_id())->where('key','tag_manager')->first();
           if (empty($tag_manager)) {
               $tag_manager = new Useroption;
               $tag_manager->user_id=seller_id();
               $tag_manager->key="tag_manager";
           }

           $tag_manager->value=$request->tag_id;
           $tag_manager->status=$request->status;
           $tag_manager->save();

           

           return response()->json(['Google Tag Manager Updated']);
       }



       if ($request->type=='whatsapp') {
            $google_analytics=filter_var($plan['whatsapp']);
            if($google_analytics == false){
               $msg='This module did not support your subscription.';
               $error['errors']['error']=$msg;
               return response()->json($error,401);
               
            } 

            $validatedData = $request->validate([
                'number' => 'required|max:20',
                'shop_page_pretext' => 'required|max:50',
                'other_page_pretext' => 'required|max:50',

            ]);

           $google= Useroption::where('user_id',seller_id())->where('key','whatsapp')->first();
           if (empty($google)) {
               $google = new Useroption;
               $google->user_id=seller_id();
               $google->key="whatsapp";
           }
           $data['phone_number']=$request->number;
           $data['shop_page_pretext']=$request->shop_page_pretext;
           $data['other_page_pretext']=$request->other_page_pretext;


           $google->value=json_encode($data);
           $google->status=$request->status;
           $google->save();

           return response()->json(['Whatsapp Settings Updated']);
       }
      if ($request->type=='fb_pixel') {
           $google_analytics=filter_var($plan['facebook_pixel']);
            if($google_analytics == false){
               $msg='This module did not support your subscription.';
               $error['errors']['error']=$msg;
               return response()->json($error,401);
               
            } 

          $validatedData = $request->validate([
            'pixel_id' => 'required|max:40',
            

          ]);

        $pixel= Useroption::where('user_id',seller_id())->where('key','fb_pixel')->first();
        if (empty($pixel)) {
           $pixel = new Useroption;
           $pixel->user_id=seller_id();
           $pixel->key="fb_pixel";
         }
         


         $pixel->value=$request->pixel_id;
         $pixel->status=$request->status;
         $pixel->save();

         return response()->json(['Facebook Pixel Settings Updated']);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $param
     * @return \Illuminate\Http\Response
     */
    public function show($param)
    {
       if ($param=='facebook-pixel') {
            $fb_pixel= Useroption::where('user_id',seller_id())->where('key','fb_pixel')->first();

           return view('seller.marketing.facebook',compact('fb_pixel'));
        }

        if ($param=='google-analytics') {
            $google= Useroption::where('user_id',seller_id())->where('key','google-analytics')->first();
            $info=json_decode($google->value ?? '');
            return view('seller.marketing.google',compact('google','info'));
        }

        if ($param=='tag-manager') {
            $tag= Useroption::where('user_id',seller_id())->where('key','tag_manager')->first();
            $info=json_decode($tag->value ?? '');
            return view('seller.marketing.tag',compact('tag','info'));
        } 

        if ($param=='whatsapp') {
            $whatsapp= Useroption::where('user_id',seller_id())->where('key','whatsapp')->first();
            $json=json_decode($whatsapp->value ?? '');
            return view('seller.marketing.whatsapp',compact('whatsapp','json'));
        }
        if($param == 'news-letter')
        {
            $domain = $_SERVER['HTTP_HOST'];
            $user_id = DB::table('domains')->where('domain', $domain)->pluck('user_id')->first();
            $newsletters = NewsLetter::orderBy('id' ,'DESC')->where('user_id',$user_id)->get();
            
            $userdata = Userplan::where('user_id', $user_id)->latest()->first();
            //------------monthly expire validate-------------------//
            $today = date('Y-m-d');
            if($today > $userdata->monthly_expire || $today == $userdata->monthly_expire){
                Userplan::where('user_id', $user_id)->update(['credit_emails' => 0, 'sent_emails' => 0]);
                $userdata = Userplan::where('user_id', $user_id)->latest()->first();
            }
            //-----------------end validate ------------------------------//
            $userplan_data = json_decode($userdata->plan_info->data ?? "");
            $subscribers = Customer::where('subscribe', 1)->count();
            $unsubscribers = Customer::where('subscribe', 0)->count();
            $sent_emails = $userdata->sent_emails;
            $credit_emails = $userdata->credit_emails;
            
            $available = $subscribers + $credit_emails - $sent_emails;
            $credits = Credits::where('set_step', 0)->get();
            $step = Credits::where('set_step', 1)->first();
            //   $whatsapp= NewsLetter::where('user_id',seller_id())->where('key','news-lette')->first();
            //     $json=json_decode($whatsapp->value ?? '');
            return view('seller.marketing.newsletter.index', compact('newsletters', 'sent_emails', 'credit_emails', 'subscribers', 'unsubscribers','available', 'credits', 'step'));
        }
          if($param == 'system-email')
        {
            $system_templates = DB::table('system_templates')->where('user_id', seller_id())->get();
            return view('seller.marketing.systememail.index', compact('system_templates'));
        }
        
        if($param == 'feed')
        {
           $exist_files = 0;
           if(File::exists(base_path('../feeds/'.seller_id().'/facebook.xml')) && File::exists(base_path('../feeds/'.seller_id().'/google.xml'))){
               $exist_files = 1;
           }
           $categories = Category::where('user_id',seller_id())->where('type','category')->get();
           return view('seller.marketing.feed.index', compact('categories', 'exist_files'));
        }
        

        abort(404);
    }
    
    public function feedgen(Request $request){
        $categories = array();
        foreach($request->ids as $id){
            $category = Category::where('user_id',seller_id())->where('type','category')->where('id', $id)->with('take_20_product')->first();
            array_push($categories, $category);
        }
        $domain = DB::table('domains')->where('user_id', seller_id())->first();
        $xml_fb = View::make('seller.marketing.feed.template.facebook')->with(compact('categories', 'domain'))->render();
        $xml_go = View::make('seller.marketing.feed.template.google')->with(compact('categories', 'domain'))->render();
        Storage::disk('feeds')->put(seller_id().'/facebook.xml', $xml_fb);
        Storage::disk('feeds')->put(seller_id().'/google.xml', $xml_go);
        $header = ['Content-Type: text/xml'];
        return response()->json(['XML Feed Generate Successfully']);
    }
    
    public function download($type)
    {
        $path = '';
        $filename = '';
        $header = ['Content-Type: text/xml'];
        if($type == 'fb'){
            $path = base_path('../feeds').'/'.seller_id().'/facebook.xml';
            $filename = 'meta.xml';
        }else {
            $path = base_path('../feeds').'/'.seller_id().'/google.xml';
            $filename = 'google.xml';
        }
        return Response::download($path, $filename, $header);
    }
    

}