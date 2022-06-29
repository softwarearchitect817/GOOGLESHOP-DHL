<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Userplan;
use App\Models\Userplanmeta;
use App\Models\Price;
use App\Category;
use App\Domain;
use App\Option;
use App\Plan;
use Carbon\Carbon;
use App\Mail\Sendmailtowillexpire;
use App\Mail\Planexpired;
use Illuminate\Support\Facades\Mail;
use Artisan;
use Auth;
class CronController extends Controller
{
	protected $sendMailToExpiredCustomer;
    protected $auto_plan_assign;

    public function makeExpireAbleCustomer()
    {
    	$users=Userplan::where('status',1)->with('user_with_domain','plan_info')->where('will_expired','<=',date('Y-m-d'))->latest()->get();
        
       
        $option=Option::where('key','company_info')->first();
        $company_info=json_decode($option->value);
        $auto_plan=Plan::where('is_default',1)->first();
        Userplan::where('status',1)->where('will_expired','<=',date('Y-m-d'))->update([
                'status'=>3
            ]);
               if(!empty($auto_plan) && $this->auto_plan_assign == true){
           
            $meta['name']=$auto_plan->name;
            $meta['product_limit']=$auto_plan->product_limit;
            $meta['storage']=$auto_plan->storage;
            $meta['customer_limit']=$auto_plan->customer_limit;
            $meta['category_limit']=$auto_plan->category_limit;
            $meta['location_limit']=$auto_plan->location_limit;
            $meta['brand_limit']=$auto_plan->brand_limit;
            $meta['variation_limit']=$auto_plan->variation_limit;
            
            $active_has_order= Userplanmeta::with('activeorder')->get();
            foreach ($active_has_order as $key => $value) {

               if($value->activeorder == null){
               
                Userplanmeta::where('id',$value->id)->update($meta);
               }
           }
            
        }
        
    	if ($this->sendMailToExpiredCustomer == true) {
            
    		foreach ($users as $key => $row) {
                
                $customer_email=$row->user_with_domain->email;
                $customer_name=$row->user_with_domain->name;
                $plan_name=$row->plan_info->name;
                $plan_price=amount_admin_format($row->plan_info->price,'format');
                if (!empty($row->user_with_domain->user_domain)) {
                    $checkoutUrl=$row->user_with_domain->user_domain->full_domain.'/seller/make-payment/'.$row->plan_id;
                    $data['checkout_url']=$checkoutUrl;
                    $data['expired_user']= $customer_email;
                    $data['customer_name']= $customer_name;
                    $data['plan_name']= $plan_name;
                    $data['plan_price']= $plan_price;
                    $data['purchased_at']= $row->created_at->format('Y-m-d');
                    $data['expiry_date']= $row->will_expired;
                    $data['order_id']= $row->order_no;
                    $data['company_info']= $company_info;

                    if(env('QUEUE_MAIL') == 'on'){
                        dispatch(new \App\Jobs\SendInvoiceEmail($data));
                    }
                    else{
                        Mail::to($customer_email)->send(new Planexpired($data));
                    }

                }


            }
    	}
    	

        

    	

    	
    }


    public function SendMailToWillExpirePlanWithInDay($days)
    {
    	$expiry_date = Carbon::now()->addDays($days - 2)->format('Y-m-d');

    	 $users=Userplan::where('status',1)->with('user_with_domain','plan_info')->where('will_expired','<=',$expiry_date)->latest()->get();
    	 $option=Option::where('key','company_info')->first();
         $company_info=json_decode($option->value);
    	foreach ($users as $key => $row) {
    	 $customer_email=$row->user_with_domain->email;
    	 $customer_name=$row->user_with_domain->name;
    	 $plan_name=$row->plan_info->name;
    	 $plan_price=amount_admin_format($row->plan_info->price,'format');
    	 if (!empty($row->user_with_domain->user_domain)) {
    		$checkoutUrl=$row->user_with_domain->user_domain->full_domain.'/seller/make-payment/'.$row->plan_id;
    		$data['checkout_url']=$checkoutUrl;
    		$data['to_will_expire_user']= $customer_email;
    		$data['customer_name']= $customer_name;
    		$data['plan_name']= $plan_name;
    		$data['plan_price']= $plan_price;
    		$data['purchased_at']= $row->created_at->format('Y-m-d');
    		$data['expiry_date']= $row->will_expired;
            $data['order_id']= $row->order_no;
            $data['company_info']= $company_info;

    		if(env('QUEUE_MAIL') == 'on'){
    			dispatch(new \App\Jobs\SendInvoiceEmail($data));
    		}
    		else{
    			Mail::to($customer_email)->send(new Sendmailtowillexpire($data));
    		}

    	 }
    	}
    	

    }

