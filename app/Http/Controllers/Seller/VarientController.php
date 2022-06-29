<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Term;
use App\Stock;
use App\Attribute;
use App\Meta;
use Auth;
use Str;
class VarientController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      $validatedData = $request->validate([
        'attribute' => 'required',
        'variation' => 'required',
        'sku' => 'required',
      ]);

       $attr= new Attribute;
       $attr->category_id=$request->attribute;
       $attr->variation_id=$request->variation;
       $attr->user_id=Auth::id();
       $attr->weight=$request->weight ?? 0;
       $attr->term_id=$request->id;
       $attr->price = $request->price;
       $attr->save();

       

       $stock= new Stock;
       $stock->attribute_id = $attr->id;
       $stock->stock_manage = $request->stock_status ?? 0;
       $stock->stock_qty = $request->stock_qty ?? 1;
       $stock->sku = $request->sku;
       $stock->save();

       return response()->json(['Attribute Added Successfully']);


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
       $id= base64_decode($id);
       $attr=  Attribute::where('user_id',Auth::id())->findorFail($id);
       $attr->weight=$request->weight ?? 0;
       $attr->price = $request->price;
       $attr->save();

      

       $stock= Stock::where('attribute_id',$id)->first();;
       $stock->stock_manage = $request->stock_manage ?? 0;
       $stock->stock_qty = $request->stock_qty;
       $stock->stock_status = $request->stock_status;
       $stock->sku = $request->sku;
       $stock->save();

       return response()->json(['Attribute Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id=base64_decode($request->id);
        $attr=Attribute::where('user_id',Auth::id())->findorFail($id);
        $attr->delete();

        return response()->json(['Variation Deleted Successfully']);
    }
}
