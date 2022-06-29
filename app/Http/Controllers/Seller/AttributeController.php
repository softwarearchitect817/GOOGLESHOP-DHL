<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Category;
use Str;
use App\Rules\Translation;
use App\Useroption;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $posts=Category::where([
        ['user_id',seller_id()],
        ['type','parent_attribute'],
       ])->with('childrenCategories')->withCount('parent_variation')->get();

       return view('seller.attributes.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* Add multi-lang edit by nurs */
        $user_id = seller_id();
        $languages= Useroption::where('user_id',$user_id)->where('key','languages')->first();
        $langlist = json_decode($languages->value ?? '');
        
        $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
        return view('seller.attributes.create', compact('langlist', 'local'));
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
          $posts_count1=Category::where('user_id',seller_id())->where('type','child_attribute')->count();
         $posts_count2=Category::where('user_id',seller_id())->where('type','parent_attribute')->count();
          $posts_count=$posts_count1+$posts_count2;
         if ($limit['variation_limit'] <= $posts_count) {
          $error['errors']['error']='Maximum Attribute limit exceeded';
          return response()->json($error,401);
         }

         
        $validatedData = $request->validate([
        //  'title' => 'required|max:20',
         'name_translations' => [new Translation]
        ]);
        
        $name_translations = (array)json_decode($request->name_translations);
        $user_id=seller_id();
                
        $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
        $slug=Str::slug($name_translations[$local]);
        
        $post=new Category;
        $post->user_id=seller_id();
        $post->type='parent_attribute';
        $post->setTranslations('name', $name_translations);
        $post->featured=$request->featured;
        $post->slug=Str::slug($slug);
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
        $posts= Category::where([
            ['type','child_attribute'],
            ['p_id',$id],
            ['user_id',seller_id()]
        ])->with('parent')->withCount('variations')->get();

        return view('seller.attributes.childAttributes.index',compact('posts','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* Add multi-lang edit by nurs */
        $user_id = seller_id();
        $languages= Useroption::where('user_id',$user_id)->where('key','languages')->first();
        $langlist = json_decode($languages->value ?? '');
        
        $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
        $info= Category::where([
            ['type','parent_attribute'],
            ['user_id',seller_id()]
        ])->findorFail($id);

         return view('seller.attributes.edit',compact('info', 'langlist', 'local'));
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
        //  'title' => 'required|max:20',
        'name_translations' => [new Translation]
        ]);

        $post= Category::where([
            ['type','parent_attribute'],
            ['user_id',seller_id()]
        ])->findorFail($id);
        
        $name_translations = (array)json_decode($request->name_translations);
        $post->setTranslations('name', $name_translations);
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
        $user_id=seller_id();
        if ($request->method=='delete') {
            foreach ($request->ids as $key => $id) {
              Category::where([
                ['user_id',$user_id],
                ['p_id',$id],
               ])->delete();

               $post= Category::where([
                ['user_id',$user_id]
               ])->findorFail($id);
               $post->delete();
            }
        }

        return response()->json(['Attribute Deleted']);
    }
}