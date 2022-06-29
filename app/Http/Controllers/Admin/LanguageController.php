<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Option;
use Cache;
class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth()->user()->can('language_edit')) {
            return abort(401);
        }
        $posts=Option::where('key','languages')->first();
        $actives=Option::where('key','active_languages')->first();

        $posts=json_decode($posts->value ?? '');
        if (!empty($actives)) {
            $actives=json_decode($actives->value);
            $data=[];
            foreach ($actives as $key => $value) {
                array_push($data, $key);
            }
            $actives=$data;

        }
        else{
           $actives=[]; 
        }
       
        return view('admin.language.index',compact('posts','actives'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth()->user()->can('language_edit')) {
            return abort(401);
        }
        $posts=base_path('langlist.json');
        $posts=file_get_contents($posts);
        $posts=json_decode($posts);
        return view('admin.language.create',compact('posts'));
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
            'language' => 'required',
            'language_name' => 'required',
        ]);

       
        $file=base_path('resources/lang/default.json');
        $file=file_get_contents($file);
        \File::put(base_path('resources/lang/'.$request->language.'.json'),$file);

        $arr=[];

        $langlist=Option::where('key','languages')->first();
        if (!empty($langlist)) {
            $langs=json_decode($langlist->value);
            foreach ($langs as $key => $value) {
                $arr[$key]=$value;
            }
        }
             
        $arr[$request->language]=$request->language_name;
           
        if (empty($langlist)) {
           $langlist=new Option;
           $langlist->key='languages';
        }
        $langlist->value=json_encode($arr);
        $langlist->save();

        return redirect('/admin/language/'.$request->language);

    }

    public function add_key(Request $request){
      $file=base_path('resources/lang/'.$request->id.'.json');
      $posts=file_get_contents($file);
      $posts=json_decode($posts);
      foreach($posts as $key => $row){
        $data[$key]=$row;
      }
      $data[$request->key]=$request->value;
      
      \File::put(base_path('resources/lang/'.$request->id.'.json'),json_encode($data,JSON_PRETTY_PRINT));
      return response()->json('Key Added');  
    }

    public function show($id)
    {
        if (!Auth()->user()->can('language_edit')) {
            return abort(401);
        }

        $file=base_path('resources/lang/'.$id.'.json');
        $posts=file_get_contents($file);
        $posts=json_decode($posts);
        return view('admin.language.edit',compact('posts','id'));
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
        $data=[];
        foreach ($request->values as $key => $row) {
            $data[$key]=$row;
        }
        $file=json_encode($data,JSON_PRETTY_PRINT);
        \File::put(base_path('resources/lang/'.$id.'.json'),$file);

        return response()->json(['Changes Saved']);
    }

    public function setActiveLanuguage(Request $request)
    {
        
        $posts=Option::where('key','active_languages')->first();
        $actives=json_decode($posts->value ?? '');
        $active_languages=[];
        
        foreach ($request->ids as $key => $value) {

            foreach ($value as $k => $row) {
                $active_languages[$row]=$k;
            }
        }
        if (empty($posts)) {
            $posts= new Option;
            $posts->key= 'active_languages';
        }
        $posts->value=json_encode($active_languages);
        $posts->save();
        Cache::forget('active_languages');
        return response()->json(['Language Activated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $posts=Option::where('key','languages')->first();
        $actives_lang=Option::where('key','active_languages')->first();


        $post=json_decode($posts->value ?? []);
        $actives=json_decode($actives_lang->value ?? '');

        $data=[];
        foreach ($post as $key => $row) {
            if ($id != $key) {
               $data[$key]=$row;
            }
            
        }

        $active_languages=[];
        foreach ($actives ?? [] as $ke => $value) {
             if ($id != $ke) {
               $active_languages[$ke]=$value;
            }
        }

        $posts->value=json_encode($data);
        $posts->save();

        if (empty($actives_lang)) {
            $actives_lang= new Option;
            $actives_lang->key= 'active_languages';
        }

        $actives_lang->value=json_encode($active_languages);
        $actives_lang->save();

        return back();
        //return $id;
    }
}
