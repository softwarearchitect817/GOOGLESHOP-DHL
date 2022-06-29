<?php

namespace App\Http\Controllers\Seller\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Http;
class InstamojoController extends Controller
{
	public function index()
	{
		if(env('APP_DEBUG') == false){
		  $url='https://www.instamojo.com/api/1.1/payment-requests/';	
		}
		else{
		  $url='https://test.instamojo.com/api/1.1/payment-requests/';
		}
		
		$params=[
			'purpose' => 'FIFA 16',
			'amount' => '2500',
			'phone' => '9999999999',
			'buyer_name' => 'John Doe',
			'redirect_url' => route('seller.insta.status'),
			'send_email' => true,
			'send_sms' => true,
			'email' => '',
			'allow_repeated_payments' => false
		];
		$response=Http::asForm()->withHeaders([
			'X-Api-Key' => '',
			'X-Auth-Token' => ''
		])->post($url,$params);

		//echo $response;
		return redirect($response['payment_request']['longurl']);
	}

	public function status()
	{
		$response=Request()->all();

		if ($response['payment_status']=='Credit') {
			return "success";
		}
		else{
			return "faild";
		}
	}
}
