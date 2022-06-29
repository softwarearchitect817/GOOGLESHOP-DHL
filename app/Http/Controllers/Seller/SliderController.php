<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Category;
use App\Categorymeta;
class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts=Category::where('user_id',seller_id())->where('type','slider')->latest()->get();

        return view('seller.store.sliders',compact('posts'));
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

        $validatedData = $request->validate([
            'url' => 'required|max:50',
            'title' => 'max:100',
            'sub_text' => 'max:200',
            'btn_text' => 'max:100',
            'file' => 'required|max:1000|image',
        ]);
        $auth_id=seller_id();
        $fileName = time().'.'.$request->file->extension();  
        $path='uploads/'.$auth_id.'/'.date('y/m');
        $request->file->move($path, $fileName);
        $name=$path.'/'.$fileName;

        $post=new Category;
        $post->name=$name;
        $post->slug=$request->url;
        $post->type='slider';
        $post->user_id=$auth_id;
        $post->save();

        $data['title']=$request->title;
        $data['sub_text']=$request->sub_text;
        $data['btn_text']=$request->btn_text;

        $meta=new Categorymeta;
        $meta->category_id=$post->id;
        $meta->type="excerpt";
        $meta->content=json_encode($data);
        $meta->save();
        return response()->json(['Slider Created']);
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $slider=Category::where('user_id',seller_id())->where('type','slider')->findorFail($id);
        if (file_exists($slider->name)) {
            unlink($slider->name);
        }
        $slider->delete();

        return back();
    }
}