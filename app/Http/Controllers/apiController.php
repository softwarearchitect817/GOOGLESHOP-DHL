<?php

namespace App\Http\Controllers;
use SimpleXMLElement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Term;
use App\Category;
use App\Attribute;
use App\Getway;
use App\Models\Review;
use Cache;
use Session;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;
use App\Useroption;
use URL;
use App\Option;
use App\Plan;
use Auth;

class apiController extends Controller
{


   public function ups(Request $datas)
    {

      // Checkout Function

            if(Auth::check() == true){
            Auth::logout();
      }
       \Cart::setGlobalTax(tax());


        if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
        }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
        }
         if(!empty($seo)){
       JsonLdMulti::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

       SEOMeta::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
       SEOTools::twitter()->setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }

      $shop_type=domain_info('shop_type');
      $user_id=domain_info('user_id');
      if($shop_type==1){
        $locations= Category::where('user_id',$user_id)->where('type','city')->with('child_relation')->get();
      }
      else{
        $locations=[];
      }
      
     
      $getways=  Getway::where('user_id',$user_id)->where('status',1)->get();

      //Checkout Function

$fromCity=$datas->fromCity; //Corado //Boca Raton
$fromCC=$datas->fromCC; //00646  
$fromPC=$datas->fromPC; //PR     //33434  //US

$toCity=$datas->toCity;
$toCC=$datas->toCC;
$toPC=$datas->toPC;
$weight=$datas->pounds;




      try {
  
  // configuration
  $access = $datas->accessKey; //"CDB44889246BAC72";
  $userid = $datas->userId; //"nsevimov";
  $passwd = $datas->password; //"Mamkamu.php!123";

  $endpointurl = "https://wwwcie.ups.com/ups.app/xml/Rate";
  $outputFileName = "XOLTResult.xml"; 
  
  
  // create a simple xml object for AccessRequest & RateRequest
  $accessRequesttXML = new SimpleXMLElement ( "<AccessRequest></AccessRequest>" ); 
  $rateRequestXML = new SimpleXMLElement ( "<RatingServiceSelectionRequest></RatingServiceSelectionRequest>" );
  
  // create AccessRequest XML
  $accessRequesttXML->addChild ( "AccessLicenseNumber", $access );
  $accessRequesttXML->addChild ( "UserId", $userid );
  $accessRequesttXML->addChild ( "Password", $passwd ); 
  
  // create RateRequest XML
  $request = $rateRequestXML->addChild ( 'Request' );
  $request->addChild ( "RequestAction", "Rate" );
  $request->addChild ( "RequestOption", "Rate" );
  
  $shipment = $rateRequestXML->addChild ( 'Shipment' );
  $shipper = $shipment->addChild ( 'Shipper' );
  $shipper->addChild ( "Name", "Name" );
  $shipper->addChild ( "ShipperNumber", "" );
  $shipperddress = $shipper->addChild ( 'Address' );
  $shipperddress->addChild ( "AddressLine1", "Address Line" );
  $shipperddress->addChild ( "City", $toCity );
  $shipperddress->addChild ( "PostalCode", $toPC );
  $shipperddress->addChild ( "CountryCode", $toCC );
  
  $shipTo = $shipment->addChild ( 'ShipTo' );
  $shipTo->addChild ( "CompanyName", "Company Name" );
  $shipToAddress = $shipTo->addChild ( 'Address' );
  $shipToAddress->addChild ( "AddressLine1", "Address Line" );
  $shipToAddress->addChild ( "City", $toCity );
  $shipToAddress->addChild ( "PostalCode", $toPC );
  $shipToAddress->addChild ( "CountryCode", $toCC );
  
  $shipFrom = $shipment->addChild ( 'ShipFrom' );
  $shipFrom->addChild ( "CompanyName", "Company Name" );
  $shipFromAddress = $shipFrom->addChild ( 'Address' );
  $shipFromAddress->addChild ( "AddressLine1", "Address Line" );
  $shipFromAddress->addChild ( "City", $fromCity );
  //$shipFromAddress->addChild ( "StateProvinceCode", "FL" );
  $shipFromAddress->addChild ( "PostalCode", $fromPC );
  $shipFromAddress->addChild ( "CountryCode", $fromCC );
  
  $service = $shipment->addChild ( 'Service' );
  $service->addChild ( "Code", "02" );
  $service->addChild ( "Description", "2nd Day Air" );
  
  $package = $shipment->addChild ( 'Package' );
  $packageType = $package->addChild ( 'PackagingType' );
  $packageType->addChild ( "Code", "02" );
  $packageType->addChild ( "Description", "UPS Package" );
  
  $packageWeight = $package->addChild ( 'PackageWeight' );
  $unitOfMeasurement = $packageWeight->addChild ( 'UnitOfMeasurement' );
  $unitOfMeasurement->addChild ( "Code", "LBS" );
  $packageWeight->addChild ( "Weight", $weight*2.20462 ); 
  
  $requestXML = $accessRequesttXML->asXML () . $rateRequestXML->asXML (); 
  
  // create Post request
  $form = array (
      'http' => array (
          'method' => 'POST',
          'header' => 'Content-type: application/x-www-form-urlencoded',
          'content' => "$requestXML" 
      ) 
  );
  
  $request = stream_context_create ( $form );
  $browser = fopen ( $endpointurl, 'rb', false, $request );
  if (! $browser) {
    throw new Exception ( "Connection failed." );
  }
  
  // get response
  $response = stream_get_contents ( $browser );
  fclose ( $browser );
  
  if ($response == false) {
    throw new Exception ( "Bad data." );
  } else {
    // save request and response to file
    $fw = fopen ( $outputFileName, 'w' );
    fwrite ( $fw, "Request: \n" . $requestXML . "\n" );
    fwrite ( $fw, "Response: \n" . $response . "\n" );
    fclose ( $fw );
    
    // get response status

    $resp = new SimpleXMLElement ( $response );
     $array_data = json_decode(json_encode($resp), true);
     $array_data=$array_data['RatedShipment'];
  
    
    // print_r('<pre>');print_r($array_data);print_r('</pre>'); exit;
     Session::put('ratesUSPS','ups');
    // return view('seller.shipping.method.app',compact('array_data'));
      return view('frontend.bigbag.checkout',compact('array_data','locations','getways'));
      
  }

} catch ( Exception $ex ) {
  echo $ex;
}


    }





       public function usps(Request $datas)
    {

       // Checkout Function

            if(Auth::check() == true){
            Auth::logout();
      }
       \Cart::setGlobalTax(tax());


        if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
        }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
        }
         if(!empty($seo)){
       JsonLdMulti::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

       SEOMeta::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
       SEOTools::twitter()->setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }

      $shop_type=domain_info('shop_type');
      $user_id=domain_info('user_id');
      if($shop_type==1){
        $locations= Category::where('user_id',$user_id)->where('type','city')->with('child_relation')->get();
      }
      else{
        $locations=[];
      }
      
     
      $getways=  Getway::where('user_id',$user_id)->where('status',1)->get();

      //Checkout Function

