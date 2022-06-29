<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;
use App\Http\Requests;
use App\Models\SystemTemplate;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SystemTemplateController extends Controller
{
    public function create()
    {
        return view ('seller.marketing.systememail.create');
    }
    
    public function store(Request $request)
    {
           $request->validate([
                  'title' => 'required|max:255',
                  'template_for' => 'required',
                  'description' => 'required',
            ]);
            $inputs = $request->all();
            $inputs['user_id'] = seller_id();
            $inputs['template_for'] = $request->template_for;
            $inputs['description'] = $request->description; 
            SystemTemplate::create($inputs);
            return response()->json(['System Email Created']);
    }
   
   public function edit_system_template(Request $request,$id)
   {
       $system_email = SystemTemplate::find($id);
       return view('seller.marketing.systememail.update_system_email',compact('system_email'));
       
   }
   
   public function update_system_template(Request $request)
   {
       
            $request->validate([
                  'title' => 'required|max:255',
                  'template_for' => 'required',
                  'description' => 'required',
            ]);
               $data = array(
                  'title' => $request->title,
                  'template_for' => $request->template_for,
                  'description' => $request->description,
            );
            SystemTemplate::where('user_id', seller_id())->update($data);
            return response()->json(['System Email Updated']);
            
   }
    
   
}