<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Auth;
use App\Models\BlogCategory;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
     
      public function create()
      {
        $domain  = $_SERVER['HTTP_HOST'];
        $user = DB::table('domains')->where('domain', $domain)->first();
        $user_id = $user->user_id;
        return view('blogs.article_create',[
            'blog_cats' => BlogCategory::latest('id')->where('user_id', $user_id)->with('blog_subcategories')->get()
        ]);
      }
      public function blog_store(Request $request)
      {
            $request->validate([
                'title' => 'required|max:255',
                'is_child' => 'nullable|integer',
                'slug' => 'unique:blog_categories,slug',
                'meta_keywords' => 'required|max:255',
                'description' => 'required|max:255',
            ]);
            $inputs = $request->all();
            $inputs['user_id'] = seller_id();
            $inputs['meta_description'] = $request->description;
            BlogCategory::create($inputs);
            return response()->json(['Blog Category Created']);
            return view('blogs.article_create');
      }
      public function view ()
      {
            $blog_cats = BlogCategory::latest('id')->with('blog_subcategories')->get();
            return view('blogs.view_blog',compact('blog_cats'));
      }
      public function blog_destroy ($id)
      {
            BlogCategory::where('id', $id)->delete();
            return redirect()->route('seller.blog-view')->with('message','Blog Article Deleted');
      }
      public function edit_blog_cat ($id)
      {
            $cat = BlogCategory::where('id', $id)->first();
            $blog_cats = DB::table('blog_categories')->get();
            return view('blogs.edit_blog',compact('cat','blog_cats'));
      }
      public function update_blog_cat (Request $request)
      {
            $request->validate([
                'title' => 'required|max:255',
                'is_child' => 'nullable|integer',
                'slug' => 'unique:blog_categories,slug,$this->id',
                'meta_keywords' => 'required|max:255',
                'description' => 'required|max:255',
            ]);
            $data = array(
                  'title' => $request->title,
                  'is_child' => $request->is_child,
                  'slug' => $request->slug,
                  'meta_keywords' => $request->meta_keywords,
                  'meta_description' => $request->description,
            );
            BlogCategory::where('id', $request->id)->update($data);
             return response()->json(['Blog Updated']);
      }
}