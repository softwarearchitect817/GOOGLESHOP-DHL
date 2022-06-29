<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plan;
use App\Category;
use App\Domain;
use App\Option;
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
use Auth;
use App\Trasection;
use App\Models\Userplan;
use App\Models\Userplanmeta;
use DB;
class PlanController extends Controller
{
	public function make_payment($id)
	{
		$info=Plan::where('status',1)->where('is_default',0)->where('is_trial',0)->where('price','>',0)->findorFail($id);

		$getways=Category::where('type','payment_getway')->with('credentials')->where('featured',1)->where('slug','!=','cod')->with('preview')->get();

		$tax=Option::where('key','tax')->first();
		$tax= ($info->price / 100) * $tax->value;

		$currency=Option::where('key','currency_info')->first();
		$currency=json_decode($currency->value);
		$currency_name=$currency->currency_default->currency_name;
		$price=$currency_name.' '.number_format($info->price+$tax,2);
		$main_price=$info->price;
		return view('seller.plan.payment',compact('info','getways','price','tax','main_price'));
	}

	public function renew()
	{
		
		return redirect('seller/make-payment/'.Auth::user()->user_plan->plan_id);
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
		if ($getway->slug=='razorpay') {
			return redirect('/seller/payment-with/razorpay');
		}
		if ($getway->slug=='mercado') {            
            return Mercado::make_payment($data);
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
			$expiry_date = \Carbon\Carbon::now()->addDays(($exp_days))->format('Y-m-d');

			$max_order=Userplan::max('id');
			$order_prefix=Option::where('key','order_prefix')->first();
			$tax=Option::where('key','tax')->first();
			$tax= ($plan->price / 100) * $tax->value;

			$order_no = $order_prefix->value.$max_order;

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
		}
		return redirect('seller/settings/plan');
		
	}

	public function fail()
	{
		Session::forget('payment_info');
		Session::flash('fail', 'Transection Failed');
		return redirect('seller/settings/plan');
	}
}
