<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Order;
use App\Trasection;
use App\Getway;
class TransectionController extends Controller
{
    protected $src;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      
        $user_id=seller_id();
        if ($request->src) {
            $this->src=$request->src;
            $orders=Order::where('transaction_id',$request->src)->latest()->with('getway')->where('user_id',$user_id)->paginate(40);
        }
        else{
           $orders=Order::with('getway')->where('user_id',$user_id)->latest()->paginate(40);
       }
       
       $getways=Getway::where('user_id',$user_id)->with('method')->get();
       return view('seller.transection.index',compact('orders','request','getways'));
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
            'o_id' => 'required|max:250',
            'method' => 'required|max:250',
            'transection_id' => 'required|max:250',
        ]);

        $transaction=Order::where('user_id',seller_id())->findorFail($request->o_id);
        $transaction->category_id = $request->method;
        $transaction->transaction_id = $request->transection_id;
        $transaction->save();
       

        return response()->json(['Trasection Successfully Updated']);

    }

    
}