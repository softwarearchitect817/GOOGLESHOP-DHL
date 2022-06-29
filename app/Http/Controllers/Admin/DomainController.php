<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Domain;
use App\Models\User;
class DomainController extends Controller
{
    protected $email;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       if (!Auth()->user()->can('domain.list')) {
        return abort(401);
       }

       if ($request->type=='email') {
        $this->email=$request->src;
         $posts=Domain::whereHas('user',function($q){
            return $q->where('email',$this->email);
         })->with('user')->latest()->paginate(40);
       }
       elseif (!empty($request->src) && !empty($request->type)) {
           $posts=Domain::with('user')->where($request->type,$request->src)->latest()->paginate(40);
       }
       else{
        $posts=Domain::with('user')->latest()->paginate(40);
       }
        


        $all=Domain::count();
        $actives=Domain::where('status',1)->count();
        $drafts=Domain::where('status',2)->count();
        $trash=Domain::where('status',0)->count();
        $Requested=Domain::where('status',3)->count();
        $type="all";
        return view('admin.domain.index',compact('posts','request','all','actives','drafts','trash','type','Requested'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       if (!Auth()->user()->can('domain.create')) {
        return abort(401);
       }

       return view('admin.domain.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $request->validate([
            'domain_name' => 'required|max:100|unique:domains,domain',
            'full_domain' => 'required|max:100|unique:domains,full_domain',
            'email' => 'required',
         ]);

         $user=User::where('email',$request->email)->where('role_id',3)->with('user_domain')->first();

         if (empty($user)) {
             $data['errors']['user']="User Not Found";
             return response()->json($data,422); 
         }

         if (empty($user->user_domain)) {
           $domain=new Domain;     
         }   
         else{
            $domain=Domain::find($user->user_domain->id);
         }

         $domain->domain=$request->domain_name;
         $domain->full_domain=$request->full_domain;
         $domain->user_id=$user->id;
         $domain->status=$request->status;
         $domain->save();

         $user->domain_id=$domain->id;
         $user->save();

         $sub_users=User::where('created_by',$user->id)->update(['domain_id'=>$domain->id]);
         

         return response()->json(['Domain Created Successfully']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       if (!Auth()->user()->can('domain.edit')) {
        return abort(401);
       }
       $info= Domain::with('user')->findorFail($id);
       return view('admin.domain.edit',compact('info'));
    }

    public function show(Request $request,$id)
    {
       if (!Auth()->user()->can('domain.list')) {
        return abort(401);
       }

       if ($request->type=='email') {
        $this->email=$request->src;
         $posts=Domain::whereHas('user',function($q){
            return $q->where('email',$this->email);
         })->with('user')->where('status',$id)->latest()->paginate(40);
       }
       elseif (!empty($request->src) && !empty($request->type)) {
           $posts=Domain::with('user')->where('status',$id)->where($request->type,$request->src)->latest()->paginate(40);
       }
       else{
        $posts=Domain::with('user')->where('status',$id)->latest()->paginate(40);
       }

      
        $all=Domain::count();
        $actives=Domain::where('status',1)->count();
        $drafts=Domain::where('status',2)->count();
        $trash=Domain::where('status',0)->count();
        $Requested=Domain::where('status',3)->count();
        $type=$id;
        return view('admin.domain.index',compact('posts','request','all','actives','drafts','trash','type','Requested'));
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
        $request->validate([
            'domain_name' => 'required|max:100|unique:domains,domain, ' . $id,
            'full_domain' => 'required|max:100|unique:domains,full_domain, ' . $id,
            'email' => 'required',
         ]);

        $user=User::where('email',$request->email)->where('role_id',3)->first();

         if (empty($user)) {
             $data['errors']['user']="User Not Found";
             return response()->json($data,422); 
         }


         $domain= Domain::findorFail($id);
         $domain->domain=$request->domain_name;
         $domain->full_domain=$request->full_domain;
         $domain->user_id=$user->id;
         $domain->status=$request->status;
         $domain->save();

         return response()->json(['Domain Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!Auth()->user()->can('domain.delete')) {
          return abort(401);
        }
       
        if ($request->ids) {
            if ($request->method != 'delete') {
                foreach ($request->ids as $id) {
                    $domain=Domain::find($id);
                    $domain->status=$request->method;
                    $domain->save();
                }
                
            }
            else{
                foreach ($request->ids as $id) {
                    Domain::destroy($id);
                }
            }
        }

        return response()->json(['Success']);

    }
}