//$username = '806SHIFL5701';
$username = $datas->username;
$originZip=$datas->origin;
$destZip=$datas->dest;
$pounds=$datas->pounds;
$ounces=$datas->ounces;

// $originZip=78238;
  //$destZip=96266;
  // $pounds=5;
  // $ounces=5;

    $datas2 = "API=RateV4&XML=<RateV4Request USERID=\"{$username}\">

<Revision>2</Revision>

 <Package ID=\"1ST\">

 <Service>Priority</Service>

 <ZipOrigination>{$originZip}</ZipOrigination>

 <ZipDestination>{$destZip}</ZipDestination>

<Pounds>{$pounds}</Pounds>

 <Ounces>{$ounces}</Ounces>

 <Container/>

 <Machinable>true</Machinable>

 </Package>

 </RateV4Request>";


 //$data=(array)$data;
 $fields = array(
     'API'=>'RateV4',
     'XML' => $datas2
 );

 $url = 'http://production.shippingapis.com/ShippingAPITest.dll?' . http_build_query($fields);
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
 $data = curl_exec($ch);
 curl_close($ch);

 //Convert the XML result into array
 $array_data = json_decode(json_encode(simplexml_load_string($data)), true);
 $array_data=$array_data['Package'];
$specialService=$array_data['Postage']['SpecialServices']['SpecialService'];

// print_r('<pre>');print_r($array_data);print_r('</pre>'); exit;

Session::put('ratesUSPS','usps');
//return view('seller.shipping.method.app',compact('array_data','specialService'));
 return view('frontend.bigbag.checkout',compact('array_data','locations','getways'));

    }


    

      public function dhl(Request $datas)
    {

       // Checkout Function

            if(Auth::check() == true){
            Auth::logout();
      }
       \Cart::setGlobalTax(tax());


        if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
        }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
        }
         if(!empty($seo)){
       JsonLdMulti::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

       SEOMeta::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
       SEOTools::twitter()->setTitle('Checkout - '.$seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }

      $shop_type=domain_info('shop_type');
      $user_id=domain_info('user_id');
      if($shop_type==1){
        $locations= Category::where('user_id',$user_id)->where('type','city')->with('child_relation')->get();
      }
      else{
        $locations=[];
      }
      
     
      $getways=  Getway::where('user_id',$user_id)->where('status',1)->get();

      //Checkout Function

$date=$datas->date;
$fromCC=$datas->fromCC;
$fromPC=$datas->fromPC;
$toCC=$datas->toCC;
$toPC=$datas->toPC;
$weight=$datas->pounds;



      $datas2 = '<?xml version="1.0" encoding="UTF-8"?>
<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
  <GetQuote>
    <Request>
      <ServiceHeader>
        <MessageTime>'.date('c').'</MessageTime>
        <MessageReference>1234567890123456789012345678901</MessageReference>
        <SiteID>v62_1ykV4eBKJ7</SiteID>
        <Password>an9PzE9ZsC</Password>
      </ServiceHeader>
    </Request>

    <From>
        <CountryCode>'.$fromCC.'</CountryCode>
        <Postalcode>'.$fromPC.'</Postalcode>
    </From>
    <BkgDetails>
      <PaymentCountryCode>US</PaymentCountryCode>
      <Date>'.$date.'</Date>
      <ReadyTime>PT10H21M</ReadyTime>
            <ReadyTimeGMTOffset>+01:00</ReadyTimeGMTOffset>
            <DimensionUnit>CM</DimensionUnit>

            <WeightUnit>KG</WeightUnit>
            <Pieces><Piece>
                <PieceID>1</PieceID>
                <Height>5</Height>
                <Depth>5</Depth>
                <Width>5</Width>   
                <Weight>'.$weight.'</Weight>
            </Piece></Pieces>
            <IsDutiable>N</IsDutiable>
            <NetworkTypeCode>AL</NetworkTypeCode>
        </BkgDetails>
        <To>
            <CountryCode>'.$toCC.'</CountryCode>
            <Postalcode>'.$toPC.'</Postalcode>
        </To>       
    </GetQuote>
</p:DCTRequest>';
$tuCurl = curl_init();
curl_setopt($tuCurl, CURLOPT_URL, "https://xmlpitest-ea.dhl.com/XMLShippingServlet");
curl_setopt($tuCurl, CURLOPT_PORT , 443);
curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
curl_setopt($tuCurl, CURLOPT_HEADER, 0);
curl_setopt($tuCurl, CURLOPT_POST, 1);
curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $datas2);
curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","SOAPAction: \"/soap/action/query\"", "Content-length: ".strlen($datas2)));

