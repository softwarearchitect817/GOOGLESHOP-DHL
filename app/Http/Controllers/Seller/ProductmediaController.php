<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Media;
use File;
use Image;
use Illuminate\Support\Facades\Storage;
use App\Options;
use App\Postmedia;
use Illuminate\Support\Str;
use Validator,Response;
use ArtisansWeb\Optimizer;
use Auth;
use Helper;
use App\Helper\Sitehelper\Sitehelper;
class ProductmediaController extends Controller
{
    protected $filename;
    protected $ext;
    protected $fullname;
    protected $path;

    public function __construct()
    {
       $this->middleware('auth');;
    }

    public function bulk_upload()
    {
        if (!Auth()->user()->can('media.upload')) {
            abort(401);
        }
        return view('admin.media.create'); 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth()->user()->can('media.list')) {
            abort(401);
        } 
       
        $medias=Media::latest()->paginate(30);  
        $src=$request->src ?? '';
        return view('admin.media.index',compact('medias','src'));
      
        
    }

    public function json(Request $request){
           
        $row=Media::latest()->select('id','name','url')->paginate(12);
        return response()->json($row);  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
       $limit=user_limit();
       if ($limit['storage'] <= str_replace(',', '', folderSize('uploads/'.Auth::id()))) {
          return response()->json('Maximum storage limit exceeded',401);
        }
     request()->validate([
        'media.*' => 'required|image'

      ]);
     
     $auth_id=Auth::id();
      
       
        
       
        $imageSizes= json_decode(imageSizes());

        if($request->hasfile('media'))
        {
            foreach($request->file('media') as $image)
            {

                $name = uniqid().date('dmy').time(). "." . $image->getClientOriginalExtension();
                $ext= $image->getClientOriginalExtension();

                
                
                $this->fullname=date('dmy').time().uniqid().'.'.$image->getClientOriginalExtension();

                $path='uploads/'.$auth_id.date('/y').'/'.date('m').'/';
                $this->path=$path; 
               
                if(substr($image->getMimeType(), 0, 5) == 'image' &&  $ext != 'ico') {
                  
                  $image->move($path, $name); 
                  $compress= $this->run($path.$name,$ext,60); 

                  // if (file_exists($path.$name) ) {
                  //   if (!in_array(strtolower($ext), array('png','gif'))) {
                      
                  //        unlink($path.$name);
                  //   }
                   
                  // }
                   
               

                
                    $schemeurl=parse_url(url('/'));
                    if ($schemeurl['scheme']=='https') {
                       $url=substr(url('/'), 6);
                    }
                    else{
                         $url=substr(url('/'), 5);
                    }

                    $fileUrl=$url.'/'.$compress['data']['image'];
                    $newpath=$path;
                    $filename=$compress['data']['image'];
                    $imgArr=explode('.', $compress['data']['image']);
                     if (file_exists($compress['data']['image'])) {
                     foreach ($imageSizes as $size) {
                       
                           $img=Image::make($compress['data']['image']);
                           $img->fit($size->width,$size->height);
                           
                           $img->save($imgArr[0].$size->key.'.'.$imgArr[1]);
                        }
                       
                     }
                
                 
                $media=new Media;
                $media->name=$filename;
                $media->url=$fileUrl;
                $media->user_id=$auth_id;
                $media->save();
                $data[] = $media;

                $dta['media_id']=$media->id;
                $dta['term_id']=$request->term;
                Postmedia::insert($dta);  

            }
           
                      
               
            }
            return response($data);
        }


        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $media=Media::find($id);
        return response()->json($media);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        if ($request->m_id) {
            $id=base64_decode($request->m_id);
            mediaRemove($id);
        }
      return response()->json('Delete Success');
    }

   








    private function create($image, $name, $type, $size, $c_type, $level) {
        $im_name = $this->fullname;
        $path=$this->path;
        $im_output = $path.$im_name;
        $im_ex = explode('.', $im_output); // get file extension
        
        // create image
        if($type == 'image/jpeg'){
            $im = imagecreatefromjpeg($image); // create image from jpeg
        }
        elseif($type == 'image/gif'){
           $im = imagecreatefromgif($image);
        }
        elseif($type == 'image/png'){
            $im=imagecreatefrompng($image);
        }
        else{
           $im = imagecreatefromjpeg($image);
        }
        
        // compree image
        if($c_type){
            $im_name = str_replace(end($im_ex), 'jpg', $im_name);
            $im_name = str_replace(end($im_ex), 'png', $im_name);
            $im_name = str_replace(end($im_ex), 'gif', $im_name);
            $im_name = str_replace(end($im_ex), 'jpeg', $im_name); // replace file extension
            $im_output = str_replace(end($im_ex), 'webp', $im_output); // replace file extension
          
            if(!empty($level)){
                imagewebp($im, $im_output, 60); // if level = 2 then quality = 80%
            }else{
                imagewebp($im, $im_output, 100); // default quality = 100% (no compression)
            }
            $im_type = 'image/webp';
            // image destroy
            imagedestroy($im);
        }
        else{

        }
        
        
        
        // output original image & compressed image
        $im_size = filesize($im_output);
        $info = array(
                'name' => $im_name,
                'image' => $im_output,
                'type' => $im_type,
                'size' => $im_size 
        );
        return $info;
    }

    private function check_transparent($im) {

        $width = imagesx($im); // Get the width of the image
        $height = imagesy($im); // Get the height of the image

        // We run the image pixel by pixel and as soon as we find a transparent pixel we stop and return true.
        for($i = 0; $i < $width; $i++) {
            for($j = 0; $j < $height; $j++) {
                $rgba = imagecolorat($im, $i, $j);
                if(($rgba & 0x7F000000) >> 24) {
                    return true;
                }
            }
        }

        // If we dont find any pixel the function will return false.
        return false;
    }  
    
    function run($image, $c_type, $level = 0) {

        // get file info
        $im_info = getImageSize($image);
        $im_name = basename($image);
        $im_type = $im_info['mime'];
        $im_size = filesize($image);
        
        // result
        $result = array();
        
        // cek & ricek
        if(in_array($c_type, array('jpeg','jpg','JPG','JPEG','gif','GIF','png','PNG'))) { // jpeg, png, gif only
           
             $result['data'] = $this->create($image, $im_name, $im_type, $im_size, $c_type, $level);

            return $result;
            
        }
    }
}
