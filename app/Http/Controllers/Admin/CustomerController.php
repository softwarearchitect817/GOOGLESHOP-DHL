<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Userplan;
use App\Trasection;
use App\Domain;
use App\Plan;
use App\Term;
use App\Option;
use Hash;
use App\Models\Userplanmeta;
use App\Models\Customer;
use DB;
class CustomerController extends Controller
{
    protected $request;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth()->user()->can('customer.list')) {
            return abort(401);
        }
        $type=$request->type ?? 'all';
        if ($type=="trash") {
           $type=0;
        }
        //return $request;
        if (!empty($request->src) && $request->term=="domain") {
            $this->request=$request->src;
            if ($type === 'all') {

              $posts=User::where('role_id',3)->whereHas('user_domain',function($q){
                return $q->where('domain',$this->request);
            })->with('user_domain','user_plan')->latest()->paginate(40);
            }
            else{
                $posts=User::where('role_id',3)->where('status',$type)->whereHas('user_domain',function($q){
                    return $q->where('domain',$request->src);
                })->with('user_domain','user_plan')->where('status',$type)->latest()->paginate(40);
            }
            
        }
        elseif (!empty($request->src) && !empty($request->term)) {
             if ($type === 'all') {
             $posts=User::where('role_id',3)->with('user_domain','user_plan')->where($request->term,$request->src)->latest()->paginate(40);
             }
             else{
                $posts=User::where('role_id',3)->where('status',$type)->with('user_domain','user_plan')->where($request->term,$request->src)->latest()->paginate(40);
             }
        }
        else{  
           if ($type === 'all') { 
            $posts=User::where('role_id',3)->with('user_domain','user_plan')->latest()->paginate(40);
           }
           else{
             $posts=User::where('role_id',3)->where('status',$type)->with('user_domain','user_plan')->latest()->paginate(40);
           }
        }


        $all=User::where('role_id',3)->count();
        $actives=User::where('role_id',3)->where('status',1)->count();
        $suspened=User::where('role_id',3)->where('status',2)->count();
        $trash=User::where('role_id',3)->where('status',0)->count();
        $requested=User::where('role_id',3)->where('status',4)->count();
        $pendings=User::where('role_id',3)->where('status',3)->count();
        return view('admin.customer.index',compact('posts','request','type','all','actives','suspened','trash','requested','pendings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth()->user()->can('customer.create')) {
            return abort(401);
        }

        return view('admin.customer.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|unique:users|email|max:255',
            'name' => 'required',
            'password' => 'required',
            'plan' => 'required',
            'domain_name' => 'required|max:100|unique:domains,domain',
            'full_domain' => 'required|max:100|unique:domains,full_domain',
        ]);

       
        $info=Plan::find($request->plan);
        


         DB::beginTransaction();
        try {
        $user=new User;
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->role_id=3;
        $user->status=1;
        $user->save();

        $exp_days =  $info->days;
        $expiry_date = \Carbon\Carbon::now()->addDays(($exp_days))->format('Y-m-d');

        $max_order=Userplan::max('id');
        $order_prefix=Option::where('key','order_prefix')->first();


        $order_no = $order_prefix->value.$max_order;

        $tax=Option::where('key','tax')->first();
        $tax= ($info->price / 100) * $tax->value;

         $userplan = new Userplan;
         $userplan->order_no=$order_no;
         $userplan->amount=$info->amount;
         $userplan->tax=$tax;
         $userplan->trx=$request->trasection_id;
         $userplan->will_expire=$expiry_date;
         $userplan->user_id=$user->id;
         $userplan->plan_id=$info->id;
         $userplan->category_id=$request->trasection_method;
         $userplan->status=1;
         $userplan->payment_status=1;
         $userplan->save();

        

        $dom=new Domain;
        $dom->domain=$request->domain_name;
        $dom->full_domain=$request->full_domain;
        $dom->status=1;
        $dom->user_id=$user->id;
        $dom->is_trial=$info->is_trial;
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




        return response()->json(['Customer Created Successfully']);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       if (!Auth()->user()->can('customer.view')) {
            return abort(401);
       }

       $info=User::withCount('term','orders','customers')->where('role_id',3)->with('user_domain','user_plan')->findorFail($id);
       $histories=Userplan::with('plan_info','category')->where('user_id',$id)->latest()->paginate(20);

        $customers=Customer::withCount('orders')->where('created_by',$id)->latest()->paginate(20);
        $posts=\App\Term::where('user_id',$id)->latest()->paginate(40);
       return view('admin.customer.show',compact('info','histories','customers','posts'));
    }

    public function planview($id)
    {
       if (!Auth()->user()->can('customer.edit')) {
            return abort(401);
       }

       $info=User::withCount('term','orders','customers')->where('role_id',3)->findorFail($id);
      
       $domain=Domain::where('user_id',$id)->first();
       $planinfo=json_decode($domain->data);
       abort_if(empty($planinfo),404);
       
       return view('admin.customer.planinfo',compact('info','planinfo','domain'));
    }

    public function updateplaninfo(Request $request, $id)
    {
        $plan_data['product_limit']=$request->product_limit;
        $plan_data['customer_limit']=$request->customer_limit;
        $plan_data['storage']=$request->storage;
        $plan_data['custom_domain']=$request->custom_domain;
        $plan_data['inventory']=$request->inventory;
        $plan_data['pos']=$request->pos;
        $plan_data['customer_panel']=$request->customer_panel;
        $plan_data['pwa']=$request->pwa;
        $plan_data['whatsapp']=$request->whatsapp;
        $plan_data['live_support']=$request->live_support;
        $plan_data['qr_code']=$request->qr_code;
        $plan_data['facebook_pixel']=$request->facebook_pixel;
        $plan_data['custom_css']=$request->custom_css;
        $plan_data['custom_js']=$request->custom_js;
        $plan_data['gtm']=$request->gtm;
        $plan_data['location_limit']=$request->location_limit;
        $plan_data['category_limit']=$request->category_limit;
        $plan_data['brand_limit']=$request->brand_limit;
        $plan_data['variation_limit']=$request->variation_limit;
        $plan_data['google_analytics']=$request->google_analytics;

       $domain=Domain::findorFail($id);
       $domain->data=json_encode($plan_data);
       $domain->save();

       return response()->json('Info Updated Successfully');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       if (!Auth()->user()->can('customer.edit')) {
            return abort(401);
        }

        $info=User::findorFail($id);
        return view('admin.customer.edit',compact('info'));
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
         $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|max:100|email|unique:users,email,' . $id,
        ]);

         $user=User::findorFail($id);
         $user->name=$request->name;
         $user->email=$request->email;
         if ($request->password) {
             $user->password=Hash::make($request->password);
         }
         $user->status=$request->status;
         $user->save();

         return response()->json(['User Updated Successfully']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!Auth()->user()->can('customer.delete')) {
            return abort(401);
        }

        if ($request->type=="term_delete") {
            foreach ($request->ids ?? [] as $key => $id) {
                \App\Term::destroy($id);
            }
        }
        elseif ($request->type=="user_delete") {
            foreach ($request->ids ?? [] as $key => $id) {
                \App\Models\Customer::destroy($id);
            }
        }
        else{
            if (!empty($request->method)) {
                if ($request->method=="delete") {
                    foreach ($request->ids ?? [] as $key => $id) {
                       \File::deleteDirectory('uploads/'.$id);
                       $user=User::destroy($id);
                    }
                }
                else{
                    foreach ($request->ids ?? [] as $key => $id) {
                       $user=User::find($id);
                       if ($request->method=="trash") {
                          $user->status=0;
                       }
                       else{
                        $user->status=$request->method;
                       }
                       $user->save();
                    }
                }
            }

        }

        return response()->json(['Success']);


    }
}
