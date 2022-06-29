<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plan;
use App\Category;
use App\Domain;
use App\Option;
use Session;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Trasection;
use App\Models\Userplan;
use App\Models\Userplanmeta;
use App\Models\Credits;
use App\Models\PaymentCredits;
use Omnipay\Omnipay;
use DB;
class CreditsController extends Controller
{
    public $gateway;
    public $completePaymentUrl;
    public function __construct()
    {
        $getways=Category::where('type','payment_getway')->where('slug', 'stripe')->first();
        $getways_id = $getways->id;
        $getways = Category::where('type','payment_getway')->with('credentials')->findorFail($getways_id);
        $credentials=json_decode($getways->credentials->content ?? '');
        $this->gateway = Omnipay::create('Stripe\PaymentIntents');
        $this->gateway->setApiKey($credentials->secret_key);  
        $this->completePaymentUrl = url('confirm');
    }
    
    public function index(){
        return view('seller.payment_credits.payment_success');
    }
    
	public function make_payment_credit(Request $request)
	{   
	    $num = $request->email_plan;
	    Session::put('credits_num', $num);
		$info=Credits::where('num', $num)->first();
		$step = Credits::where('set_step', 1)->first();
		$getways=Category::where('type','payment_getway')->with('credentials')->where('featured',1)->where('slug', 'stripe')->with('preview')->get();
		$plan_price = 0;
		if(isset($info->num)){
		   $plan_price = $info->discount_price;
		}else {
		    $plan_price = $num / $step->num * $step->discount_price;
		}
		$tax=Option::where('key','tax')->first();
		$tax= ($plan_price / 100) * $tax->value;

		$currency=Option::where('key','currency_info')->first();
		$currency=json_decode($currency->value);
		$currency_name=$currency->currency_default->currency_name;
		$price=$currency_name.' '.number_format($plan_price + $tax,2);
		$main_price=$plan_price;
		$amount = number_format($plan_price + $tax,2);
		return view('seller.payment_credits.stripe_payment',compact('info','getways','price','tax','main_price', 'num', 'amount'));
	}

	public function renew()
	{
		
		return redirect('seller/make-payment/'.Auth::user()->user_plan->plan_id);
	}

	public function make_charge_credit(Request $request, $num)
	{
	    $info=Credits::where('num', $num)->first();
		$step = Credits::where('set_step', 1)->first();
		$getways=Category::where('type','payment_getway')->with('credentials')->where('featured',1)->where('slug', 'stripe')->with('preview')->get();
		$plan_price = 0;
		if(isset($info->num)){
		   $plan_price = $info->discount_price;
		}else {
		    $plan_price = $num / $step->num * $step->discount_price;
		}
		$tax=Option::where('key','tax')->first();
		$tax= ($plan_price / 100) * $tax->value;

		$currency=Option::where('key','currency_info')->first();
		$currency=json_decode($currency->value);
		$currency_name=$currency->currency_default->currency_name;
		
		$price=$currency_name.' '.number_format($plan_price + $tax,2);
		$main_price=$plan_price;
		$amount = number_format($plan_price + $tax,2);
		if($request->input('stripeToken'))
        {
            $token = $request->input('stripeToken');
 
            $response = $this->gateway->authorize([
                'amount' => $request->input('amount'),
                'currency' => $currency_name,
                'description' => 'This is a X purchase transaction.',
                'token' => $token,
                'returnUrl' => $this->completePaymentUrl,
                'confirm' => true,
            ])->send();
 
            if($response->isSuccessful())
            {
                $response = $this->gateway->capture([
                    'amount' => $amount,
                    'currency' => $currency_name,
                    'paymentIntentReference' => $response->getPaymentIntentReference(),
                ])->send();
 
                $arr_payment_data = $response->getData();
 
                $this->store_payment([
                    'payment_id' => $arr_payment_data['id'],
                    'payer_email' => $request->input('email'),
                    'amount' => $arr_payment_data['amount']/100,
                    'currency' => $currency_name,
                    'payment_status' => $arr_payment_data['status'],
                    'credits_num' => Session::get('credits_num'),
                ]);
                
                $this->add_credits(Session::get('credits_num'));
 
                return redirect("/seller/payment_credits")->with("success", "Payment is successful. Your payment id is: ". $arr_payment_data['id']);
            }
            elseif($response->isRedirect())
            {
                session(['payer_email' => $request->input('email')]);
                $response->redirect();
            }
            else
            {
                return redirect("/seller/payment_credits")->with("error", $response->getMessage());
            }
        }
    }
 
    public function confirm(Request $request)
    {
        $currency=Option::where('key','currency_info')->first();
		$currency=json_decode($currency->value);
		$currency_name=$currency->currency_default->currency_name;
		
        $response = $this->gateway->confirm([
            'paymentIntentReference' => $request->input('payment_intent'),
            'returnUrl' => $this->completePaymentUrl,
        ])->send();
         
        if($response->isSuccessful())
        {
            $response = $this->gateway->capture([
                'amount' => $request->input('amount'),
                'currency' => $currency_name,
                'paymentIntentReference' => $request->input('payment_intent'),
            ])->send();
 
            $arr_payment_data = $response->getData();
 
            $this->store_payment([
                'payment_id' => $arr_payment_data['id'],
                'payer_email' => session('payer_email'),
                'amount' => $arr_payment_data['amount']/100,
                'currency' => $currency_name,
                'payment_status' => $arr_payment_data['status'],
                'credits_num' => Session::get('credits_num'),
            ]);
 
            return redirect("/seller/payment_credits")->with("success", "Payment is successful. Your payment id is: ". $arr_payment_data['id']);
        }
        else
        {
            return redirect("/seller/payment_credits")->with("error", $response->getMessage());
        }
    }
 
    public function store_payment($arr_data = [])
    {
        $currency=Option::where('key','currency_info')->first();
		$currency=json_decode($currency->value);
		$currency_name=$currency->currency_default->currency_name;
		
        $isPaymentExist = PaymentCredits::where('payment_id', $arr_data['payment_id'])->first();  
  
        if(!$isPaymentExist)
        {
            $payment = new PaymentCredits;
            $payment->payment_id = $arr_data['payment_id'];
            $payment->payer_email = $arr_data['payer_email'];
            $payment->amount = $arr_data['amount'];
            $payment->currency = $currency_name;
            $payment->payment_status = $arr_data['payment_status'];
            $payment->credits_num = $arr_data['credits_num'];
            $payment->user_id = seller_id();
            $payment->save();
        }
    }
    
    public function add_credits($num){
        $user = Userplan::where('user_id', seller_id())->first();
        $total = $num + $user->credit_emails;
        $future_timestamp = strtotime("+1 month");
        $monthly_expire = date('Y-m-d', $future_timestamp);
        Userplan::where('user_id', seller_id())->update(['credit_emails' => $total, 'monthly_expire' => $monthly_expire]);
        return true;
    }
}
