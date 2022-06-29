<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Auth;
use App\Models\NewsLetter;
use App\Models\User;
use App\Models\Userplan;
use App\Models\Customer;
use App\Http\Requests;
use App\Mail\BulkEmail;
use App\Plan;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
   
    public function NewsLetter()
    {
      return view('seller.marketing.newsletter');
    }
    public function store_NewsLetter(Request $request)
    {
      $request->validate([
              'title' => 'required|max:255',
              'description' => 'required',
        ]);
        
        $newsletter = new NewsLetter;
        $newsletter->title = $request->title;
        $newsletter->description = $request->description;
        $newsletter->user_id = seller_id();
        $newsletter->save();
         return response()->json(['NewsLetter Created']);
    }
    
    public function edit_newsletter($id)
    {
         $newsletter = NewsLetter::find($id);
         return view('seller.marketing.newsletter.edit', compact('newsletter'));
    }
      public function update_NewsLetter(Request $request, $id)
    {
          $request->validate([
                  'title' => 'required|max:255',
                  'description' => 'required',
            ]);
            
            $newsletter = NewsLetter::find($id);
            $newsletter->title = $request->title;
            $newsletter->description = $request->description;
            $newsletter->user_id = seller_id();
            $newsletter->update();
            return response()->json(['NewsLetter Updated']);
        
    }
    
    public function delete_newsletter($id)
    {
        $newsletter = Newsletter::find($id);
        $newsletter->delete($id);
        return back()->with('message', 'NewsLetter Deleted');
        
    }
    
    public function all_users()
    {
        $users = Customer::where('created_by', seller_id())->where('subscribe', 1)->orderBy('name')->get();
        $templates = Newsletter::where('user_id', seller_id())->orderBy('title')->get();
        return view('seller.marketing.newsletter.user',compact('users','templates'));
    }
    
    public function send_email(Request $request)
    {
        
        $validatedData = $request->validate([
            'template_id' => 'required|integer',
            'emails' => 'required|array|min:1',
        ]);
        
        $userdata = Userplan::where('user_id', seller_id())->latest()->first();
        $userplan_data = $userdata->plan_info->data;
        $userplan_data = json_decode($userplan_data);
        $subscribers_total = (int)$userplan_data->total_emails;
        $sent_emails = $userdata->sent_emails;
        $credit_emails = $userdata->credit_emails;
        $total = $subscribers_total + $credit_emails;
        
        foreach($request->emails as $email){
            if($total > $sent_emails){
                
                $data['description']= Newsletter::where('id',$request->template_id)->pluck('description')->first();
                $data['subject']=Newsletter::where('id',$request->template_id)->pluck('title')->first();
                $data['to_subscriber'] = $email;
                $data['mail_from'] = env('MAIL_TO');
                if(env('QUEUE_MAIL') == 'on'){
                    Mail::to($email)->send(new BulkEmail($data));
                //  dispatch(new \App\Jobs\SendInvoiceEmail($data));
                }
                else{
                    Mail::to($email)->send(new BulkEmail($data));
                }
                
                $sent_emails = $sent_emails + 1;
                Userplan::where('user_id', seller_id())->latest()->update(['sent_emails' => $sent_emails]);
            }else{
                return response()->json(['Your Email Limit Finished Contact To Admin Please Or upgrade your Plan.']);
                
            }
            
        }
        return response()->json(['Mail Sent Successfully']);
    }
    
   
}