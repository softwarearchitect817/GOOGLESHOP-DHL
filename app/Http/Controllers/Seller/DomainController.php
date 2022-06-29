<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Domain;
use App\Models\Userplan;
use App\Models\Requestdomain;
use App\Option;
use Carbon\Carbon;
use Http;
class DomainController extends Controller
{
    public function index()
    {
    	 
       abort_if(getenv("AUTO_APPROVED_DOMAIN") == false,404);

    	$info=user_limit();;
    	$request=Requestdomain::where('user_id',Auth::id())->first();
    	
    	$dns=Option::where('key','instruction')->first();
    	$dns=json_decode($dns->value ?? '');
    	return view('seller.domain.config',compact('info','dns','request'));
    }

    public function store(Request $request)
    {
    	$checkisvalid=$this->is_valid_domain_name($request->domain);
         if ($checkisvalid == false) {
            $error['errors']['domain']='Please enter valid domain....!!';
           return response()->json($error,422);
        }


        $info=user_limit();;

        if (!empty($info)) {
    		
    		$plan=filter_var($info['custom_domain']);
    		
    		
    	}
    	else{
    		$plan='';
    	}


        $check_before= Requestdomain::where([['user_id',Auth::id()]])->first();
        if (!empty($check_before)) {
            $error['errors']['domain']='Oops you already customdomain created....!!';
            return response()->json($error,422);
        }

       
        if (!empty($plan)) {
            if ($plan == true) {
                 $validatedData = $request->validate([
                    'domain' => 'required|string|max:50',
                 ]);

                 $domain=strtolower($request->domain);
                 $input = trim($domain, '/');
                 if (!preg_match('#^http(s)?://#', $input)) {
                    $input = 'http://' . $input;
                 }
                $urlParts = parse_url($input);
                $domain = preg_replace('/^www\./', '', $urlParts['host'] ?? $urlParts['path']);
                
                $checkArecord=$this->dnscheckRecordA($domain);
                $checkCNAMErecord=$this->dnscheckRecordCNAME($domain);
                if ($checkArecord != true) {
                  $error['errors']['domain']='A record entered incorrectly.';
                  return response()->json($error,422);
                }

                if ($checkCNAMErecord != true) {
                    $error['errors']['domain']='CNAME record entered incorrectly.';
                    return response()->json($error,422);
                }

                $check= Domain::where('domain',$domain)->first();
                if (!empty($check)) {
                    $error['errors']['domain']='Oops domain name already taken....!!';
                    return response()->json($error,422);
                }
                $check= Requestdomain::where('domain',$domain)->first();
                if (!empty($check)) {
                    $error['errors']['domain']='Oops domain name already requested....!!';
                    return response()->json($error,422);
                }

                $subdomain= new Requestdomain;
                $subdomain->domain= $domain;
                $subdomain->user_id= Auth::id();
                $subdomain->status=2;
                $subdomain->domain_id=Auth::user()->user_domain->id;
                $subdomain->save();

                return response()->json('Custom Domain Created Successfully...!!');
            }

            $error['errors']['domain']='Sorry custom domain modules not support in your plan....!!';
            return response()->json($error,422);
        }
        $error['errors']['domain']='Opps something wrong...!!';
        return response()->json($error,422);
    }


    public function update(Request $request,$id)
    {
    	 $checkisvalid=$this->is_valid_domain_name($request->domain);
        if ($checkisvalid == false) {
            $error['errors']['domain']='Please enter valid domain....!!';
           return response()->json($error,422);
        }

        $info=user_limit();;

        if (!empty($info)) {
        
        $plan=filter_var($info['custom_domain']);
    		
    		
      	}
      	else{
      		$plan='';
      	}

        
        if (!empty($plan)) {
            if ($plan == true) {
                 $validatedData = $request->validate([
                    'domain' => 'required|string|max:50',
                 ]);

                 $domain=strtolower($request->domain);
                 $input = trim($domain, '/');
                 if (!preg_match('#^http(s)?://#', $input)) {
                    $input = 'http://' . $input;
                 }
                $urlParts = parse_url($input);
                $domain = preg_replace('/^www\./', '', $urlParts['host'] ?? $urlParts['path']);
                

                $check= Requestdomain::where('domain',$domain)->where('id','!=',$id)->first();
                if (!empty($check)) {
                    $error['errors']['domain']='Oops domain name already taken....!!';
                    return response()->json($error,422);
                }
                $check= Requestdomain::where('domain',$domain)->first();
                if (!empty($check)) {
                    $error['errors']['domain']='Oops domain name already requested....!!';
                    return response()->json($error,422);
                }

                $custom_domain= Requestdomain::where('user_id',Auth::id())->findorFail($id);
                
                if ($custom_domain->domain != $domain) {
                  $checkArecord=$this->dnscheckRecordA($domain);
                  $checkCNAMErecord=$this->dnscheckRecordCNAME($domain);
                  if ($checkArecord != true) {
                    $error['errors']['domain']='A record entered incorrectly.';
                    return response()->json($error,422);
                  }

                  if ($checkCNAMErecord != true) {
                    $error['errors']['domain']='CNAME record entered incorrectly.';
                    return response()->json($error,422);
                  }
                }

                $custom_domain->domain= $domain;    
                $custom_domain->status= 2;                
                $custom_domain->save();

                return response()->json('Custom Domain Request Updated Successfully...!!');
            }

            $error['errors']['domain']='Sorry subdomain modules not support in your plan....!!';
            return response()->json($error,422);
        }
        $error['errors']['domain']='Opps something wrong...!!';
        return response()->json($error,422);

    }

    //check is valid domain name
    public function is_valid_domain_name($domain_name)
    {
      if(filter_var(gethostbyname($domain_name), FILTER_VALIDATE_IP))
      {
        return TRUE;
      }
      return false;
   }

   //check A record
   public function dnscheckRecordA($domain)
   {
    if (env('MOJODNS_AUTHORIZATION_TOKEN') != null  && env('VERIFY_IP') == true) {
        try {
          $response=Http::withHeaders(['Authorization'=>env('MOJODNS_AUTHORIZATION_TOKEN')])->acceptJson()->get('https://api.mojodns.com/api/dns/'.$domain.'/A');
          $ip= $response['answerResourceRecords'][0]['ipAddress'];

          if ($ip == env('SERVER_IP')) {
              $ip= true;
          }
          else{
            $ip=false;
          }

        } catch (Exception $e) {
          $ip=false;
        }

        return $ip;
    }
     
     return true;
   } 


   //check crecord name
   public function dnscheckRecordCNAME($domain)
   {
    if (env('MOJODNS_AUTHORIZATION_TOKEN') != null) {
        if (env('VERIFY_CNAME') === true) {
        try {
          $response=Http::withHeaders(['Authorization'=>env('MOJODNS_AUTHORIZATION_TOKEN')])->acceptJson()->get('https://api.mojodns.com/api/dns/'.$domain.'/CNAME');
          if ($response->successful()) {
            $cname= $response['reportingNameServer'];

            if ($cname === env('CNAME_DOMAIN')) {
              $cname= true;
          }
          else{
           $cname=false;
        }

        } 
        else{
            $cname=false;
        }
              
          }
          catch (Exception $e) {
              $cname=false;
          }
          

        return $cname;
       }
      }
     
     return true;
   }
}
