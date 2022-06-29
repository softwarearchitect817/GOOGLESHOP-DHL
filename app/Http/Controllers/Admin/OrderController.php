<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Userplan;
use App\Models\Userplanmeta;
use App\Models\User;
use App\Plan;
use App\Trasection;
use App\Domain;
use App\Mail\SubscriptionMail;
use Illuminate\Support\Facades\Mail;
use DB;
use App\Option;
class OrderController extends Controller
{

    protected $user_email;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth()->user()->can('order.list')) {
            return abort(401);
        }
        
        $type=$request->status ?? 'all';
        if ($request->status=='cancelled') {
           $type=0;
        }

        if (!empty($request->src) && $request->term=='email') {
            $this->user_email=$request->src;
            if ($type==='all') {
                 $posts=Userplan::whereHas('user',function($q){
                    return $q->where('email',$this->user_email);
                 })->with('user','plan_info','category')->latest()->paginate(40);
             }
             else{
                $posts=Userplan::whereHas('user',function($q){
                    return $q->where('email',$this->user_email);
                 })->with('user','plan_info','category')->where('status',$type)->latest()->paginate(40);
            }
        }
        elseif (!empty($request->src)) {
             if ($type==='all') {
                 $posts=Userplan::with('user','plan_info','category')->where($request->term,$request->src)->latest()->paginate(40);
             }
             else{
                $posts=Userplan::with('user','plan_info','category')->where($request->term,$request->src)->where('status',$type)->latest()->paginate(40);
            }
        }
        else{
          if ($type==='all') {
           $posts=Userplan::with('user','plan_info','category')->latest()->paginate(40);
          }
        else{
            $posts=Userplan::with('user','plan_info','category')->where('status',$type)->latest()->paginate(40);
          }  
        }

        return view('admin.order.index',compact('type','posts','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!Auth()->user()->can('order.create')) {
            return abort(401);
        }
        $payment_getway=\App\Category::where('type','payment_getway')->get();
        $posts=Plan::where('status',1)->get();
        $email=$request->email ?? '';
        return view('admin.order.create',compact('posts','email','payment_getway'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth()->user()->can('order.create')) {
            return abort(401);
        }
       
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'payment_method' => 'required',
            'transition_id' => 'required',
            'plan' => 'required',
            'notification_status' => 'required',
            
        ]);

        if ($request->notification_status == 'yes' && $request->content==null) {
             $msg['errors']['email_comment']='Email Comment Is Required';
             return response()->json($msg,401);
        }
        $user=User::where('email',$request->email)->where('role_id',3)->first();
        if (empty($user)) {
            $msg['errors']['user']='User Not Found';
            return response()->json($msg,401);
        }

        
        $plan=Plan::findorFail($request->plan);
        $exp_days =  $plan->days;
        $expiry_date = \Carbon\Carbon::now()->addDays(($exp_days))->format('Y-m-d');

        $max_order=Userplan::max('id');
        $order_prefix=Option::where('key','order_prefix')->first();
        $tax=Option::where('key','tax')->first();
        $tax= ($plan->price / 100) * $tax->value;
        $order_no = $order_prefix->value.$max_order;

        $order=new Userplan;
        $order->order_no=$order_no;
        $order->amount=$plan->price;
        $order->tax=$tax;
        $order->trx=$request->transition_id;
        $order->will_expire=$expiry_date;
        $order->user_id=$user->id;
        $order->plan_id=$plan->id;
        $order->category_id=$request->payment_method;
        $order->payment_status = 1;
        $order->status=1;
        $order->save();

        $dom=Domain::where('user_id',$user->id)->first();
        $dom->data=$plan->data;
        $dom->userplan_id=$order->id;
        $dom->will_expire=$expiry_date;
        $dom->is_trial=0;
        $dom->save();


        $dom->orderlog()->create(['userplan_id'=>$order->id,'domain_id'=>$dom->id]);


        

         if ($request->notification_status == 'yes'){
            $data['info']=Userplan::with('plan_info','category','user')->find($order->id);
            $data['comment']=$request->content;
            $data['to_vendor']='vendor';
            if(env('QUEUE_MAIL') == 'on'){
             dispatch(new \App\Jobs\SendInvoiceEmail($data));
            }
            else{
             Mail::to($user->email)->send(new SubscriptionMail($data));
            }
         }

        return response()->json(['Order Created Successfully']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth()->user()->can('order.view')) {
            return abort(401);
        }
        $info=Userplan::with('plan_info','category','user')->findorFail($id);
        $user=User::with('user_domain')->find($info->user->id);
       
        return view('admin.order.show',compact('info','user'));
    }

    /**
     * print invoice the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $info=Userplan::with('plan_info','category','user')->findorFail($id);
        $user=User::with('user_domain')->find($info->user->id);
        $company_info=\App\Option::where('key','company_info')->first();
        $company_info=json_decode($company_info->value);
        $pdf = \PDF::loadView('email.subscription_invoicepdf',compact('company_info','info','user'));
        return $pdf->download('invoice.pdf');
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth()->user()->can('order.edit')) {
            return abort(401);
        }

        $info= Userplan::find($id);
        $payment_getway=\App\Category::where('type','payment_getway')->get();
        $posts=Plan::get();

        return view('admin.order.edit',compact('posts','info','payment_getway'));
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
         if ($request->notification_status == 'yes' && $request->content==null) {
             $msg['errors']['email_comment']='Email Comment Is Required';
             return response()->json($msg,401);
        }

        DB::beginTransaction();
        try {

        $order=Userplan::findorFail($id);
        $order->plan_id =$request->plan;
        $order->order_no=$request->order_no;
        $order->amount=$request->amount;
        $order->tax=$request->tax;
        $order->trx=$request->trx;
        $order->status=$request->order_status;
        $order->category_id =$request->category_id;
        $order->payment_status=$request->payment_status;
        $order->save();
        $user=User::find($order->user_id);
        
        if($request->subscription_status == 1){
                $plan=Plan::find($order->plan_id);
                
                $exp_days =  $plan->days;
                $expiry_date = \Carbon\Carbon::now()->addDays(($exp_days))->format('Y-m-d');
                $dom=Domain::where('user_id',$order->user_id)->first();
                $dom->data=$plan->data;
                $dom->userplan_id=$order->id;
                $dom->will_expire=$expiry_date;
                $dom->is_trial=0;
                $dom->save();
                $dom->orderlog()->create(['userplan_id'=>$order->id,'domain_id'=>$dom->id]);

        }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        if ($request->notification_status == 'yes'){
            $data['info']=Userplan::with('plan_info','category','user')->find($order->id);
            $data['comment']=$request->content;
            $data['to_vendor']='vendor';
            if(env('QUEUE_MAIL') == 'on'){
             dispatch(new \App\Jobs\SendInvoiceEmail($data));
            }
            else{
             Mail::to($user->email)->send(new SubscriptionMail($data));
            }
        }

        return response()->json(['Order Updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!Auth()->user()->can('order.delete')) {
            return abort(401);
        }

        if ($request->ids && !empty($request->method)) {
            if ($request->method=='delete') {
                foreach ($request->ids as $key => $id) {
                    $order=Userplan::find($id);
                    $order->delete();
                }
            }
            else{
                if ($request->method=='cancelled') {
                    $status=0;
                }
                else{
                     $status=$request->method;
                }
                foreach ($request->ids as $key => $id) {
                    $order=Userplan::find($id);
                    $order->status=$status;
                    $order->save();
                }
            }
        }

        return response()->json(['Success']);
    }
}
