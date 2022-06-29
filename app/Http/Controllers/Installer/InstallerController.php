<?php

namespace App\Http\Controllers\Installer;
use Lpress\Verify\Everify;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Artisan;
use DB;
use Illuminate\Support\Str;
use File;

class InstallerController extends Controller
{

    public function install()
    {
        
       try {
          DB::connection()->getPdo();
          if(DB::connection()->getDatabaseName()){
            return redirect()->route(404);
          }else{
            $phpversion = phpversion();
            $mbstring = extension_loaded('mbstring');
            $bcmath = extension_loaded('bcmath');
            $ctype = extension_loaded('ctype');
            $json = extension_loaded('json');
            $openssl = extension_loaded('openssl');
            $pdo = extension_loaded('pdo');
            $tokenizer = extension_loaded('tokenizer');
            $xml = extension_loaded('xml');

            $info = [
                'phpversion' => $phpversion,
                'mbstring' => $mbstring,
                'bcmath' => $bcmath,
                'ctype' => $ctype,
                'json' => $json,
                'openssl' => $openssl,
                'pdo' => $pdo,
                'tokenizer' => $tokenizer,
                'xml' => $xml,
            ];
            return view('installer.requirments',compact('info'));
          }
        } catch (\Exception $e) {
            $phpversion = phpversion();
            $mbstring = extension_loaded('mbstring');
            $bcmath = extension_loaded('bcmath');
            $ctype = extension_loaded('ctype');
            $json = extension_loaded('json');
            $openssl = extension_loaded('openssl');
            $pdo = extension_loaded('pdo');
            $tokenizer = extension_loaded('tokenizer');
            $xml = extension_loaded('xml');

            $info = [
                'phpversion' => $phpversion,
                'mbstring' => $mbstring,
                'bcmath' => $bcmath,
                'ctype' => $ctype,
                'json' => $json,
                'openssl' => $openssl,
                'pdo' => $pdo,
                'tokenizer' => $tokenizer,
                'xml' => $xml,
            ];
            return view('installer.requirments',compact('info'));
        }

  
        
    }

    public function info()
    {

        try {
          DB::connection()->getPdo();
          if(DB::connection()->getDatabaseName()){
            return redirect()->route(404);
          }else{
            return view('installer.info'); 
          }
        } catch (\Exception $e) {
            return view('installer.info'); 
        }
           
    }

    public function send(Request $request)
    {

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
             $app_protocol = "https://";  
        } 
        else{
            $app_protocol = "http://";   
        }  
         
    
        $domain=strtolower(url('/'));
        $input = trim($domain, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        $domain = preg_replace('/^www\./', '', $urlParts['host']);
        $app_protocol_less_url=rtrim($domain, '/');

        $APP_NAME = Str::slug($request->app_name);
        $PUSHER_APP_KEY = $request->PUSHER_APP_KEY;
        $PUSHER_APP_CLUSTER = $request->PUSHER_APP_CLUSTER;
        $app_protocol_less_url=$app_protocol_less_url;
        $app_protocol=$app_protocol;
        $APP_URL_WITHOUT_WWW=str_replace('www.','', url('/'));
        $txt ="APP_NAME=".$APP_NAME."
APP_ENV=local
APP_KEY=base64:kZN2g9Tg6+mi1YNc+sSiZAO2ljlQBfLC3ByJLhLAUVc=
APP_DEBUG=true
APP_URL=".$request->app_url."
APP_PROTOCOLESS_URL=".$app_protocol_less_url."
APP_URL_WITHOUT_WWW=".$APP_URL_WITHOUT_WWW."
APP_PROTOCOL=".$app_protocol."
MULTILEVEL_CUSTOMER_REGISTER=false
LOG_CHANNEL=stack
LOG_LEVEL=debug
DB_CONNECTION=".$request->db_connection."
DB_HOST=".$request->db_host."
DB_PORT=".$request->db_port."
DB_DATABASE=".$request->db_name."
DB_USERNAME=".$request->db_user."
DB_PASSWORD=".$request->db_pass."\n
BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120\n
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379\n
QUEUE_MAIL=off
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_TO=
MAIL_NOREPLY=
MAIL_FROM_NAME=\n
TIMEZONE=UTC
DEFAULT_LANG=en";
       File::put(base_path('.env'),$txt);
       return "Sending Credentials";
    }
    
    

    public function check()
    {
        try {
          DB::connection()->getPdo();
            if(DB::connection()->getDatabaseName()){
                return "Database Installing";
            }else{
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        
    }

    public function migrate()
    {
        ini_set('max_execution_time', '0');
        \Artisan::call('migrate:fresh');
        return "Demo Importing";
    }

  

   

}
