<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Option;
use App\Term;
use App\Models\User;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;
use Auth;
class SeoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if (!Auth()->user()->can('seo')) {
        return abort(401);
      }
       $settings=Option::where('key','seo')->first();
       $info=json_decode($settings->value ?? '');

       return view('admin.seo.index',compact('info'));
    }

 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $seo['title']=$request->title;
        $seo['description']=$request->description;
        $seo['canonical']=$request->canonical;
        $seo['tags']=$request->tags;
        $seo['twitterTitle']=$request->twitterTitle;

        $json=json_encode($seo);

        $settings=Option::where('key','seo')->first();
        if (empty($settings)) {
            $settings=new Option;
            $settings->key="seo";
        }
        $settings->value=$json;
        $settings->save();
        return response()->json('Site Seo Updated');
        
    }

    public function update(Request $request,$id)
    {
        $posts=Term::where('type','page')->where('is_admin',1)->get();

        $index = new Index(base_path('sitemap.xml'));
        $index->addSitemap(url('/'));
        foreach ($posts as $key => $row) {
            $index->addSitemap(url('/page',$row->slug));
        }
           
        $check= $index->write();
      
        return response()->json('New Sitemap Generated');
    }
}