$tuData = curl_exec($tuCurl);

curl_close($tuCurl);
$xml = simplexml_load_string($tuData);
$array_data = json_decode(json_encode($xml), true);
// print_r('<pre>');print_r($array_data);print_r('</pre>'); exit;
$array_data=$array_data['GetQuoteResponse']['BkgDetails']['QtdShp'];
$specialService=$array_data['QtdShpExChrg'];
    
    

       
Session::put('ratesUSPS','dhl');
//return view('seller.shipping.method.app',compact('array_data','specialService'));
 return view('frontend.bigbag.checkout',compact('array_data','locations','getways'));
    }



    public function usps_label(){


    }


     public function ups_label(){

      // Configuration
$accessLicenseNumber = "CDB44889246BAC72";
$userId = "nsevimov";
$password = "Mamkamu.php!123";

$endpointurl = 'http://153.2.133.60:48011/xoltws_ship/LBRecovery';
$outputFileName = "XOLTResult.xml";

try {
  
  // Create AccessRequest XMl
  $accessRequesttXML = new SimpleXMLElement ( "<AccessRequest></AccessRequest>" );
  $accessRequesttXML->addChild ( "AccessLicenseNumber", $accessLicenseNumber );
  $accessRequesttXML->addChild ( "UserId", $userId );
  $accessRequesttXML->addChild ( "Password", $password );
  
  // Create LabelRecoveryRequest XMl
  $labelRecoveryRequestXML = new SimpleXMLElement ( "<LabelRecoveryRequest ></LabelRecoveryRequest >" );
  $request = $labelRecoveryRequestXML->addChild ( 'Request' );
  $request->addChild ( "RequestAction", "LabelRecovery" );
  
  $labelSpecification = $labelRecoveryRequestXML->addChild ( 'LabelSpecification' );
  $labelSpecification->addChild ( "HTTPUserAgent" );
  $labelImageFormat = $labelSpecification->addChild ( 'LabelImageFormat' );
  $labelImageFormat->addChild ( "Code", "GIF" );
  
  $labelDelivery = $labelRecoveryRequestXML->addChild ( 'LabelDelivery' );
  $labelDelivery->addChild ( "LabelLinkIndicator" );
  $labelDelivery->addChild ( "ResendEMailIndicator" );
  
  $labelRecoveryRequestXML->addChild ( "TrackingNumber", "Your Tracking Number" );
  
  $requestXML = $accessRequesttXML->asXML () . $labelRecoveryRequestXML->asXML ();
  
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $endpointurl );
  curl_setopt( $ch, CURLOPT_POST, true );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $requestXML );
  $response = curl_exec($ch);
  curl_close($ch);
  
  if ($response == false) {
    throw new Exception ( "Bad data." );
  } else {
    // save request and response to file
    $fw = fopen ( $outputFileName, 'w' );
    fwrite ( $fw, "Request: \n" . $requestXML . "\n" );
    fwrite ( $fw, "Response: \n" . $response . "\n" );
    fclose ( $fw );
    
    // get response status
    $resp = new SimpleXMLElement ( $response );
    echo $resp->Response->ResponseStatusDescription . "\n";
  }
  
  Header ( 'Content-type: text/xml' );
} catch ( Exception $ex ) {
  echo $ex;
}
    }


     public function dhl_label(){

      
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-sandbox.dhl.com/dgff/transportation/shipment-label",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"shipmentID\":\"S21000645937\",\"housebillNumber\":\"8FE7018\",\"additionalInformation\":\"This is a test label\",\"mimeType\":\"pdf\",\"acceptContentType\":\"application/octet-stream\"}",
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer REPLACE_BEARER_TOKEN",
    "content-type: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
    }




}
