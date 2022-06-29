<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Getway;
use Auth;
class GetwayController extends Controller
{
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $getway=Category::where('type','payment_getway')->with('active_getway')->findorFail($request->id);
        if (empty($getway->active_getway)) {
            if ($getway->slug=='cod') {
                $data['title']='Cash On Delivery (COD)';
                $data['additional_details']='';
                
            }
            elseif($getway->slug=='instamojo'){
                $data['title']='instamojo';
                $data['env']='production';
                $data['purpose']='';
                $data['private_api_key']='';
                $data['private_auth_token']='';
            }

            elseif($getway->slug=='razorpay'){
                $data['title']='razorpay';
                $data['description']='';
                $data['currency']='';
                $data['key_id']='';
                $data['key_secret']='';
            }
            elseif($getway->slug=='paypal'){
                $data['title']='paypal';
                $data['description']='';
                $data['currency']='';
                $data['ClientID']='';
                $data['ClientSecret']='';
                $data['env']='production';
            }
            elseif($getway->slug=='stripe'){
                $data['title']='stripe';
                $data['description']='';
                $data['currency']='';
                $data['stripe_key']='';
                $data['stripe_secret']='';
                $data['env']='production';
            }
            elseif($getway->slug=='toyyibpay'){
                $data['title']='toyyibpay';
                $data['description']='';
                $data['currency']='';
                $data['user_secretkey']='';
                $data['category_code']='';
                $data['env']='production';
            }
            elseif($getway->slug=='mollie'){
                $data['title']='mollie';
                $data['description']='';
                $data['currency']='';
                $data['api_key']='';
            }
            elseif($getway->slug=='paystack'){
                $data['title']='paystack';
                $data['description']='';
                $data['currency']='';
                $data['public_key']='';
                $data['secret_key']='';
            }
            elseif($getway->slug=='mercado'){
                $data['title']='mercadopago';
                $data['description']='';
                $data['public_key']='';
                $data['access_token']='';
                 $data['env']='production';
            }
            
            else{
                return back();
            }
           $install=new Getway;
           $install->user_id =seller_id();
           $install->category_id = $request->id;
           $install->status = 0;
           $install->content = json_encode($data);
           $install->save();
        }

        return redirect()->route('seller.payment.show',$getway->slug);
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $info=Category::where('type','payment_getway')->with('active_getway')->where('slug',$slug)->first();
        $data=json_decode($info->active_getway->content ?? '');
        return view('seller.settings.payment.'.$slug,compact('info','data'));
       
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
        $info=Getway::with('method')->where('user_id',seller_id())->findorFail($id);
        if ($info->method->slug=='cod') {
            $data['title']=$request->name;
            $data['additional_details']=$request->additional_details;
            
        }

        if ($info->method->slug=='instamojo') {
            $data['title']=$request->name;
            $data['env']=$request->env ?? 'production';
            $data['private_api_key']=$request->private_api_key;
            $data['private_auth_token']=$request->private_auth_token;
            $data['purpose']=$request->purpose;
            
        }

         if ($info->method->slug=='razorpay') {
            $data['title']=$request->name;
            $data['key_id']=$request->key_id;
            $data['key_secret']=$request->key_secret;
            $data['description']=$request->description;
            $data['currency']=$request->currency;
            
        }

        if ($info->method->slug=='paypal') {
            $data['title']=$request->name;
            $data['currency']=$request->currency;
            $data['ClientID']=$request->ClientID;
            $data['ClientSecret']=$request->ClientSecret;
            $data['env']=$request->env ?? 'production';
        }

        if ($info->method->slug=='stripe') {
            $data['title']=$request->name;
            $data['description']=$request->description;
            $data['currency']=$request->currency;
            $data['stripe_key']=$request->stripe_key;
            $data['stripe_secret']=$request->stripe_secret;
            $data['env']=$request->env ?? 'production';
        }
        if ($info->method->slug=='toyyibpay') {
            $data['title']=$request->name;
            $data['description']=$request->description;
            $data['currency']=$request->currency;
            $data['user_secretkey']=$request->user_secretkey;
            $data['category_code']=$request->category_code;
            $data['env']=$request->env ?? 'production';
        }

        if($info->method->slug=='mollie'){
           $data['title']=$request->name;
           $data['description']=$request->description;
           $data['currency']=$request->currency;
           $data['api_key']=$request->api_key;
        }
        elseif($info->method->slug=='paystack'){
            $data['title']=$request->name;
            $data['description']=$request->description;
            $data['currency']=strtoupper($request->currency);
            $data['public_key']=$request->public_key;
            $data['secret_key']=$request->secret_key;
        }
        elseif($info->method->slug=='mercado'){
            $data['title']=$request->name;           
            $data['public_key']=$request->public_key;
            $data['access_token']=$request->access_token;
            $data['env']=$request->env ?? 'production';
        }

        $info->content=json_encode($data);
      
        $info->status=$request->status ?? 0;
        $info->save();

        return response()->json(['Information Updated']);

    }

    
}