<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Category;
use Str;
class ChildattributeController extends Controller
{
     
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $limit=user_limit();
         $posts_count1=Category::where('user_id',Auth::id())->where('type','child_attribute')->count();
         $posts_count2=Category::where('user_id',Auth::id())->where('type','parent_attribute')->count();
          $posts_count=$posts_count1+$posts_count2;
         if ($limit['variation_limit'] <= $posts_count) {
          $error['errors']['error']='Maximum Attribute limit exceeded';
          return response()->json($error,401);
         }

        $validatedData = $request->validate([
         'title' => 'required|max:20',
         'parent_attribute' => 'required',
        ]);

        $user_id=Auth::id();
        $info=Category::where([
            ['user_id',$user_id],
            ['type','parent_attribute'],
        ])->findorFail($request->parent_attribute);

        $post=new Category;
        $post->user_id=$user_id;
        $post->type='child_attribute';
        $post->name=$request->title;
        $post->p_id=$request->parent_attribute;
        $post->featured=$request->featured;
        $post->slug=Str::slug($request->title);
        $post->save();

        return response()->json(['Attribute Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $info= Category::where([
            ['type','parent_attribute'],
            ['user_id',Auth::id()]
        ])->with('childrenCategories')->findorFail($id);

        return view('seller.attributes.childAttributes.create',compact('info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info= Category::where([
            ['type','child_attribute'],
            ['user_id',Auth::id()]
        ])->find($id);

         return view('seller.attributes.childAttributes.edit',compact('info'));
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
         'title' => 'required|max:20',
         'parent_attribute' => 'required',
        ]);

        $post= Category::where([
            ['type','child_attribute'],
            ['user_id',Auth::id()]
        ])->findorFail($id);
        $post->name=$request->title;
        $post->p_id=$request->parent_attribute;
        $post->featured=$request->featured;
        $post->save();

        return response()->json(['Attribute Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user_id=Auth::id();
        if ($request->method=='delete') {
            foreach ($request->ids as $key => $id) {
               $post= Category::where([
                ['type','child_attribute'],
                ['user_id',$user_id]
               ])->findorFail($id);
               $post->delete();
            }
        }

        return response()->json(['Attribute Deleted']);
    }
}
