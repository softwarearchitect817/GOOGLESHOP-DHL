<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Term;
use App\Meta;
use App\Post;
use Auth;
use Illuminate\Support\Str;
class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (!Auth()->user()->can('page.list')) {
            abort(401);
        }    

       $pages=Term::where('type','page')->where('is_admin',1)->latest()->paginate(20);
       
       return view('admin.page.index',compact('pages'));
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         if (!Auth()->user()->can('page.create')) {
            abort(401);
        }
        return view('admin.page.create');
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
            'title' => 'required|max:100', 
        ]);


        $creat_slug=Str::slug($request->title);
        $check=Term::where('type','page')->where('slug',$creat_slug)->count();
        if ($check != 0) {
            $slug=$creat_slug.'-'.$check.rand(20,80);
        }
        else{
            $slug=$creat_slug;
        }

        $post=new Term;
        $post->title=$request->title;
        $post->slug=$slug;
        $post->status=$request->status;
        $post->type='page';
        $post->is_admin=1;
        $post->user_id=Auth::id();
        
        $post->save();

        $post_meta = new Meta;
        $post_meta->term_id=$post->id;
        $post_meta->key='excerpt';
        $post_meta->value=$request->excerpt;
        $post_meta->save();

        $post_meta = new Meta;
        $post_meta->term_id=$post->id;
        $post_meta->key='content';
        $post_meta->value=$request->content;
        $post_meta->save();
       
        return redirect('/admin/page');
    }

   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         if (!Auth()->user()->can('page.edit')) {
            abort(401);
        }
      $info=Term::with('excerpt','content')->find($id);   

       return view('admin.page.edit',compact('info'));
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
            'title' => 'required|max:255',    
        ]);

       
        $post= Term::find($id);
        $post->title=$request->title;
        $post->status=$request->status;
        $post->save();

        $post_meta =  Meta::where('term_id',$id)->where('key','excerpt')->first();
      //  if (!empty($post_meta)) {
        $post_meta->term_id=$post->id;
        $post_meta->key='excerpt';
        $post_meta->value=$request->excerpt;
        $post_meta->save();
       // }
       
        $postdetail= Meta::where('term_id',$id)->where('key','content')->first();
       // if (!empty($postdetail)) {
        $postdetail->term_id=$post->id;
        $postdetail->key='content';
        $postdetail->value=$request->content;
        $postdetail->save(); 
       // }
             
         return redirect('/admin/page');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       
          if ($request->status=='publish') {
            if ($request->ids) {

                foreach ($request->ids as $id) {
                    $post=Term::find($id);
                    $post->status=1;
                    $post->save();   
                }
                    
            }
        }
        elseif ($request->status=='trash') {
            if ($request->ids) {
                foreach ($request->ids as $id) {
                    $post=Term::find($id);
                    $post->status=0;
                    $post->save();   
                }
                    
            }
        }
        elseif ($request->status=='delete') {
            if ($request->ids) {
                foreach ($request->ids as $id) {
                   Term::destroy($id);
                   
                }
            }
        }
        return response()->json('Success');
    }
}