    public  function RunJob()
    {
        $cron_info=Option::where('key','cron_info')->first();
        $cron_info=json_decode($cron_info->value);
        if ($cron_info->send_notification_expired_date == 'on') {
           $this->sendMailToExpiredCustomer = true;
        }
        if ($cron_info->send_notification_expired_date == 'yes') {
           $this->auto_plan_assign = true;
        }
        
    	$this->makeExpireAbleCustomer();
    	
    }

    public function run_SendMailToWillExpirePlanWithInDay()
    {
        $cron_info=Option::where('key','cron_info')->first();
        $cron_info=json_decode($cron_info->value);
        $this->SendMailToWillExpirePlanWithInDay($cron_info->send_mail_to_will_expire_within_days);
    }

    public function reset_product_price(){
        $start=Price::where('starting_date','<=',date('Y-m-d'))->where('special_price','!=',null)->get();
        foreach($start as $row){
         
            if($row->price_type == 1){
                $price=$row->regular_price-$row->special_price;
            }
            else{
                $percent= $row->regular_price * $row->special_price / 100;
                $price= $row->regular_price-$percent;
                $price=str_replace(',','',number_format($price,2));
            }

            $new_price=Price::find($row->id);
            $new_price->price=$price;
            $new_price->save();

        }
        $ending_date=Price::where('ending_date','<=',date('Y-m-d'))->get();
        foreach($ending_date as $row){
            $price=Price::find($row->id);
            $price->price=$price->regular_price;
            $price->special_price=null;
            $price->price_type=1;
            $price->starting_date=null;
            $price->ending_date=null;
            $price->save();
        } 
        return response()->json('success');   
    }


    public function index()
    {
        
        if (!Auth()->user()->can('cron_job.control')) {
            return abort(401);
        }
        $option=Option::where('key','cron_info')->first();
        $info=json_decode($option->value);
       
       return view('admin.cron.index',compact('info'));
    }

    public function make_expirable_user()
    {
        Artisan::call('make:make_expirable_user');
        return "done";
    }

    public function send_mail_to_will_expire_plan_soon()
    {
       //before expired how many days left
          $option = Option::where('key','cron_option')->first();
         $cron_option = json_decode($option->value);

         $date= Carbon::now()->addDays($cron_option->days)->format('Y-m-d');
         
         $tenants=Tenant::where([['status',1],['will_expire','<=',$date],['auto_renew',0],['will_expire','!=',Carbon::now()->format('Y-m-d')]])->with('orderwithplan','user')->get();
         
        
         $expireable_tenants=[];

         foreach($tenants as $row){
            $plan=$row->orderwithplan->plan;
            
            if (!empty($plan)) {
                if($row->orderwithplan->plan->is_trial == 0){
                   $order_info['email']=$row->user->email;
                   $order_info['name']=$row->user->name;
                   $order_info['plan_name']=$plan->name;
                   $order_info['tenant_id']=$row->id;
                   $order_info['will_expire']=$row->will_expire;
                   $order_info['amount']=$plan->price;
                   $order_info['plan_name']=$plan->name;
                   array_push($expireable_tenants, $order_info);
                  
               }
               
            }
         }
         

         $this->expireSoon($expireable_tenants,$cron_option->alert_message);
        
         return "success";
    }


    public function store(Request $request)
    {
        $option=Option::where('key','cron_info')->first();
        $data=json_decode($option->value);
        $info['send_mail_to_will_expire_within_days']=$request->send_mail_to_will_expire_within_days;
        $info['expire_message']=$request->expire_message;
        $info['trial_expired_message']=$request->trial_expired_message;
        $info['alert_message']=$request->alert_message;
        $info['auto_approve']=$request->auto_approve;
        $option->value=json_encode($info);
        $option->save();

        return response()->json(['Job Updated !!']);
    }
}
