<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Userplan;
use Cart;
use App\Models\AbandonedCart;
use App\Useroption;
use App\Mail\AbandonedCartMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AbandonedCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Useroption::where('user_id', seller_id())->where('key','abandoned_cart_days')->first();
        $days = $data ? intval($data->value) : 1;
        $days = 0;
        $carts = AbandonedCart::where('user_id', seller_id())->whereDate('updated_at', '<=', Carbon::now()->subDays($days))->orderBy('created_at', 'desc')->paginate(15);
        $all_carts = AbandonedCart::where('user_id', seller_id())->whereDate('updated_at', '<=', Carbon::now()->subDays($days))->get();
        $total_num = AbandonedCart::where('user_id', seller_id())->whereDate('updated_at', '<=', Carbon::now()->subDays($days))->count();
        $potential = 0;
        foreach($all_carts as $row){
            $contents = json_decode($row->content);
            foreach($contents as $content){
                $potential += $content->subtotal;
            }
        }
        $recovered_num =  AbandonedCart::where('user_id', seller_id())->whereDate('updated_at', '<=', Carbon::now())->where('linked', 1)->count();
        $recovered_carts = AbandonedCart::where('user_id', seller_id())->whereDate('updated_at', '<=', Carbon::now())->where('linked', 1)->get();
        $recovered = 0;
        foreach($recovered_carts as $row){
            $contents = json_decode($row->content);
            foreach($contents as $content){
                $recovered += $content->subtotal;
            }
        }
        return view('seller.abandoned_cart.index', compact('carts', 'total_num', 'potential', 'recovered_num', 'recovered'));
    }
    
    public function send_email(Request $request)
    {
        foreach($request->emails as $email) {
            $data['description'] = 'Abandoned Cart Reminder';
            $data['subject']= 'Abandoned Cart Reminder';
            $str = array();
            $str = explode(',', $email);
            $email = $str[0];
            $cart_id = $str[1];
            $data['to_subscriber'] = $email;
            $data['mail_from'] = env('MAIL_TO');
            $data['url'] = domain_info('full_domain').'/cart/'.$cart_id;
            if(env('QUEUE_MAIL') == 'on'){
                Mail::to($email)->send(new AbandonedCartMail($data));
            }
            else{
                Mail::to($email)->send(new AbandonedCartMail($data));
            }
        }
    
        return response()->json(['Mail Sent Successfully']);
    }
    
    public function show($id){
        $abd_cart = AbandonedCart::find($id);
        $customer = $abd_cart->customer();
        return view('seller.abandoned_cart.show', compact('abd_cart', 'customer'));
    }
}