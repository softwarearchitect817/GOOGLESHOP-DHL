<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Domain;
use App\Models\Requestdomain;

class CustomdomainController extends Controller
{
	public function index(Request $request)
	{
     abort_if(getenv("AUTO_APPROVED_DOMAIN") == false,404);
		if (!Auth()->user()->can('domain.list')) {
			return abort(401);
		}

		if ($request->type=='email') {
			$this->email=$request->src;
			$posts=Requestdomain::whereHas('user',function($q){
				return $q->where('email',$this->email);
			})->with('user','parentdomain')->latest()->paginate(40);
		}
		elseif (!empty($request->src) && !empty($request->type)) {
			$posts=Requestdomain::with('user','parentdomain')->where($request->type,$request->src)->latest()->paginate(40);
		}
		else{
			$posts=Requestdomain::with('user','parentdomain')->latest()->paginate(40);
		}
		$type="all";
		$all=Requestdomain::count();
		$actives=Requestdomain::where('status',1)->count();
		$trash=Requestdomain::where('status',0)->count();
		$requested=Requestdomain::where('status',2)->count();

		return view('admin.domain.custom_domain_requests',compact('posts','request','type','all','actives','trash','requested'));
	}

	public function show(Request $request,$id)
	{
		if (!Auth()->user()->can('domain.list')) {
        return abort(401);
       }

       if ($request->type=='email') {
        $this->email=$request->src;
         $posts=Requestdomain::whereHas('user',function($q){
            return $q->where('email',$this->email);
         })->with('user','parentdomain')->where('status',$id)->latest()->paginate(40);
       }
       elseif (!empty($request->src) && !empty($request->type)) {
           $posts=Requestdomain::with('user','parentdomain')->where('status',$id)->where($request->type,$request->src)->latest()->paginate(40);
       }
       else{
        $posts=Requestdomain::with('user','parentdomain')->where('status',$id)->latest()->paginate(40);
       }

      
        $all=Requestdomain::count();
        $actives=Requestdomain::where('status',1)->count();
        $trash=Requestdomain::where('status',0)->count();
        $requested=Requestdomain::where('status',2)->count();
        $type=$id;
        return view('admin.domain.custom_domain_requests',compact('posts','request','all','actives','trash','type','requested'));
	}

	public function edit($id)
	{
    	$info=Requestdomain::findorfail($id);
    	return view('admin.domain.custom_domain_edit',compact('info'));
	}

	public function update(Request $request,$id)
	{

    $domain=Requestdomain::findorfail($id);
    $domain->status=$request->status;
    $domain->domain=$request->domain;
    $domain->save();

    if ($request->status == 1 && $request->reflect == 1) {
      $check=Domain::where('domain',$request->domain)->where('id','!=',$domain->domain_id)->first();
      if (!empty($check)) {
        $error['errors']['domain']='Opps this domain already taken....!!';
        return response()->json($error,422);
      }
      $current_domain=Domain::findorfail($domain->domain_id);
      $full_domain=env('APP_PROTOCOL').$request->domain;
      $current_domain->domain=$request->domain;
      $current_domain->full_domain=$full_domain;
      $current_domain->save();
      

    }

    return response()->json(['Domain Updated']);
    	


	}

	public function destroy(Request $request)
	{
		if (!Auth()->user()->can('domain.delete')) {
          return abort(401);
        }
       
        if ($request->ids) {
            if ($request->method != 'delete') {
                foreach ($request->ids as $id) {
                    $domain=Requestdomain::find($id);
                    $domain->status=$request->method;
                    $domain->save();
                }
                
            }
            else{
                foreach ($request->ids as $id) {
                    Requestdomain::destroy($id);
                }
            }
        }

        return response()->json(['Success']);
	}
}
