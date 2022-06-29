<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Template;
use App\Domain;
use Cache;
use File;
class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts=Template::latest()->paginate(20);
        $active_theme=Domain::where('user_id',seller_id())->first();
        return view('seller.store.theme',compact('posts','active_theme'));
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

        $domain_id=Auth::user()->domain_id;
        Domain::where('id',$domain_id)->update(['template_id'=>$id]);
       
        $template = Template::find($id);

        if ($template) {
            $arr = explode('/', $template->src_path);
            $template_name = $arr[count($arr) - 1];
        
            $default_template_path = base_path('resources/views/frontend/default-templates/'.$template_name);
            $user_template_path = base_path('resources/views/frontend/'.seller_id().'/'.$template_name);

            if(!File::isDirectory($user_template_path)) {
                File::copyDirectory($default_template_path, $user_template_path);
            }
        
            $default_asset_path = base_path('../frontend/default-templates/'.$template_name);
            $user_asset_path = base_path('../frontend/'.seller_id().'/'.$template_name);

            if(!File::isDirectory($user_asset_path)) {
                File::copyDirectory($default_asset_path, $user_asset_path);
            }
        }

        
        Cache::forget(get_host());
       \Session::flash('success', 'Theme activated successfully');
        return back();
    }

    public function customise(Request $request, $id)
    {
        $template = Template::find($id);
        $arr = explode('/', $template->src_path);
        $template_name = $arr[count($arr) - 1];
        
        return view('seller.store.theme_customise',compact('template_name'));
    }
}