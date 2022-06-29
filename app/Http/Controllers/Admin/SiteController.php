<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Option;
use File;
use Cache;
use Str;
use Auth;
class SiteController extends Controller
{
    public function site_settings()
    {
		if (!Auth()->user()->can('site.settings')) {
            return abort(401);
        }

    	$site_info=\App\Option::where('key','company_info')->first();
        $info=json_decode($site_info->value);
    // 	$currency_name=Option::where('key','currency_name')->first();
    // 	$currency_icon=Option::where('key','currency_icon')->first();
    	$order_prefix=Option::where('key','order_prefix')->first();
        $currency_info=Option::where('key','currency_info')->first();
    	$auto_order=Option::where('key','auto_order')->first();
        $tax=Option::where('key','tax')->first();
        $default_currency = ["currency_name" => "USD", "currency_icon" => "$"];
        $all_currencies = $this->allCurrencies();
        
        //-----default currency is non changable forever-----//
        $default_currency = json_encode($default_currency);
        $default_currency = json_decode($default_currency);
        
        //--------------additioanl currencies-------------//
        $additional_currencies = json_decode($currency_info->value ?? '');
        $additional_currencies = (array)$additional_currencies->currency_name;

        $currency_info=json_decode($currency_info->value ?? '');
       
    	return view('admin.settings.site_settings',compact('info', 'order_prefix','currency_info','auto_order','tax', 'all_currencies', 'default_currency','additional_currencies'));
    }

    public function site_settings_update(Request $request)
    {
    	$option=Option::where('key','company_info')->first();
    	if (empty($option)) {
    		$option=new Option;
    		$option->key="company_info";
    	}
    	$data['name']=$request->site_name;
        $data['site_description']=$request->site_description;
    	$data['email1']=$request->email1;
    	$data['email2']=$request->email2;
    	$data['phone1']=$request->phone1;
    	$data['phone2']=$request->phone2;
    	$data['country']=$request->country;
    	$data['zip_code']=$request->zip_code;
    	$data['state']=$request->state;
    	$data['city']=$request->city;
    	$data['address']=$request->address;
        $data['facebook']=$request->facebook ?? '';
        $data['twitter']=$request->twitter ?? '';
        $data['linkedin']=$request->linkedin ?? '';
        $data['instagram']=$request->instagram ?? '';
        $data['youtube']=$request->youtube ?? '';
    	$data['site_color']=$request->site_color;
    	$option->value=json_encode($data);
    	$option->save();
        $str = explode(',', $request->currency_default);
        $currency_data['currency_default']['currency_name']=$str[0];
        $currency_data['currency_default']['currency_icon']=$str[1];
        $currency_data['currency_position']=$request->currency_position;
        
        $cu_names = [];
        foreach($request->currency_name as $key => $value) {
            $str = explode(',', $value);
            $cu_names[$str[0]] = $str[1];
        }
        $currency_data['currency_name']=$cu_names;
    	$currency_name=Option::where('key','currency_info')->first();
    	if (empty($currency_name)) {
    		$currency_name=new Option;
    		$currency_name->key="currency_info";
    	}
    	$currency_name->value=json_encode($currency_data);
    	$currency_name->save();


    	$order_prefix=Option::where('key','order_prefix')->first();
    	if (empty($order_prefix)) {
    		$order_prefix=new Option;
    		$order_prefix->key="order_prefix";
    	}
    	$order_prefix->value=$request->order_prefix;
    	$order_prefix->save();


        $auto_order=Option::where('key','auto_order')->first();
        if (empty($auto_order)) {
            $auto_order=new Option;
            $auto_order->key="auto_order";
        }
        $auto_order->value=$request->auto_order;
        $auto_order->save();

        $tax=Option::where('key','tax')->first();
        if (empty($tax)) {
            $tax=new Option;
            $tax->key="tax";
        }
        $tax->value=$request->tax;
        $tax->save();



    	if ($request->logo) {
    		$validatedData = $request->validate([
    			'logo' => 'mimes:png',
    		]);
    	   $path='uploads/';
           $fileName = 'logo.png';
           $request->logo->move($path,$fileName);
    	}
    	if ($request->favicon) {
    		$validatedData = $request->validate([
    			'favicon' => 'mimes:ico',
    		]);
    	   $path='uploads/';
           $fileName = 'favicon.ico';
           $request->favicon->move($path,$fileName);
    	}



        Cache::forget('site_info');
    	return response()->json(['Site Settings Updated']);
    }

    public function system_environment_view()
    {
		if (!Auth()->user()->can('site.settings')) {
            return abort(401);
        }
    	$countries= base_path('resources/lang/langlist.json');
        $countries= json_decode(file_get_contents($countries),true);
    	return view('admin.settings.env',compact('countries'));
    }

