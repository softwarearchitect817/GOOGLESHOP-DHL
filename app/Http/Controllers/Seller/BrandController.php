<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Categorymeta;
use Auth;
use Str;
class BrandController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts=Category::where('user_id',seller_id())->where('type','brand')->with('preview')->latest()->paginate(20);
        return view('seller.brand.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seller.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
          $limit=user_limit();
         $posts_count=\App\Category::where('user_id',seller_id())->where('type','brand')->count();
         if ($limit['brand_limit'] <= $posts_count) {
        
         $error['errors']['error']='Maximum Brand limit exceeded';
         return response()->json($error,401);
        }

         if ($limit['storage'] <= str_replace(',', '', folderSize('uploads/'.seller_id()))) {
         \Session::flash('error', 'Maximum storage limit exceeded');
         $error['errors']['error']='Maximum storage limit exceeded';
         return response()->json($error,401);
        }

        $slug=Str::slug($request->name);
        
        $check=Category::where('type','brand')->where('slug',$slug)->count();
        if ($check > 0) {
            $slug= $slug.'-'.rand(20,100);    
        }


        $category= new Category;
        $category->name=$request->name;
        $category->slug=$slug;      
        $category->type='brand';
        $category->featured=$request->featured;
        $category->user_id=seller_id();
        $category->save();

        if($request->file){

            $fileName = time().'.'.$request->file->extension();  
            $path='uploads/'.seller_id().'/'.date('y/m');
            $request->file->move($path, $fileName);
            $name=$path.'/'.$fileName;

            $meta= new Categorymeta;
            $meta->category_id =$category->id;
            $meta->type="preview";
            $meta->content=$name;
            $meta->save();

        }

        return response()->json(['Brand Created']);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info= Category::where('user_id',seller_id())->findOrFail($id);
        return view('seller.brand.edit',compact('info'));
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
        $category= Category::where('user_id',seller_id())->findOrFail($id);
        $category->name=$request->name;
             
        $category->featured=$request->featured;
        $category->save();

        if($request->file){
            $limit=user_limit();
            if ($limit['storage'] <= str_replace(',', '', folderSize('uploads/'.seller_id()))) {
               \Session::flash('error', 'Maximum storage limit exceeded');
               $error['errors']['error']='Maximum storage limit exceeded';
               return response()->json($error,401);
            }

            if(!empty($category->preview)){
                if(file_exists($category->preview->content)){
                    unlink($category->preview->content);
                }
            }

            $fileName = time().'.'.$request->file->extension();  
            $path='uploads/'.seller_id().'/'.date('y/m');
            $request->file->move($path, $fileName);
            $name=$path.'/'.$fileName;
            $meta =  Categorymeta::where('category_id',$category->id)->where('type','preview')->first();
            if (empty($meta)){
              $meta= new Categorymeta;  
            }
            
            $meta->category_id =$category->id;
            $meta->type="preview";
            $meta->content=$name;
            $meta->save();

        }


        return response()->json(['Brand Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->type=='delete') {
            foreach ($request->ids as $key => $row) {
                $id=base64_decode($row);
                $category= Category::destroy($id);
            }
        }

        return response()->json(['Brand Deleted']);
    }
}