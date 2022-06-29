<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Order;
use App\Ordermeta;
use App\Orderitem;
use App\Ordershipping;
use App\Term;
use App\Attribute;
use App\Category;
use App\User;
use App\Models\Customer;
use App\Useroption;
use App\Stock;
use Cart;
use Carbon\Carbon;
use App\Mail\CustomerOrderMail;
use Illuminate\Support\Facades\Mail;
use App\Getway;
use App\Models\Currency;
class OrderController extends Controller
{
    protected $user_id; 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$type)
    {
      
        $user_id=seller_id();

        if ($type=='all') {
            if (!empty($request->src)) {
              
                $orders=Order::where([
                    ['user_id',$user_id],
                    ['order_no',$request->src]
                ])->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);

            }
            elseif(!empty($request->payment_status) && !empty($request->status) && !empty($request->start)  && !empty($request->end)){
                $arr['user_id']=$user_id;
                $arr['status']=$request->status;
                if ($request->payment_status=='cancel') {
                    $arr['payment_status']=0;
                }
                else{
                    $arr['payment_status']=$request->payment_status;
                }
                
               
                $start = date("Y-m-d",strtotime($request->start));
                $end = date("Y-m-d",strtotime($request->end));


                $orders=Order::where($arr)->whereBetween('created_at',[$start,$end])->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);

            }
            else{

                $orders=Order::where('user_id',$user_id)->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);
            }
           
        }
        else{
             if (!empty($request->src)) {
                $orders=Order::where([
                    ['user_id',$user_id],
                    ['order_no',$request->src]
                ])->where('status',$type)->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);
              }
              elseif(!empty($request->payment_status) && !empty($request->status) && !empty($request->start)  && !empty($request->end)){
                $arr['user_id']=$user_id;
                $arr['status']=$request->status;
                if ($request->payment_status=='cancel') {
                    $arr['payment_status']=0;
                }
                else{
                    $arr['payment_status']=$request->payment_status;
                }
                
               
                $start = date("Y-m-d",strtotime($request->start));
                $end = date("Y-m-d",strtotime($request->end));

                $orders=Order::where($arr)->whereBetween('created_at',[$start,$end])->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);

              }
              else{
                $orders=Order::where('user_id',$user_id)->where('status',$type)->with('customer')->withCount('order_items')->orderBy('id','DESC')->paginate(40);
              }  
            
        }
        

        $pendings=Order::where('user_id',$user_id)->where('status','pending')->count();
        $processing=Order::where('user_id',$user_id)->where('status','processing')->count();
        $pickup=Order::where('user_id',$user_id)->where('status','ready-for-pickup')->count();
        $completed=Order::where('user_id',$user_id)->where('status','completed')->count();
        $canceled=Order::where('user_id',$user_id)->where('status','canceled')->count();
        $archived=Order::where('user_id',$user_id)->where('status','archived')->count();

        $type=$type;
        return view('seller.orders.index',compact('orders','pendings','processing','pickup','completed','archived','canceled','request','type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $plan=user_limit();
        $google_analytics=filter_var($plan['pos']);
            if($google_analytics == false){
               $msg='This module did not support your subscription.';
               \Session::flash('error',$msg);
               return back();
               
        } 

        if (!empty($request->search)) {
          $posts=Term::with('preview','price','attributes','options','stock')->where('id',$request->search)->where('user_id',seller_id())->where('type','product')->where('status',1)->latest()->paginate(40);
        }
        else{
          $posts=Term::with('preview','price','attributes','options','stock')->where('user_id',seller_id())->where('type','product')->where('status',1)->latest()->paginate(40);
        }
        $src=$request->src ?? '';
        return view('seller.orders.create',compact('posts','src'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id=seller_id();
        $this->user_id=$user_id;
        $option=$request->option ?? [];
        $variation=$request->variation ?? [];
       
       $term=Term::where('user_id',$this->user_id)->with('price','preview')->where('id',$request->term_id);
       if($request->option != null){
        $term=$term->with('termoption',function($q) use ($option){
            if(count($option) > 0){
            return $q->whereIn('id',$option);
            }
            else{
                return $q;
            }
            });
       }
       if($request->variation){
        $term=$term->with('attributes',function($q) use ($variation){
            if(count($variation) > 0){
             return $q->whereIn('id',$variation);
            }
            else{
                return $q;
            }
         
        });
       }
       $term= $term->first();
       if(!empty($term)){
           $price=$term->price->price;
           if($request->option != null){
            foreach($term->termoption ?? [] as $row){
                if($row->amount_type == 1){
                 $price= $price+$row->amount;
                }
                else{
                 $percent= $price * $row->amount / 100;
                 $price= $price+$percent;
                }
            }
            $options=$term->termoption;
           }
           else{
            $options= [];
           }

           if($request->variation != null){
            $attributes=$term->attributes ?? [];
           }
           else{
            $attributes= [];
           }
          
           $price=$price;
         
        
           Cart::add($term->id,$term->title, $request->qty,$price,0,['attribute' => $attributes,'options'=>$options,'preview' => $term->preview->media->url ?? asset('uploads/default.png')]);
           Cart::setGlobalTax(tax());
       }

        
        $data['count']=Cart::count();
        $data['subtotal']=Cart::subtotal();
        $data['priceTotal']=Cart::priceTotal();
        $data['total']=Cart::total();
        $data['tax']=Cart::tax();
        $data['items']=Cart::content();
        
        return $data;
       
    }

    public function cartRemove($id)
    {
       
        Cart::remove($id);
        return back();
    }

    public function apply_coupon(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|max:50',
         ]);

        $code=Category::where('user_id',seller_id())->where('type','coupon')->where('name',$request->code)->first();
        if (empty($code)) {
           $error['errors']['error']='Coupon Code Not Found.';
           return response()->json($error,404);
        }
        $mydate= Carbon::now()->toDateString();
        if ($code->slug >= $mydate) {
            Cart::setGlobalDiscount($code->featured);

            return response()->json(['Coupon Applied']);
        }

        $error['errors']['error']='Sorry, this coupon is expired';
        return response()->json($error,401);



    }

    public function make_order(Request $request)
    {
        $user_id=seller_id();
        
        if($request->customer_type == 1){
            $user=Customer::where('created_by',$user_id)->where('email',$request->email)->first();
            if (empty($user)) {
                $error['errors']['error']='Sorry, Customer Not Exist';
                return response()->json($error,401);
            }
            $customer_id=$user->id;
        }
        else{
            $customer_id=null;
        }

        if(Cart::count() == 0){
          return response()->json('Cart empty');
        }

        if ($request->delivery_type=='1') {
          
            $validatedData = $request->validate([
            'email' => 'required|email|max:50',
            'name' => 'required|max:50',
            'phone' => 'required|max:50',
            'address' => 'required|max:100',
            'location' => 'required',
            'zip_code' => 'required',
            'shipping_method' => 'required',
            'payment_method' => 'required',
            'payment_id' => 'required|max:100',
            ]);
            
           
            $prefix=Useroption::where('user_id',$user_id)->where('key','order_prefix')->first();
            $max_id=Order::max('id');
            if (empty($prefix)) {
                $prefix=$max_id+1;
            }
            else{
                 $prefix=$prefix->value.$max_id;
            }

            $shipping_amount=Category::where('user_id',$user_id)->where('type','method')->find($request->shipping_method);
            $order=new Order;
            $order->order_no=$prefix;
            $order->transaction_id =$request->payment_id;
            $order->category_id =$request->payment_method;
            $order->customer_id =$customer_id;
            $order->user_id  =$user_id;
            $order->order_type  =$request->delivery_type;
            $order->payment_status=$request->payment_status;
            $order->status='pending';
            $order->tax=Cart::tax();
            $order->shipping=$this->calculateWeight(Cart::weight(),$shipping_amount->slug);
            $order->total=$this->calculateShipping(Cart::total(),$shipping_amount->slug,Cart::weight());
            $order->save();

            $info['name']=$request->name;
            $info['email']=$request->email;
            $info['phone']=$request->phone;
            $info['comment']=$request->comment;
            $info['address']=$request->address;
            $info['zip_code']=$request->zip_code;
            $info['coupon_discount']=Cart::discount();
            $info['sub_total']=Cart::subtotal();


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
                $data['amount']=$row->price;
                array_push($items, $data);
            }

            Orderitem::insert($items);

            $ship['order_id']=$order->id;
            $ship['location_id']=$request->location;
            $ship['shipping_id']=$request->shipping_method;

            Ordershipping::insert($ship);

            Cart::destroy();

            return response()->json(['Order Created']);

        }
        else{
           
           $validatedData = $request->validate([
            'email' => 'required|email|max:50',
            'name' => 'required|max:50',
            'payment_method' => 'required',
            'payment_id' => 'required|max:100',
           ]);
           $user_id=seller_id();
           $user=Customer::where('created_by',$user_id)->where('email',$request->email)->first();
           if (empty($user)) {
            $error['errors']['error']='Sorry, Customer Not Exist';
            return response()->json($error,401);
           }
           $prefix=Useroption::where('user_id',$user_id)->where('key','order_prefix')->first();
           $max_id=Order::max('id');
           if (empty($prefix)) {
               $prefix=$max_id+1;
           }
           else{
                $prefix=$prefix->value.$max_id;
           }


            $order=new Order;
            $order->order_no=$prefix;
            $order->transaction_id =$request->payment_id;
            $order->category_id =$request->payment_method;
            $order->customer_id =$customer_id;
            $order->user_id  = $user_id;
            $order->order_type  = $request->delivery_type;
            $order->payment_status=$request->payment_status;
            $order->status='pending';
            $order->tax=Cart::tax();
            $order->total=Cart::total();
            $order->save();

            $info['name']=$request->name;
            $info['email']=$request->email;
            $info['comment']=$request->comment;
            $info['coupon_discount']=Cart::discount();
            $info['sub_total']=Cart::subtotal();


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
                $data['amount']=$row->price;
                array_push($items, $data);
            }

            Orderitem::insert($items);
            Cart::destroy();
            return response()->json(['Order Created']);
        }
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
    public function checkout()
    {
       $posts=Getway::where('user_id',seller_id())->where('status',1)->get();;
       return view('seller.orders.checkout',compact('posts'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id = seller_id();
        
        $info=Order::where('user_id',seller_id())->with('order_item','customer','order_content','shipping_info','getway')->findorFail($id);
        $order_content=json_decode($info->order_content->value);
        
        return view('seller.orders.show',compact('info','order_content'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_id=seller_id();
        
        $order=Order::where('user_id',$user_id)->with('order_item_with_stock','order_item_with_file','customer','order_content','shipping_info')->findorFail($id);
        
        if ($request->status) {
            $order->status=$request->status;
        }
        $order->payment_status=$request->payment_status ?? 0;
        $order->save();

       
        if($request->status == 'completed'){
          
            foreach($order->order_item_with_stock as $row){
              // dd($row);
                if($row->stock != null){
                    $available_qty=$row->stock->stock_qty;
                    $order_qty=$row->qty;
                    if($order_qty >= $available_qty){
                        $final_qty=0;
                        $final_stock_status=0;
                    }
                    else{
                        $final_qty=$available_qty-$order_qty;
                        $final_stock_status=1;
                    }
                  
                    $stock=Stock::find($row->stock->id);
                    $stock->stock_status=$final_stock_status;
                    $stock->stock_qty=$final_qty;
                    $stock->save();


                }
            }
        }
       
        if (!empty($request->mail_notify)) {
           

           $location=Useroption::where('key','location')->where('user_id',$user_id)->first();
           $store_email=Useroption::where('key','store_email')->where('user_id',$user_id)->first();
           $location=json_decode($location->value ?? '');

           $order_content=json_decode($order->order_content->value ?? '');
           $data['order']=$order;
           $data['location']=$location;
           $data['order_content']=$order_content;
           $data['url']=my_url();
           $data['customer_email']=$order_content->email ?? '';
           $data['admin_email']=$store_email->value ?? Auth::user()->email;
            if(!empty($order_content->email)){
              if(env('QUEUE_MAIL') == 'on'){
               dispatch(new \App\Jobs\SendInvoiceEmail($data));
              }
             else{
              Mail::to($data['customer_email'])->send(new CustomerOrderMail($data));
             }
            }
           
           
        }

        return response()->json(['Order Updated']);

       
    }

    public function invoice($id)
    {
        $user_id=seller_id();
        $order=Order::where('user_id',seller_id())->with('order_item','customer','order_content','shipping_info')->findorFail($id);

        $location=\App\Useroption::where('key','location')->where('user_id',$user_id)->first();
        $location=json_decode($location->value ?? '');

        $order_content=json_decode($order->order_content->value ?? '');
        $pdf = \PDF::loadView('email.invoice',compact('order','order_content','location'));
        return $pdf->download('invoice.pdf');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $auth_id=seller_id();
        if($request->method == 'delete'){
            if ($request->ids) {
                foreach ($request->ids as $id) {
                 $order= Order::where('user_id',$auth_id)->findorFail($id);
                 $order->delete();
              }
            }
        }
        else{
            if ($request->ids) {
                foreach ($request->ids as $id) {
                 $order= Order::where('user_id',$auth_id)->findorFail($id);
                 $order->status=$request->method;
                 $order->save();
              }
            }
        }

        return response()->json(['Success']);

    }
    
    
    public function order_total($query, $default_cur){
    	foreach($query as $row){
    	    $cur = json_decode($row["currency"]);
    	    $default = strtolower($default_cur);
    	    $rate = Currency::where('currency_id', $cur->currency_name)->first();
    	    $rate = $rate[$default];
    	    $total_ = $row['amount'] / $rate;
    	    $row['amount'] = $total_;
    	   // $row['def_cur'] = $default_cur;
    	}
    }
}