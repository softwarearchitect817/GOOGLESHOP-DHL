<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Categorymeta;
use Str;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->src) {
           $posts=Category::withCount('posts')->where('type','category')->where($request->type,$request->src)->latest()->paginate(20);
         }
         else{
            $posts=Category::withCount('posts')->where('type','category')->latest()->paginate(20);
         }   

       
       return view('admin.category.index',compact('posts'));
    } 

    public function countries(Request $request)
    {
        if ($request->src) {
           $posts=Category::withCount('posts')->where('type','country')->where($request->type,$request->src)->latest()->paginate(20); 
        }
        else{
            $posts=Category::withCount('posts')->where('type','country')->latest()->paginate(20);
        }
       
       return view('admin.location.country.index',compact('posts'));
    }
    public function countryCreate()
    {
        return view('admin.location.country.create');
    }

    public function cities(Request $request)
    {
        if ($request->src) {
          $posts=Category::withCount('posts')->where('type','city')->where($request->type,$request->src)->latest()->paginate(20);
        }
        else{
          $posts=Category::withCount('posts')->where('type','city')->latest()->paginate(20);
        }
       
       return view('admin.location.city.index',compact('posts'));
    }
    public function cityCreate()
    {
        return view('admin.location.city.create');
    }

   


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
        'name' => 'required|unique:categories|max:100',
        'file' => 'image|max:1000',
        ]);
        $slug=Str::slug($request->name);
        if (empty($slug)) {
            $slug=Category::max('id')+1;
        }
         
        $post= new Category;
        $post->name=$request->name;
        $post->slug=$slug;
        if ($request->file) {
            $imageName = date('dmy').time().'.'.request()->file->getClientOriginalExtension();
            request()->file->move('uploads/admin/1/'.date('y/m'), $imageName);
            $post->avatar='uploads/admin/1/'.date('y/m').'/'.$imageName;
        }
        
        $post->type=$request->type;
        if ($request->p_id) {
           $post->p_id=$request->p_id;
        }
        
        $post->featured=$request->featured;
        $post->save();

        if ($request->type=='country' || $request->type=='city') {
          $data['latitude']=$request->latitude;
          $data['longitude']=$request->longitude;
          $data['zoom']=$request->zoom;
          $meta=new Categorymeta;
          $meta->type='mapinfo';
          $meta->category_id=$post->id;
          $meta->content=json_encode($data);
          $meta->save();

        }
        return response()->json([$request->type.' Created']);
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
         $info=Category::with('map')->find($id);
         if ($info->type=='country') {
            $map=json_decode($info->map->content);
           return view('admin.location.country.edit',compact('info','map'));
         }
         elseif($info->type=='city'){
             $map=json_decode($info->map->content);
             return view('admin.location.city.edit',compact('info','map'));
         }
         elseif($info->type=='category'){
            return view('admin.category.edit',compact('info'));
         }
         //return view('admin.category.edit',compact('info'));
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
        $validatedData = $request->validate([
        'name' => 'required|max:100',
        'slug' => 'required|max:100',
        'file' => 'image|max:1000',
        ]);

        $post= Category::find($id);
        $post->name=$request->name;
        $post->slug=$request->slug;
        if ($request->file) {
            $imageName = date('dmy').time().'.'.request()->file->getClientOriginalExtension();
            request()->file->move('uploads/'.date('y/m'), $imageName);
            if (file_exists($post->avatar)) {
               unlink($post->avatar);
            }
            $post->avatar='uploads/'.date('y/m').'/'.$imageName;
        }
        
        $post->p_id=$request->p_id;
        $post->featured=$request->featured;
        $post->save();

        if ($post->type=='country' || $post->type=='city') {
          $data['latitude']=$request->latitude;
          $data['longitude']=$request->longitude;
          $data['zoom']=$request->zoom;
          $meta= Categorymeta::where('type','mapinfo')->where('category_id',$post->id)->first();
          if (!empty($meta)) {
         
          $meta->content=json_encode($data);
          $meta->save();
           }

        }

        return response()->json([$post->type.' Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        
        if ($request->type == "delete") {
           foreach ($request->ids as $row) {
                $category=Category::with('preview')->find($row);
                if (!empty($category->preview->content)) {
                    if (file_exists($category->preview->content)) {
                        unlink($category->preview->content);
                    }
                }
                
                $category->delete();
           }
        }

        return response()->json(['Success']);
    }
}
