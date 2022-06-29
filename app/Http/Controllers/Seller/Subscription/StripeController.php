<?php

namespace App\Http\Controllers\Seller\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use App\Payment;
class StripeController extends Controller
{
	public function index()
	{
		return view('subscription.stripe');
	}

	public function charge(Request $request)
	{
		 if ($request->input('stripeToken')) {
  
            $gateway = Omnipay::create('Stripe');
            $gateway->setApiKey("sk_test_T15VoCB37F3rHDLaCeBzqntk00wDwbrdlT");
          
            $token = $request->input('stripeToken');
          
            $response = $gateway->purchase([
                'amount' => "20",
                'currency' => "USD",
                "email"=>"mdmaruf782@gmail.com",
                'token' => $token,
            ])->send();
          
            if ($response->isSuccessful()) {
                // payment was successful: insert transaction data into the database
                $arr_payment_data = $response->getData();
                 
                
          
             
 
                return "Payment is successful. Your payment id is: ". $arr_payment_data['id'];
            } else {
                // payment failed: display message to customer
                return $response->getMessage();
            }
        }
	}
}
