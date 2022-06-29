<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Useroption;
use Auth;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;
class SeoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info=Useroption::where('user_id',seller_id())->where('key','seo')->first();
        if (empty($info)) {
            $info=new Useroption;
            $info->user_id=seller_id();
            $info->key='seo';
            $data['title']='';
            $data['twitterTitle']='';
            $data['canonical']='';
            $data['tags']='';
            $data['description']='';
            $info->value=json_encode($data);
            $info->save();
            
        }
        $id=$info->id;
        $info=json_decode($info->value);
       
        return view('seller.store.seo',compact('info','id'));
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth_id=seller_id();
        if (!file_exists('uploads/'.$auth_id.'/sitemap.xml')) {
            file_put_contents('uploads/'.$auth_id.'/sitemap.xml', '');
        }
        $url=my_url();

        $products=\App\Term::where('user_id',$auth_id)->where('type','product')->get();
        $pages=\App\Term::where('user_id',$auth_id)->where('type','page')->get();
        $categories=\App\Category::where('user_id',$auth_id)->where('type','page')->get();
        $brands=\App\Category::where('user_id',$auth_id)->where('type','brands')->get();

        $index = new Index('uploads/'.seller_id().'/sitemap.xml');
        $index->addSitemap($url.'/');
        $index->addSitemap($url.'/shop');
        $index->addSitemap($url.'/contact');


        foreach ($products as $key => $row) {
             $index->addSitemap($url.'/product/'.$row->slug.'/'.$row->id);
        }

        foreach ($categories as $key => $row) {
             $index->addSitemap($url.'/category/'.$row->slug.'/'.$row->id);
        }

        foreach ($pages as $key => $row) {
             $index->addSitemap($url.'/page/'.$row->slug.'/'.$row->id);
        }

         foreach ($brands as $key => $row) {
             $index->addSitemap($url.'/brand/'.$row->slug.'/'.$row->id);
        }
        $check= $index->write();


      return response()->json(['New Sitemap Generated']);
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
       $info=Useroption::where('user_id',seller_id())->where('key','seo')->findorFail($id);
       $data['title']=$request->title;
       $data['twitterTitle']=$request->twitterTitle;
       $data['canonical']=$request->canonical;
       $data['tags']=$request->tags;
       $data['description']=$request->description;
       $info->value=json_encode($data);
       $info->save();

       return response()->json(['Site Seo Content Updated']);
    }

   
}