    public function env_update(Request $request)
    {
    	$APP_URL_WITHOUT_WWW=str_replace('www.','', url('/'));
    	 $APP_NAME = Str::slug($request->APP_NAME);
$txt ="APP_NAME=".$APP_NAME."
APP_ENV=".$request->APP_ENV."
APP_KEY=".$request->APP_KEY."
SITE_KEY=".env('SITE_KEY')."
AUTHORIZED_KEY=".env('AUTHORIZED_KEY')."
APP_DEBUG=".$request->APP_DEBUG."
APP_URL=".$request->APP_URL."
APP_URL_WITHOUT_WWW=".$APP_URL_WITHOUT_WWW."
APP_PROTOCOLESS_URL=".$request->APP_PROTOCOLESS_URL."
APP_PROTOCOL=".$request->APP_PROTOCOL."
MULTILEVEL_CUSTOMER_REGISTER=".$request->MULTILEVEL_CUSTOMER_REGISTER."

LOG_CHANNEL=".$request->LOG_CHANNEL."
LOG_LEVEL=".$request->LOG_LEVEL."\n
DB_CONNECTION=".env("DB_CONNECTION")."
DB_HOST=".env("DB_HOST")."
DB_PORT=".env("DB_PORT")."
DB_DATABASE=".env("DB_DATABASE")."
DB_USERNAME=".env("DB_USERNAME")."
DB_PASSWORD=".env("DB_PASSWORD")."\n
BROADCAST_DRIVER=".$request->BROADCAST_DRIVER."
CACHE_DRIVER=".$request->CACHE_DRIVER."
QUEUE_CONNECTION=".$request->QUEUE_CONNECTION."
SESSION_DRIVER=".$request->SESSION_DRIVER."
SESSION_LIFETIME=".$request->SESSION_LIFETIME."\n
REDIS_HOST=".$request->REDIS_HOST."
REDIS_PASSWORD=".$request->REDIS_PASSWORD."
REDIS_PORT=".$request->REDIS_PORT."\n
QUEUE_MAIL=".$request->QUEUE_MAIL."
MAIL_MAILER=".$request->MAIL_MAILER."
MAIL_HOST=".$request->MAIL_HOST."
MAIL_PORT=".$request->MAIL_PORT."
MAIL_USERNAME=".$request->MAIL_USERNAME."
MAIL_PASSWORD=".$request->MAIL_PASSWORD."
MAIL_ENCRYPTION=".$request->MAIL_ENCRYPTION."
MAIL_FROM_ADDRESS=".$request->MAIL_FROM_ADDRESS."
MAIL_TO=".$request->MAIL_TO."
MAIL_NOREPLY=".$request->MAIL_NOREPLY."
MAIL_FROM_NAME=".Str::slug($request->MAIL_FROM_NAME)."\n
DO_SPACES_KEY=".$request->DO_SPACES_KEY."
DO_SPACES_SECRET=".$request->DO_SPACES_SECRET."
DO_SPACES_ENDPOINT=".$request->DO_SPACES_ENDPOINT."
DO_SPACES_REGION=".$request->DO_SPACES_REGION."
DO_SPACES_BUCKET=".$request->DO_SPACES_BUCKET."\n
NOCAPTCHA_SECRET=".$request->NOCAPTCHA_SECRET."
NOCAPTCHA_SITEKEY=".$request->NOCAPTCHA_SITEKEY."



TIMEZONE=".$request->TIMEZONE.""."
DEFAULT_LANG=".$request->DEFAULT_LANG."\n
";
  File::put(base_path('.env'),$txt);
if(getenv("AUTO_APPROVED_DOMAIN") !== false){
   $t="
AUTO_APPROVED_DOMAIN=".$request->AUTO_APPROVED_DOMAIN."
MOJODNS_AUTHORIZATION_TOKEN=".$request->MOJODNS_AUTHORIZATION_TOKEN."
SERVER_IP=".$request->SERVER_IP."
CNAME_DOMAIN=".$request->CNAME_DOMAIN."
VERIFY_IP=".$request->VERIFY_IP."
VERIFY_CNAME=".$request->VERIFY_CNAME."";

  File::append(base_path('.env'),$t);
}

     
       return response()->json(['System Updated']);


    }
    
    public function get_currencies_from_json($part){
    $path = storage_path().'/currencies';
    
    if($part == 'all'){
        $fileName = 'All_Currencies_info.json';
        $path = $path.'/'.$part.'/'.$fileName;
        $path_json_data = json_decode(file_get_contents($path));
        return $path_json_data;
    }
    }

    public function allCurrencies(){
        $all_currencies = $this->get_currencies_from_json('all');
        return $all_currencies;
    }
}
