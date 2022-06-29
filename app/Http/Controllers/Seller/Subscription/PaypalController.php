<?php

namespace App\Http\Controllers\Seller\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
class PaypalController extends Controller
{

	protected $amount="5000";
	protected $currency="USD";
	protected $ClientID="ATSE4YX-Whpc--QZPoS_7mAaNreE_la4EQ3ahAiLmtOz5HSgG1UPOa9wPPqX2L-7HZ3yQGbUi15uWNKn";
	protected $ClientSecret="EPMkuiKL6kKhqjwPofNfO00CUkWrx4H4llKHEpgNQfaHRC95sSBvyWVHKZfBwwamgiXmlExX-Wy06U3n";
	public function index()
	{
		return view('subscription.paypal');
	}

	public function execute()
	{
		
		$apiContext = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential(
            $this->ClientID,     // ClientID
            $this->ClientSecret      // ClientSecret
        )
		);

		$payer = new \PayPal\Api\Payer();
		$payer->setPaymentMethod('paypal');

		$amount = new \PayPal\Api\Amount();
		$amount->setTotal($this->amount);
		$amount->setCurrency($this->currency);

		$transaction = new \PayPal\Api\Transaction();
		$transaction->setAmount($amount);

		$redirectUrls = new \PayPal\Api\RedirectUrls();
		$redirectUrls->setReturnUrl(route('seller.paypal.status'))
		->setCancelUrl(route('seller.paypal.cancel'));

		$payment = new \PayPal\Api\Payment();
		$payment->setIntent('sale')
		->setPayer($payer)
		->setTransactions(array($transaction))
		->setRedirectUrls($redirectUrls);


		try {
			$payment->create($apiContext);
			//echo $payment;

			//if success
			return $payment->getApprovalLink();
			return redirect($payment->getApprovalLink());
		}
		catch (\PayPal\Exception\PayPalConnectionException $ex) {
	    // This will print the detailed information on the exception.
	    //REALLY HELPFUL FOR DEBUGGING
				
			return redirect(route('seller.paypal.cancel'));
		}

	}

	public function status()
	{
		//if success
		return $paymentId=Request()->all();
	}

	public function cancel()
	{
		return "faild";
	}
}
