<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Auth;
use App\Models\Article;
Use App\Models\ArticleComment;
use App\Http\Requests;

use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    public function ArticleComment()
    {
     $articles = Article::latest('id')->with(['category', 'subcategory'])->get();
     return view('blogs.comments.index', compact('articles'));
    }
    
    public function show_comments($id)
    {
          $ArticleComments = ArticleComment::orderBy('id', 'DESC')->where('article_id',$id)->get();
          return view('blogs.comments.show_comment', compact('ArticleComments'));
    }
    
    public function comment_deleted($id)
    {
        $comment = ArticleComment::find($id);
        $comment->delete();
        return response()->json(['Comment Deleted']);
    }
}

