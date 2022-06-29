<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Http\Requests;
use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\BlogSetting;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
      public function blog_setting()
      {
          $blog_setting = BlogSetting::where('user_id',seller_id())->first();
          return view('blogs.blog_setting.index', compact('blog_setting'));
      }
      
      public function blog_setting_update(Request $request)
      {
          $update_blog_setting = BlogSetting::where('user_id',seller_id())->first();
          $update_blog_setting->user_id = seller_id();
          $update_blog_setting->articles_per_page = $request->articleperpage;
          $update_blog_setting->comments = $request->comments;
          $update_blog_setting->update();
          return response()->json(['Blog Setting Updated']);
          
      }


      public function articles()
      {
        $domain  = $_SERVER['HTTP_HOST'];
        $user = DB::table('domains')->where('domain', $domain)->first();
        $user_id = $user->user_id;
        $articles = Article::latest('id')->where('user_id', $user_id)->with(['category', 'subcategory'])->get();
        return view('blogs.articles.articles', compact('articles'));
      }

      public function articles_create()
      {
            $blog_articles =  BlogCategory::orderBy('id', 'DESC')->get();
            return view('blogs.articles.article_create', compact('blog_articles'));
      }
      public function articles_store(Request $request)
      {
            $request->validate([
                  'title' => 'required|max:255',
                  'category_id' => 'nullable|integer',
                  'slug' => 'required|max:255|unique:articles,slug',
                  'tag' => 'required|max:255',
                  'comments' => 'required|max:255',
                  'description' => 'required|max:255',
                  'image' => 'required|mimes:pdf,doc,jpg,png',
            ]);
            $input = $request->all();
            $input['user_id'] = seller_id();
            $input['tags'] = $request->tag;
            
            if ($request->hasfile('image')) {
                  $file = $request->image;
                  $image = pathinfo($file->getClientOriginalName())['filename'] . '-' . seller_id() . Str::random(10) . '.' . $file->extension();
                  // $image = microtime() . '-' . $request->image->getClientOriginalName();
                  $file->move('uploads/articles', $image);
                  $input['image'] = 'uploads/articles/' . $image;
            }
            
            Article::create($input);
            
            if (is_null(DB::table('blog_settings')->where('user_id',seller_id())->first())) {
                  DB::table('blog_settings')->insert([
                        'user_id' => seller_id()
                  ]);
            }
            return response()->json(['Blog Article Created']);
      }

      public function article_edit($id)
      {
            $articles = Article::find($id);
            $blog_articles = BlogCategory::orderBy('id', 'DESC')->get();
            return view('blogs.articles.edit_article', compact('articles', 'blog_articles'));
      }

      public function article_update(Request $request)
      {

            $request->validate([
                  'title' => 'required|max:255',
                  'category_id' => 'nullable|integer',
                  'slug' => 'required|max:255',
                  'tag' => 'required|max:255',
                  'comments' => 'required|max:255',
                  'description' => 'required|max:255',
            ]);
            $article = Article::where('id', $request->id)->firstOrFail();
            $article['user_id'] = seller_id();
            $article['category_id'] = $request->category_id;
            $article['title'] = $request->title;
            $article['slug'] = $request->slug;
            $article['tags'] = $request->tag;
            $article['is_comment'] = $request->comments;
            $article['description'] = $request->description;
            if ($request->hasfile('image')) {
                  $this->validate($request, [
                        'image' => 'required|mimes:pdf,doc,jpg,png',
                  ]);
                  if (file_exists($article->image)) {
                        unlink($article->image);
                  }
                  $file = $request->image;
                  $image = pathinfo($file->getClientOriginalName())['filename'] . '-' . seller_id() . Str::random(10) . '.' . $file->extension();
                  $file->move('uploads/articles', $image);
                  $article['image'] = 'uploads/articles/' . $image;
            }
            $article->update();
            return response()->json(['Blog Article Updated']);
      }

      public function article_destroy($id)
      {
            $article = Article::where('id', $id)->firstOrFail();
            $article->delete();
            return redirect()->route('seller.blog-articles')->with('message', 'Blog Article Deleted');
      }
}