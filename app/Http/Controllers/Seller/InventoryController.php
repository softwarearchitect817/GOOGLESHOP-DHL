<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Stock;
use Auth;
class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $plan=user_limit();
      $plan=filter_var($plan['inventory']);
            if($plan == false){
               return back();
               
      } 

        if (!empty($request->src)) {
            $posts=Stock::where('sku','LIKE','%'.$request->src.'%')->whereHas('term',function($q){
            	return $q->where('user_id',seller_id());
            })->with('term')->paginate(50);
        }
        elseif(!empty($request->status)){
            if ($request->status=='in') {
                $posts=Stock::where('stock_status',1)->whereHas('term',function($q){
                	return $q->where('user_id',seller_id());
                })->with('term')->paginate(50);
            }
            else{
                $posts=Stock::where('stock_status',0)->whereHas('term',function($q){
                	return $q->where('user_id',seller_id());
                })->with('term')->paginate(50); 
            }
           

        }
        else{
           $posts=Stock::with('term')->whereHas('term',function($q){
            	return $q->where('user_id',seller_id());
            })->paginate(30);
        } 

       
       $src=$request->src ?? '';
       $status=$request->status ?? '';
       $in_stock=Stock::where('stock_status',1)->whereHas('term',function($q){
            	return $q->where('user_id',seller_id());
            })->count();
       $out_stock=Stock::where('stock_status',0)->whereHas('term',function($q){
            	return $q->where('user_id',seller_id());
            })->count();
       $total=Stock::whereHas('term',function($q){
            	return $q->where('user_id',seller_id());
            })->count();

       return view('seller.inventory.index',compact('posts','total','in_stock','out_stock','status'));
    }

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
         $id=base64_decode($id);
         $stock=Stock::with('term')->find($id);
        if($stock->term->user_id != seller_id()){
            die();
        }
         if (!empty($stock)) {

            if (!empty($request->stock_manage)) {
              $stock->stock_manage=$request->stock_manage;
          }



          if (!empty($request->stock_qty)) {
              $stock->stock_qty=$request->stock_qty;
          }
          if (!empty($request->sku)) {
              $stock->sku=$request->sku;
          }

          $stock->stock_status=$request->stock_status ?? 0;


          $stock->save();
         }

          return response()->json(['Stock Update Successfully']);
    }

   
}