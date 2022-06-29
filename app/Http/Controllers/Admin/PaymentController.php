<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Categorymeta;
use Auth;
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         if (!Auth()->user()->can('payment_gateway.config')) {
            abort(401);
          }  
        $posts=Category::where('type','payment_getway')->with('preview')->withCount('gateway_users')->get();
        return view('admin.payment_gateway.index',compact('posts'));
    }

    

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
         if (!Auth()->user()->can('payment_gateway.config')) {
            abort(401);
        } 
        $info=Category::with('description','preview','credentials')->where('type','payment_getway')->where('slug',$slug)->first();
        $credentials=json_decode($info->credentials->content ?? '');

        return view('admin.payment_gateway.edit',compact('info','info','credentials'));
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
        $validatedData = $request->validate([
            'title' => 'required|max:20',
            'description' => 'required|max:200',
            'file' => 'image',
            
        ]);

       $info=Category::find($id);
       $info->name=$request->title;
      
       $info->featured=$request->status ?? 1;
      
       $info->save();

       $meta=Categorymeta::where('category_id',$id)->where('type','description')->first();
       $meta->content=$request->description;
       $meta->save();

       
       if ($info->slug != 'cod') {
          
       $credentials=Categorymeta::where('category_id',$id)->where('type','credentials')->first();
       if (empty($credentials)) {
           $credentials=new Categorymeta;
           $credentials->type="credentials";
           $credentials->category_id=$id;
       }

        if ($info->slug=='instamojo') {
         $data['x_api_Key']=$request->x_api_Key;
         $data['x_api_token']=$request->x_api_token;
        }
        if ($info->slug=='razorpay') {
         $data['key_id']=$request->key_id;
         $data['key_secret']=$request->key_secret;
         $data['currency']=$request->currency;
        }
        if ($info->slug=='paypal') {
         $data['client_id']=$request->client_id;
         $data['client_secret']=$request->client_secret;
         $data['currency']=$request->currency;
        }

        if ($info->slug=='stripe') {
         $data['publishable_key']=$request->publishable_key;
         $data['secret_key']=$request->secret_key;
         $data['currency']=$request->currency;
        }

        if ($info->slug=='toyyibpay') {
         $data['userSecretKey']=$request->userSecretKey;
         $data['categoryCode']=$request->categoryCode;
        } 

        if ($info->slug=='mollie') {
         $data['api_key']=$request->api_key;
         $data['currency']=$request->currency;
        }
        if ($info->slug=='paystack') {
         $data['public_key']=$request->public_key;
         $data['secret_key']=$request->secret_key;
         $data['currency']=$request->currency;
        }
        if ($info->slug=='mercado') {
         $data['public_key']=$request->public_key;
         $data['access_token']=$request->access_token;
         
        }



         $credentials->content=json_encode($data);
         $credentials->save();

        }

       if (!empty($request->file)) {
            $imageName = date('dmy').time().'.'.request()->file->getClientOriginalExtension();
            request()->file->move('uploads/admin/1/'.date('y/m'), $imageName);
            $name='uploads/admin/1/'.date('y/m').'/'.$imageName;

            $preview=Categorymeta::where('category_id',$id)->where('type','preview')->first();
            $preview->content=$name;
            $preview->save();

       }

       return response()->json(['Payment Gateway Info Updated']);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
