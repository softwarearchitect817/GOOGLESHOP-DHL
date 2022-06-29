<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adminmenu;
use App\Option;
use DB;
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
    	if (!Auth()->user()->can('menu')) {
            return abort(401);
        }
        $menus= Adminmenu::latest()->get();
        $langs=Option::where('key','active_languages')->first();
        $langs=json_decode($langs->value ?? '');

        $positions=MenuPositions();
        $langs=$langs ?? [];
        return view('admin.menu.create',compact('menus','langs','positions'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         if (!Auth()->user()->can('menu')) {
            return abort(401);
        }
        if ($request->status==1) {
            if ($request->position == 'header') {
                DB::table('adminmenus')->where('position',$request->position)->where('lang',$request->lang)->update(['status'=>0]);
            }   
        }
        $men=new Adminmenu;
        $men->name=$request->name;
        $men->position=$request->position;
        $men->status=$request->status;
        $men->lang=$request->lang;
        $men->data="[]";
        $men->save();


        return response()->json(['Menu Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth()->user()->can('menu')) {
            return abort(401);
        }
        $info= Adminmenu::find($id);

        return view('admin.menu.index',compact('info'));
    }

    /*
    update menus json row in  menus table
    */
    public function MenuNodeStore(Request $request)
    {
        if (!Auth()->user()->can('menu')) {
            return abort(401);
        }
        $info= Adminmenu::find($request->menu_id);
        $info->data=$request->data;
        $info->save();

        Cache::forget($info->position.$info->lang);
        return response()->json(['Menu Updated']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $langs=Option::where('key','active_languages')->first();
       $langs=json_decode($langs->value);
       $info= Adminmenu::find($id);
       $positions=MenuPositions();

       return view('admin.menu.edit',compact('info','langs','positions'));
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
        if (!Auth()->user()->can('menu')) {
            return abort(401);
        }
        if ($request->status==1) {
           if ($request->position == 'header') {
            DB::table('adminmenus')->where('position',$request->position)->where('lang',$request->lang)->update(['status'=>0]);
        }
    }

    $men= Adminmenu::find($id);
    $men->name=$request->name;
    $men->position=$request->position;
    $men->status=$request->status;
    $men->lang=$request->lang;
    $men->save();
    
    Cache::forget($request->position.$request->lang);
    return response()->json(['Menu Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        public function destroy(Request $request)
        {
            if (!Auth()->user()->can('menu')) {
                return abort(401);
            }
            if ($request->method=='delete') {
             if ($request->ids) {
                foreach ($request->ids as $id) {
                 Adminmenu::destroy($id);
               }
             }
            }

        return response()->json(['Menu Removed']);
        }
}
