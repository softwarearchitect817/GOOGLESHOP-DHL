<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Auth;
use Str;
use App\Models\Userplanmeta;
use Storage;
class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


$json = Storage::disk('local')->get('countries.json');
$json = json_decode($json, true);
// foreach($json as $k => $v) echo $v; return;

        $posts=Category::where('user_id',Auth::id())->where('type','city')->latest()->paginate(20);

        $json=(array)$json; 
        // $json=(object)$json; 
        // return gettype($json);
        return view('seller.shipping.location.index',compact('posts','json'));
    }

    public function index2()
    {
        $posts=Category::where('user_id',Auth::id())->where('type','city')->get()->latest()->paginate(20);
        return view('seller.shipping.location.index2',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('seller.shipping.location.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plan=user_limit(); 
        $countries=$request->title;          
        
       /* $count=Category::where('user_id',Auth::id())->where('type','city')->count();
        $limit=$plan['location_limit'];
        if($limit <= $count){
           $msg='Maximum Location Exceeded Please Update Your Plan';
           $error['errors']['error']=$msg;
          // return response()->json($error,401); 
           return $msg; 
            }  */

     //  $validatedData = $request->validate([
        //'title' => 'required|max:50', ]);

        foreach($countries as $cn) {
       $post = new Category;
       $post->name=$cn;
       $post->user_id =Auth::id();
       $post->slug=Str::slug($cn);
       $post->type="city";
       $post->save();
   }

       return redirect()->back();// 'Location Created Successfully';
    }





   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info=Category::where('user_id',Auth::id())->findorFail($id);
        return view('seller.shipping.location.edit',compact('info'));
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
        'title' => 'required|max:50',
       ]);
       $post = Category::where('user_id',Auth::id())->findorFail($id);
       $post->name=$request->title;
       $post->save();

       return response()->json(['Location Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $auth_id=Auth::id();
        if ($request->method=='delete') {
           foreach ($request->ids as $key => $id) {
               $post = Category::where('user_id',$auth_id)->findorFail($id);
               $post->delete();
           }
        }

        return response()->json(['Success']);
    }
}
