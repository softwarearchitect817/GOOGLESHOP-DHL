<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Option;
use App\Category;
use App\Categorymeta;
use App\Helper\Sitehelper\Sitehelper;
use Str;
use Auth;
class FrontendController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->type=="features") {
            $validatedData = $request->validate([
                'title' => 'required|max:100',
                'description' => 'required|max:200',
                'file' => 'required|image|max:1000',
            ]);

            if ($request->file) {
                 $fileName = time().'.'.$request->file->extension();  
                 $request->file->move('uploads/1/'.date('Y/m/'), $fileName);
                 $fileName='uploads/1/'.date('Y/m/').$fileName;
            }

            $category=new Category;
            $category->type="features";
            $category->name=$request->title;
            $category->slug=Str::slug($request->title);
            $category->is_admin=1;
            $category->user_id=Auth::id();
            $category->save();

            $meta=new Categorymeta;
            $meta->category_id =$category->id;
            $meta->type ="preview";
            $meta->content =$fileName;
            $meta->save();

            $meta=new Categorymeta;
            $meta->category_id =$category->id;
            $meta->type ="excerpt";
            $meta->content =$request->description;
            $meta->save();

            return response()->json(['Features Created']);
        }

        if ($request->type=="widget") {
            $validatedData = $request->validate([
                'title' => 'required|max:100',
                'count' => 'required|max:100',
                'file' => 'required|image|max:1000',
            ]);

            if ($request->file) {
                 $fileName = time().'.'.$request->file->extension();  
                 $request->file->move('uploads/1/'.date('Y/m/'), $fileName);
                 $fileName='uploads/1/'.date('Y/m/').$fileName;
            }

            $category=new Category;
            $category->type="widget";
            $category->name=$request->title;
            $category->slug=$request->count;
            $category->is_admin=1;
            $category->user_id=Auth::id();
            $category->save();

            $meta=new Categorymeta;
            $meta->category_id =$category->id;
            $meta->type ="preview";
            $meta->content =$fileName;
            $meta->save();

            return response()->json(['Widget Created']);
        }

        if ($request->type=="testimonial") {
            

            $category=new Category;
            $category->type="testimonial";
            $category->name=$request->name;
            $category->slug=$request->position;
            $category->is_admin=1;
            $category->user_id=Auth::id();
            $category->save();

           

            $meta=new Categorymeta;
            $meta->category_id =$category->id;
            $meta->type ="excerpt";
            $meta->content =$request->feedback;
            $meta->save();


            return response()->json(['Testimonial Created']);
        }

        if ($request->type=="brand") {
            $validatedData = $request->validate([
                'name' => 'required|max:100',
                'file' => 'required|image|max:1000',
            ]);

            if ($request->file) {
                 $fileName = time().'.'.$request->file->extension();  
                 $request->file->move('uploads/1/'.date('Y/m/'), $fileName);
                 $fileName='uploads/1/'.date('Y/m/').$fileName;
            }

            $category=new Category;
            $category->type="brand";
            $category->name=$request->name;
            $category->is_admin=1;
            $category->user_id=Auth::id();
            $category->save();

            $meta=new Categorymeta;
            $meta->category_id =$category->id;
            $meta->type ="preview";
            $meta->content =$fileName;
            $meta->save();

            return response()->json(['Brand Created']);
        }

        if ($request->type=="support") {


        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($type)
    {
        if (!Auth()->user()->can('site.settings')) {
            return abort(401);
        }

        if ($type=="header") {
            $option=Option::where('key','header')->first();
            $info=json_decode($option->value ?? '');
            return view('admin.frontend.header',compact('info'));
        }
        if ($type=="features") {
            $posts=Category::where('type','features')->where('is_admin',1)->latest()->get(); 
            return view('admin.frontend.features',compact('posts'));
        }
        if ($type=="counter_area") {
            
            $option=Option::where('key','counter_area')->first();
            $info=json_decode($option->value ?? '');
            return view('admin.frontend.counter_area',compact('info','type'));
        }
        if ($type=="about_1") {            
            $option=Option::where('key','about_1')->first();           
            $info=json_decode($option->value);
           
            return view('admin.frontend.about',compact('info','type'));
        }
        if ($type=="about_2") {
            $option=Option::where('key','about_2')->first();
            $info=json_decode($option->value ?? '');
            return view('admin.frontend.about',compact('info','type'));
        }
        if ($type=="about_3") {

            $option=Option::where('key','about_3')->first();
            $info=json_decode($option->value ?? '');
            return view('admin.frontend.about',compact('info','type'));
        }

        if ($type=="testimonials") {
            $posts=Category::where('type','testimonial')->where('is_admin',1)->latest()->get(); 
            return view('admin.frontend.testimonials',compact('posts'));
        }

        if ($type=="brands") {
            $posts=Category::where('type','brand')->with('preview')->where('is_admin',1)->latest()->get(); 
            return view('admin.frontend.brands',compact('posts'));
        }

        if ($type=="ecom_features") {
             $option=Option::where('key','ecom_features')->first();
             $info=json_decode($option->value ?? '');
            return view('admin.frontend.ecom_features',compact('info','type'));
        }

        if ($type=="contact") {
            return view('admin.frontend.contact');
        }

        if ($type == 'instruction') {
        
         $option=Option::where('key','instruction')->first();
         $info=json_decode($option->value ?? '');
         return view('admin.frontend.instruction',compact('info','type'));
        }

        if ($type == 'css-js') {
        
          if (file_exists('uploads/support.js')) {
            $support=file_get_contents('uploads/support.js');
          }
          else{
            $support='';
          }

          if (file_exists('uploads/additional.js')) {
            $additional_js=file_get_contents('uploads/additional.js');
          }
          else{
            $additional_js='';
          }
          if (file_exists('uploads/additional.css')) {
            $additional_css=file_get_contents('uploads/additional.css');
          }
          else{
            $additional_css='';
          }
         return view('admin.frontend.script',compact('support','additional_js','additional_css','type'));
        }
        
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

        if ($id=='instruction') {
            $option=Option::where('key',$id)->first();
            $info=json_decode($option->value ?? '');
            if (empty($option)) {
                $option=new Option;
                $option->key=$id;
            }

            $data['dns_configure_instruction']=$request->dns_configure_instruction;
            $data['support_instruction']=$request->support_instruction;
            $option->value=json_encode($data);
            $option->save();
            return response()->json(['Update Success']);
        }

        if ($id=='css-js') {
            
             \File::put('uploads/additional.css',$request->additional_css);
             \File::put('uploads/additional.js',$request->additional_js);
             \File::put('uploads/support.js',$request->support_js);

            return response()->json(['Update Success']);
        }
        if ($id=="header") {
            $validatedData = $request->validate([
                'file' => 'image|max:1000',
            ]);

            $option=Option::where('key','header')->first();
            $info=json_decode($option->value ?? '');
            if (empty($option)) {
                $option=new Option;
                $option->key="header";
            }
            if(!empty($request->ytb_video)){
                $ytb_url=$this->parse_yotube_url($request->ytb_video);
            }
            else{
                $ytb_url=null;
            }
            
            $data['title']=$request->title;
            $data['highlight_title']=$request->highlight_title;
            $data['ytb_video']=$ytb_url;
            $data['description']=$request->description;
            if ($request->file) {
                if (!empty($info->preview)) {
                    if (file_exists($info->preview)) {
                        unlink($info->preview);
                    }
                }
                $fileName = time().'.'.$request->file->extension();  
                $request->file->move('uploads/1/'.date('Y/m/'), $fileName);
                $fileName='uploads/1/'.date('Y/m/').$fileName;
                
            }
            else{
                $fileName =$info->preview ?? null;
            }

            $data['preview']=$fileName;
            $option->value=json_encode($data);
            $option->save();

            return response()->json(['Update Success']);
        }

        if ($id=="counter_area") {
           

            $option=Option::where('key','counter_area')->first();
            if (empty($option)) {
                $option=new Option;
                $option->key='counter_area';
            }

            $data['happy_customer']=$request->happy_customer;
            $data['total_reviews']=$request->total_reviews;
            $data['total_domain']=$request->total_domain;
            $data['community_member']=$request->community_member;
            
            $option->value=json_encode($data);
            $option->save();

            return response()->json(['Update Success']);
        }




        if ($id=="ecom_features") {
             $validatedData = $request->validate([
                'top_image' => 'image|max:1000',
                'center_image' => 'image|max:1000',
                'bottom_image' => 'image|max:1000',
             ]);
             $option=Option::where('key',$id)->first();
             $info=json_decode($option->value ?? '');
             if (empty($option)) {
                $option=new Option;
                $option->key=$id;
             }

            if ($request->top_image) {
                if (!empty($info->top_image)) {
                    if (file_exists($info->top_image)) {
                        unlink($info->top_image);
                    }
                }
                $fileName = time().'.'.$request->top_image->extension();  
                $request->top_image->move('uploads/1/'.date('Y/m/'), $fileName);
                $top_image='uploads/1/'.date('Y/m/').$fileName;
                
            }
            else{
                $top_image =$info->top_image ?? null;
            }

            if ($request->center_image) {
                if (!empty($info->center_image)) {
                    if (file_exists($info->center_image)) {
                        unlink($info->center_image);
                    }
                }
                $fileName = time().'.'.$request->center_image->extension();  
                $request->center_image->move('uploads/1/'.date('Y/m/'), $fileName);
                $center_image='uploads/1/'.date('Y/m/').$fileName;
                
            }
            else{
                $center_image =$info->center_image ?? null;
            }

            if ($request->bottom_image) {
                if (!empty($info->bottom_image)) {
                    if (file_exists($info->bottom_image)) {
                        unlink($info->bottom_image);
                    }
                }
                $fileName = time().'.'.$request->bottom_image->extension();  
                $request->bottom_image->move('uploads/1/'.date('Y/m/'), $fileName);
                $bottom_image='uploads/1/'.date('Y/m/').$fileName;
                
            }
            else{
                $bottom_image =$info->bottom_image ?? null;
            }

            $data['top_image']=$top_image;
            $data['center_image']=$center_image;
            $data['bottom_image']=$bottom_image;
            $data['area_title']=$request->area_title;
            $data['description']=$request->description;
            $data['btn_link']=$request->btn_link;
            $data['btn_text']=$request->btn_text;
            $option->value=json_encode($data);
            $option->save();

            return response()->json('Option Updated Successfully...!!');



        }    
         if ($id=="about_1") {
            $validatedData = $request->validate([
                'file' => 'image|max:1000',
            ]);

            $option=Option::where('key',$id)->first();
            $info=json_decode($option->value ?? '');
            if (empty($option)) {
                $option=new Option;
                $option->key=$id;
            }
            $about_1['title']=$request->title ?? '';
            $about_1['description']=$request->description ?? '';
            $about_1['btn_link']=$request->btn_link ?? '';
            $about_1['btn_text']=$request->btn_text ?? '';
            $fileName =$request->icon_name ?? null;

            $about_1['preview']=$fileName;
            $option->value=json_encode($about_1);
            $option->save();

            return response()->json('Section Updated');
        }

        if ($id=="about_2") {

            $validatedData = $request->validate([
                'file' => 'image|max:1000',
            ]);

            $option=Option::where('key',$id)->first();
            $info=json_decode($option->value ?? '');
            if (empty($option)) {
                $option=new Option;
                $option->key=$id;
            }
            $about_2['title']=$request->title ?? '';
            $about_2['description']=$request->description ?? '';
            $about_2['btn_link']=$request->btn_link ?? '';
            $about_2['btn_text']=$request->btn_text ?? '';
            $fileName =$request->icon_name ?? null;

            $about_2['preview']=$fileName;
            $option->value=json_encode($about_2);
            $option->save();

            return response()->json('Section Updated');
        }

        if ($id=="about_3") {
            $validatedData = $request->validate([
                'file' => 'image|max:1000',
            ]);

            $option=Option::where('key',$id)->first();
            $info=json_decode($option->value ?? '');
            if (empty($option)) {
                $option=new Option;
                $option->key=$id;
            }
            $data['title']=$request->title ?? '';
            $data['description']=$request->description ?? '';
            $data['btn_link']=$request->btn_link ?? '';
            $data['btn_text']=$request->btn_text ?? '';
            $fileName =$request->icon_name ?? null;

            $data['preview']=$fileName;
            $option->value=json_encode($data);
            $option->save();

            return response()->json('Section Updated');
        }



        if ($id=="contact") {
            $validatedData = $request->validate([
                'file' => 'image|max:1000',
            ]);
            $fileName = 'contact.png';  
            $request->file->move('uploads/', $fileName);
            return response()->json(['Image Changed']);

         }   
    }

     public function parse_yotube_url($url) 
        {
        $pattern = '#^(?:https?://)?';    # Optional URL scheme. Either http or https.
        $pattern .= '(?:www\.)?';         #  Optional www subdomain.
        $pattern .= '(?:';                #  Group host alternatives:
        $pattern .=   'youtu\.be/';       #    Either youtu.be,
        $pattern .=   '|youtube\.com';    #    or youtube.com
        $pattern .=   '(?:';              #    Group path alternatives:
        $pattern .=     '/embed/';        #      Either /embed/,
        $pattern .=     '|/v/';           #      or /v/,
        $pattern .=     '|/watch\?v=';    #      or /watch?v=,    
        $pattern .=     '|/watch\?.+&v='; #      or /watch?other_param&v=
        $pattern .=   ')';                #    End path alternatives.
        $pattern .= ')';                  #  End host alternatives.
        $pattern .= '([\w-]{11})';        # 11 characters (Length of Youtube video ids).
        $pattern .= '(?:.+)?$#x';         # Optional other ending URL parameters.
        preg_match($pattern, $url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
