<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Useroption;
use Auth;
class AdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts=Category::where('type','offer_ads')->where('user_id',seller_id())->latest()->get();
        return view('seller.ads.offer',compact('posts'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|max:500|image',
            'url' => 'required|max:150',
        ]);


        $limit=user_limit();
        $posts_count=\App\Term::where('user_id',seller_id())->count();
        if ($limit['product_limit'] <= $posts_count) {
           \Session::flash('error', 'Maximum posts limit exceeded');
           $error['errors']['error']='Maximum posts limit exceeded';
           return response()->json($error,401);
        }

        if ($limit['storage'] <= str_replace(',', '', folderSize('uploads/'.seller_id()))) {
           \Session::flash('error', 'Maximum storage limit exceeded');
           $error['errors']['error']='Maximum storage limit exceeded';
           return response()->json($error,401);
        }

        $auth_id=seller_id();
        $fileName = time().'.'.$request->file->extension();  
        $path='uploads/'.$auth_id.'/'.date('y/m');
        $request->file->move($path, $fileName);
        $name=$path.'/'.$fileName;

        $post=new Category;
        $post->name=$name;
        $post->slug=$request->url;
        $post->type='offer_ads';
        $post->user_id=$auth_id;
        $post->save();

        return response()->json(['Ads Created']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $row=Category::where('type','banner_ads')->where('user_id',seller_id())->first();
        return view('seller.ads.banner',compact('row'));
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
         $validated = $request->validate([
            'file' => 'required|max:500|image',
            'url' => 'required|max:150',
        ]);


        $limit=user_limit();
        $posts_count=\App\Term::where('user_id',seller_id())->count();
        if ($limit['product_limit'] <= $posts_count) {
           \Session::flash('error', 'Maximum posts limit exceeded');
           $error['errors']['error']='Maximum posts limit exceeded';
           return response()->json($error,401);
        }

        if ($limit['storage'] <= str_replace(',', '', folderSize('uploads/'.seller_id()))) {
           \Session::flash('error', 'Maximum storage limit exceeded');
           $error['errors']['error']='Maximum storage limit exceeded';
           return response()->json($error,401);
        }  

        $post =Category::where('type','banner_ads')->where('user_id',seller_id())->first();
        if(empty($post)){
            $post=new Category;
           
        }
        else{
            if(file_exists($post->name)){
                unlink($post->name);
            }

        }

        $auth_id=seller_id();
        $fileName = time().'.'.$request->file->extension();  
        $path='uploads/'.$auth_id.'/'.date('y/m');
        $request->file->move($path, $fileName);
        $name=$path.'/'.$fileName;

        
        $post->name=$name;
        $post->slug=$request->url;
        $post->type='banner_ads';
        $post->user_id=$auth_id;
        $post->save();

        return response()->json(['Banner Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $category= Category::where('user_id',seller_id())->findorFail($id);
        if (file_exists($category->name)){
            unlink($category->name);
        }
        $category->delete();
        return back();
    }
}
