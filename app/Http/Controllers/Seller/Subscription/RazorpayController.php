<?php

namespace App\Http\Controllers\Seller\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Http;
use Razorpay\Api\Api;
class RazorpayController extends Controller
{
	protected $key_id="rzp_test_siWkeZjPLsYGSi";
	protected $key_secret="jmIzYyrRVMLkC9BwqCJ0wbmt";
	protected $user_name="maruf test";
	protected $currency="USD";
	protected $email="mohammadmaruf020@gmail.com";
	protected $phone="0182000000";
	protected $address="";
	protected $amount=100;

	protected $payment_id=null;
	
	protected $url;

	public function index()
	{
		$api = new Api($this->key_id, $this->key_secret);
		$referance_id="test123";
		$order = $api->order->create(array(
			'receipt' => $referance_id,
			'amount' => $this->amount*100,
			'currency' => $this->currency
		)
	    );

		 // Return response on payment page
		$response = [
			'orderId' => $order['id'],
			'razorpayId' => $this->key_id,
			'amount' => $this->amount*100,
			'name' => $this->user_name,
			'currency' => $this->currency,
			'email' => $this->email,
			'contactNumber' => $this->phone,
			'address' => $this->address,
			'description' => 'Testing description',
		];

        // Let's checkout payment page is it working
		return view('subscription.razorpay',compact('response'));
	}

	

	public function status(Request $request)
	{
    // Now verify the signature is correct . We create the private function for verify the signature
		$signatureStatus = $this->SignatureVerify(
			$request->all()['rzp_signature'],
			$request->all()['rzp_paymentid'],
			$request->all()['rzp_orderid']
		);

    // If Signature status is true We will save the payment response in our database
    // In this tutorial we send the response to Success page if payment successfully made
		if($signatureStatus == true)
		{
        // You can create this page
			dd($this->payment_id);
			//for success
		}
		else{
        // You can create this page
			dd($signatureStatus);
			//for faild
		}
	}

// In this function we return boolean if signature is correct
	private function SignatureVerify($_signature,$_paymentId,$_orderId)
	{
		try
		{
        // Create an object of razorpay class
			$api = new Api($this->key_id, $this->key_secret);
			$attributes  = array('razorpay_signature'  => $_signature,  'razorpay_payment_id'  => $_paymentId ,  'razorpay_order_id' => $_orderId);
			$order  = $api->utility->verifyPaymentSignature($attributes);
			$this->payment_id=$_paymentId;
			return true;
		}
		catch(\Exception $e)
		{
        // If Signature is not correct its give a excetption so we use try catch
			return false;
		}
	}
}
