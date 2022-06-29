<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Useroption;
use App\Menu;
use Auth;
use Cache;
class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     return view('seller.store.menu.index');
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        if ($slug=='left' || $slug=='right' || $slug=='center' || $slug=='header') {
            $info=Menu::where('user_id',seller_id())->where('position',$slug)->first();
            if (empty($info)) {
                $info=new Menu;
                $info->user_id=seller_id();
                $info->position=$slug;
                $info->name=$slug;
                $info->data='[]';
                $info->save();
            }
            
            return view('seller.store.menu.edit',compact('info'));
        }
        else{
            abort(404);
        }

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
        
        $info=Menu::where('user_id',seller_id())->findorFail($id);
        $info->name=$request->name;
        $info->data=$request->data;
        $info->save();

        Cache::forget($info->position.'menu'.seller_id());
        return response()->json(['Menu Updated']);
    }

    
}