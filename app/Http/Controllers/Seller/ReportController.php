<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Order;
use App\Models\Currency;
class ReportController extends Controller
{
    public function index(Request $request)
    {
    	$user_id=seller_id();
    	$currency_info = \App\Useroption::where('user_id', $user_id)->where('key', 'currency')->first();
        $currency_info = json_decode($currency_info->value ?? '');
        if($currency_info){
             $default_currency['name'] = $currency_info->currency_default->currency_name;
             $default_currency['icon'] = $currency_info->currency_default->currency_icon;
        }else{
             $default_currency['name'] = env('DEFAULT_CURRENCY_NAME');
             $default_currency['icon'] = env('DEFAULT_CURRENCY_ICON');
        }
        
    	if ($request->start) {
    		
    		$start = date("Y-m-d",strtotime($request->start));
    		$end = date("Y-m-d",strtotime($request->end));

    		$total=Order::where('user_id',$user_id)->whereBetween('created_at',[$start,$end])->count();
    		$completed=Order::where('user_id',$user_id)->whereBetween('created_at',[$start,$end])->where('status','completed')->count();
    		$canceled=Order::where('user_id',$user_id)->whereBetween('created_at',[$start,$end])->where('status','canceled')->count();
    		$proccess=Order::where('user_id',$user_id)->whereBetween('created_at',[$start,$end])->where([
    			['status','!=','completed'],
    			['status','!=','canceled'],
    		])->count();


    		$amounts=Order::where([
    			['user_id',$user_id]
    		])->whereBetween('created_at',[$start,$end]);
    		$amounts_num = $this->calc_amount($amounts, $default_currency['name']);
    		
    		$amounts = $amounts_num;
    		$amount_cancel=Order::where([
    			['user_id',$user_id],
    			['status','canceled'],
    			['payment_status',0]
    		])->whereBetween('created_at',[$start,$end]);
    		$amount_cancel_num = $this->calc_amount($amount_cancel, $default_currency['name']);
    		$amount_cancel = $amount_cancel_num;
    		$amount_proccess=Order::where([
    			['user_id',$user_id],
    			['status','!=','completed'],
    			['status','!=','canceled'],
    			
    		])->whereBetween('created_at',[$start,$end]);
    		$amount_proccess_num = $this->calc_amount($amount_proccess, $default_currency['name']);
    		$amount_proccess = $amount_proccess_num;
    		$amount_completed=Order::where([
    			['user_id',$user_id],
    			['status','completed'],
    			['payment_status',1]
    		])->whereBetween('created_at',[$start,$end]);
    		$amount_completed_num = $this->calc_amount($amount_completed, $default_currency['name']);
    		$amount_completed = $amount_completed_num;


    		$orders=Order::where('user_id',$user_id)->whereBetween('created_at',[$start,$end])->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);

    	}
    	else{
    		$orders=Order::where([
    			['user_id',$user_id]
    		])->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);
    		
    		
    		$total=Order::where('user_id',$user_id)->count();
    		$completed=Order::where('user_id',$user_id)->where('status','completed')->count();
    		$canceled=Order::where('user_id',$user_id)->where('status','canceled')->count();
    		$proccess=Order::where('user_id',$user_id)->where([
    			['status','!=','completed'],
    			['status','!=','canceled'],
    		])->count();

    		$amounts=Order::where([
    			['user_id',$user_id]
    		])->get();

    		$amounts_num = $this->calc_amount($amounts, $default_currency['name']);
    		$amounts = $amounts_num;
    		$amount_cancel=Order::where([
    			['user_id',$user_id],
    			['status','canceled'],
    			['payment_status',0]
    		])->get();
    		$amount_cancel_num = $this->calc_amount($amount_cancel, $default_currency['name']);
    		$amount_cancel = $amount_cancel_num;
    		$amount_proccess=Order::where([
    			['user_id',$user_id],
    			['status','!=','completed'],
    			['status','!=','canceled'],

    		])->get();
    		$amount_proccess_num = $this->calc_amount($amount_proccess, $default_currency['name']);
    		$amount_proccess = $amount_proccess_num;
    		
    		$amount_completed=Order::where([
    			['user_id',$user_id],
    			['status','completed'],
    			['payment_status',1]
    		])->get();
    		$amount_completed_num = $this->calc_amount($amount_completed, $default_currency['name']);
    		$amount_completed = $amount_completed_num;
    		
    	}

    	$start=$request->start ?? '';
    	$end=$request->end ?? '';
    	return view('seller.report.index',compact('orders','start','end','total','completed','canceled','proccess','amounts','amount_cancel','amount_proccess','amount_completed','request', 'default_currency'));
    }
    
	public function order_total($query, $default_cur){
    	foreach($query as $q){
    	    $cur = json_decode($q->currency);
    	    $default = strtolower($default_cur);
    	    $rate = Currency::where('currency_id', $cur->currency_name)->first();
    	    $rate = $rate[$default];
    	    $total_ = $q->total / $rate;
    	    $q['total'] = $total_;
    	    $q['def_cur'] = $default_cur;
    	}
    }
    
    public function calc_amount($query, $default_cur){
	    $total_num = 0;
		foreach($query as $q){
		    $cur = json_decode($q->currency);
		    $default = strtolower($default_cur);
		    $rate = Currency::where('currency_id', $cur->currency_name)->first();
		    $rate = $rate[$default];
		    $total_ = $q->total / $rate;
		    $total_num = $total_num + $total_;
		}
		return $total_num;
	}
    // public function product_order($query, $default_curr){
    //     $rate = 
    //      $query["total"];
    // }
}