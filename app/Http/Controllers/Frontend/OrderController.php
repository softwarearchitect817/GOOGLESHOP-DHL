<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Customer;
use App\Models\Userorder;
use App\Order;
use App\Orderitem;
use App\Useroption;
use App\Ordermeta;
use App\Ordershipping;
use App\Category;
use App\Trasection;
use Cart;
use Hash;
use Session;
use App\Mail\SellerOrderMail;
use Illuminate\Support\Facades\Mail;
use App\Helper\Order\Paypal;
use App\Helper\Order\Instamojo;
use App\Helper\Order\Toyyibpay;
use App\Helper\Order\Stripe;
use App\Helper\Order\Mollie;
use App\Helper\Order\Paystack;
use App\Helper\Order\Mercado;
use Cache;
use App\Models\Userplanmeta;
use Str;
use DB;
class OrderController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      if(Cart::count() == 0){
        return back();
      }

        $validated = $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:100',
            'phone' => 'required|max:20',
            'shipping_mode' => 'required'
        ],
        [
            'shipping_mode.required' => 'You cannot proceed without choosing a delivery method.'
        ]
        );
        $shop_type=domain_info('shop_type');
        $domain_id=domain_info('domain_id');
        $user_id=domain_info('user_id');

        if($shop_type == 1){
            $validated = $request->validate([
                'location' => 'required',
                'shipping_mode' => 'required',
                'delivery_address' => 'required|max:100',
                'zip_code' => 'required|max:50',
                
            ],
            [
                'shipping_mode.accepted' => 'You cannot proceed without choosing a delivery method.'
            ]);
        }

        if($request->create_account == 1){
           $validated = $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|min:8',
          ]);
           $check_is_exist=Customer::where('email',$request->email)->where('created_by',domain_info('user_id'))->first();
           if (!empty($check_is_exist)) {
            Session::flash('user_limit','Opps email address already exists');
            
            return back();
           }
           
           $user_limit=domain_info('customer_limit',0);
           
           $total_customers=Customer::where('created_by',$user_id)->count();

           if($user_limit <= $total_customers){
            Session::flash('user_limit','Opps something wrong with registration but you can make order');
            Session::put('registration',false);
            return back();
           }
            else{
             Session::forget('registration');
            }

           $user= new Customer();
           $user->email=$request->email;
           $user->name=$request->name;
           $user->password=Hash::make($request->password);
           $user->domain_id=$domain_id;
           $user->created_by=$user_id;
           $user->save();
           Auth::guard('customer')->loginUsingId($user->id);
        }
       
        $prefix=Useroption::where('user_id',$user_id)->where('key','order_prefix')->first();
        $max_id=Order::max('id');
        if (empty($prefix)) {
          $prefix=$max_id+1;
        }
        else{
         $prefix=$prefix->value.$max_id;
       }

       $shipping_amount=Category::where('user_id',$user_id)->where('type','method')->find($request->shipping_mode);
       if ($request->payment_method == 2) {
         $payment_id=Str::random(10);
       }
       else{
        $payment_id=null;
       }

       DB::beginTransaction();
       try {

         
        $currency_info = currency_info();
        $currencies = $currency_info['currency_name'];
        $currency_default = $currency_info['currency_default'];
        $currency_icon = '';
        if(Session::get('to_currency') == $currency_default->currency_name){
            $currency_icon = $currency_default->currency_icon;
        }else {
            foreach($currencies as $key => $value){
                if($key == Session::get('to_currency')){
                    $currency_icon = $value;
                }
            }
        }
        
        $currency = ['currency_name' => Session::get('to_currency'), 'currency_icon' => $currency_icon];
        
       $order=new Order;
       $order->order_no=$prefix;
       if(Auth::guard('customer')->check()){
        $order->customer_id=Auth::guard('customer')->user()->id;
       }
       
       $order->user_id  =$user_id;
       $order->order_type  =$shop_type;
       $order->payment_status=2;
       $order->status='pending';
       $order->transaction_id =$payment_id;
       $order->category_id =$request->payment_method;
       $order->payment_status=2;
       $order->tax=Cart::tax() * Session::get('rate_base');
       $order->shipping=$this->calculateWeight(Cart::weight(),$shipping_amount->slug ?? 0) * Session::get('rate_base');
       $order->total=$this->calculateShipping(Cart::total(),$shipping_amount->slug ?? 0,Cart::weight()) * Session::get('rate_base');
       $order->currency=json_encode($currency);
       $order->save();

       $info['name']=$request->name;
       $info['email']=$request->email;
       $info['phone']=$request->phone;
       $info['comment']=$request->comment;
       $info['address']=$request->delivery_address;
       $info['zip_code']=$request->zip_code;
       $info['coupon_discount']=Cart::discount() * Session::get('rate_base');
       $info['sub_total']=Cart::subtotal() * Session::get('rate_base');
       $info['currency']=$currency;
       
       $meta=new Ordermeta;
       $meta->order_id=$order->id;
       $meta->key='content';
       $meta->value=json_encode($info);
       $meta->save();

       $items=[];

       foreach (Cart::content() as $key => $row) {
        $options['attribute']= $row->options->attribute;
        $options['options']= $row->options->options;

        $data['order_id']=$order->id;
        $data['term_id']=$row->id;
        $data['info']=json_encode($options);
        $data['qty']=$row->qty;
        $data['amount']=$row->price * Session('rate_base');
        $data['currency'] = json_encode($info['currency']);
        array_push($items, $data);
      }

      Orderitem::insert($items);

      if($request->location){
        $ship['order_id']=$order->id;
        $ship['location_id']=$request->location;
        $ship['shipping_id']=$request->shipping_mode;
        Ordershipping::insert($ship);
      }

      DB::commit();
     } catch (Exception $e) {
      DB::rollback();
     }
      
      Session::put('order_no',$order->order_no);
      if($request->payment_method != 2){
        $payment_data['ref_id']=$order->id;
        $payment_data['getway_id']=$request->payment_method;
        $payment_data['amount']=$order->total;
        $payment_data['email']=$request->email;
        $payment_data['name']=$request->name;
        $payment_data['phone']=$request->phone;
        $payment_data['billName']='Order No :'.$order->order_no;
        Session::put('customer_order_info',$payment_data);
      
        if($request->payment_method == 5){
          try{
            return Paypal::make_payment($payment_data);
          }
          catch(Exception $e){
             Order::destroy($order->id);
             return $this->payment_fail();
          }
        // Session::put('paypal_payment',true);
        // return redirect('/payment-with-paypal');
        //   return Paypal::make_payment($payment_data);
        }
        if($request->payment_method == 3){
          try{
             return Instamojo::make_payment($payment_data);
          }

          catch(Exception $e){
             Order::destroy($order->id);
             return $this->payment_fail();
          } 
        }
        if($request->payment_method == 7){
          try{
            return Toyyibpay::make_payment($payment_data);
          }
          catch(Exception $e){
             Order::destroy($order->id);
             return $this->payment_fail();
          }
        }
        if($request->payment_method == 8){
          try{
             return Mollie::make_payment($payment_data);
          }
          catch(Exception $e){
             Order::destroy($order->id);
             return $this->payment_fail();
          }
         
        }
        if($request->payment_method == 6){
          Session::put('stripe_payment',true);
          return redirect('/payment-with-stripe');
        }
        if($request->payment_method == 4){
          Session::put('razorpay_payment',true);
          return redirect('/payment-with-razorpay');
        }
        // if($request->payment_method == 9){
        //   Session::put('paystack_payment',true);
        //   return redirect('/payment-with-paystack');
        // }
        if($request->payment_method == 10){
         try{
             return Mercado::make_payment($payment_data);
          }
          catch(Exception $e){
             Order::destroy($order->id);
             return $this->payment_fail();
          }
        }
        
      }
        

      try{
          if(Cache::has(domain_info('user_id').'store_email')){
            $store_email=Cache::get(domain_info('user_id').'store_email');  
          }
          else{
            
            $admin=User::findorFail($user_id);
            $store_email=$admin->email;
          }

          $mail_data['store_email']=$store_email;
          $mail_data['order_no']=$prefix;
          $mail_data['base_url']=url('/');
          $mail_data['site_name']=Cache::get(domain_info('user_id').'shop_name',env('APP_NAME'));
          $mail_data['order_url']= url('/seller/order',$order->id);

          if(env('QUEUE_MAIL') == 'on'){
           
           dispatch(new \App\Jobs\Ordernotification($mail_data));
         }
         else{
          
           Mail::to($store_email)->send(new SellerOrderMail($mail_data));
         }
      }
      catch(Exception $e){
       
      }

      Cart::destroy();
      
       if(Cache::has(domain_info('user_id').'order_receive_method')){
                $method=Cache::get(domain_info('user_id').'order_receive_method');
                
            }
            else{
                $method="email";
            }
            
            if($method == 'whatsapp'){
               if(Cache::has(domain_info('user_id').'whatsapp')){
                    $whatsapp=json_decode(Cache::get(domain_info('user_id').'whatsapp'));
                    $url="https://wa.me/+".$whatsapp->phone_number."?text=My Order No Is ".str_replace('#','',$order->order_no);
                    return redirect($url);
            }
           
        }
       
        
      return redirect('/thanks');
      
    }

    public function payment_success(){
      
      if (Session::has('customer_payment_info')) {
      
        $data= Session::get('customer_payment_info');

        $order=Order::findorFail($data['ref_id']);
        $order->transaction_id = $data['payment_id'];
        $order->category_id=$data['getway_id'];
        if (isset($data['payment_status'])) {
          $order->payment_status = $data['payment_status'];
        }
        else{
           $order->payment_status = 1;
        }
       
        $order->save();
        Session::forget('customer_payment_info');
        Cart::destroy();


        
            if(Cache::has(domain_info('user_id').'store_email')){
              $store_email=Cache::get(domain_info('user_id').'store_email');  
             
            }
            else{
              
              $admin=User::findorFail(domain_info('user_id'));
              $store_email=$admin->email;
            }
            

            $mail_data['store_email']=$store_email;
            $mail_data['order_no']=$order->order_no;
            $mail_data['base_url']=url('/');
            $mail_data['site_name']=Cache::get(domain_info('user_id').'shop_name',null);
            $mail_data['order_url']= url('/seller/order',$order->id);
            
            if(Cache::has(domain_info('user_id').'order_receive_method')){
                $method=Cache::get(domain_info('user_id').'order_receive_method');
                
            }
            else{
                $method="email";
            }
            
            if($method == 'email'){
               if(env('QUEUE_MAIL') == 'on'){
            
                dispatch(new \App\Jobs\Ordernotification($mail_data));
               }
              else{
            
                Mail::to($store_email)->send(new SellerOrderMail($mail_data));
               } 
                return redirect('/thanks');
            }
            else{
                if(Cache::has(domain_info('user_id').'whatsapp')){
                    $whatsapp=json_decode(Cache::get(domain_info('user_id').'whatsapp'));
                    $url="https://wa.me/+".$whatsapp->phone_number."?text=My Order No Is ".str_replace('#','',$order->order_no);
                    return redirect($url);
                }
               if(env('QUEUE_MAIL') == 'on'){
            
                dispatch(new \App\Jobs\Ordernotification($mail_data));
               }
                else{
            
                Mail::to($store_email)->send(new SellerOrderMail($mail_data));
               } 
                return redirect('/thanks');
                
            }
            
            
            
            
           
         
        
       
      }

      abort(404);
     
    }

    public function payment_fail(){
      Session::flash('payment_fail','Sorry Transaction Failed');

      return redirect('/checkout');
    }


    public function calculateShipping($total,$shipping_amount,$weight)
    {
        $shipping_amount=(float)$shipping_amount;
        $totalAmount=$total;

        $weight_amount=$this->calculateWeight($weight,$shipping_amount);
        $amount=$totalAmount+$weight_amount;

        return $amount;

    }

    public function calculateWeight($weight,$amount)
    {
        return $amount; 
    }

}
