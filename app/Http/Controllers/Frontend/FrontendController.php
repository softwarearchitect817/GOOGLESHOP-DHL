<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Term;
use App\Category;
use App\Attribute;
use App\Getway;
use App\Models\Review;
use App\Models\Userplan;
use App\Models\AbandonedCart;
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
use App\Models\Article;
use App\Models\Currency;
use Auth;
use Cart;
use App\Models\ArticleComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{

    public $cats;
    public $attrs;

    public function index(Request $request)
    {    
         $url=$request->getHost();
         $url=str_replace('www.','',$url); 
        
        if (url('/') == env('APP_URL') || $url == 'localhost') {
        $seo=Option::where('key','seo')->first();
        $seo=json_decode($seo->value);

       JsonLdMulti::setTitle($seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/logo.png'));

       SEOMeta::setTitle($seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle($seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
       SEOTools::twitter()->setTitle($seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));

      
      $latest_gallery=Category::where('type','gallery')->with('preview')->where('is_admin',1)->latest()->take(15)->get();
      $features=Category::where('type','features')->with('preview','excerpt')->where('is_admin',1)->latest()->get(); 
      
      $testimonials=Category::where('type','testimonial')->with('excerpt')->where('is_admin',1)->latest()->get(); 

      $brands=Category::where('type','brand')->with('preview')->where('is_admin',1)->latest()->get(); 

      $plans=Plan::where('status',1)->get();
      
      $header=Option::where('key','header')->first();
      $header=json_decode($header->value ?? '');

      $about_1=Option::where('key','about_1')->first();
      $about_1=json_decode($about_1->value ?? '');
       
      $about_2=Option::where('key','about_2')->first();
      $about_2=json_decode($about_2->value ?? '');

      $about_3=Option::where('key','about_3')->first();
      $about_3=json_decode($about_3->value ?? '');

      $ecom_features=Option::where('key','ecom_features')->first();
      $ecom_features=json_decode($ecom_features->value ?? '');

      $counter_area=Option::where('key','counter_area')->first();
      $counter_area=json_decode($counter_area->value ?? '');

      return view('welcome',compact('latest_gallery','plans','features','header','about_1','about_3','about_2','testimonials','brands','ecom_features','counter_area'));
        }
      
        if($url==env('APP_PROTOCOLESS_URL')){
          return redirect('/check');
        }

    	 if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
       }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
       }
       JsonLdMulti::setTitle($seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

       SEOMeta::setTitle($seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle($seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
       SEOTools::twitter()->setTitle($seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png')); 
       $this->set_session_curr_rate();

        list($frontend, $theme) = explode(".", base_view());
    	return view($frontend.'.'.domain_info('user_id').'.'.$theme.'.index');
    }

    public function page()
    {
      $id=request()->route()->parameter('id');
      $info=Term::where('user_id',domain_info('user_id'))->where('type','page')->with('excerpt','content')->findorFail($id);
      JsonLdMulti::setTitle($info->title ?? env('APP_NAME'));
      JsonLdMulti::setDescription($info->excerpt->value ?? null);
      JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

      SEOMeta::setTitle($info->title ?? env('APP_NAME'));
      SEOMeta::setDescription($info->excerpt->value ?? null);
     
      SEOTools::setTitle($info->title ?? env('APP_NAME'));
      SEOTools::setDescription($info->excerpt->value ?? null);
      SEOTools::setCanonical(url('/'));
      SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
      SEOTools::twitter()->setTitle($info->title ?? env('APP_NAME'));
      SEOTools::twitter()->setSite($info->title ?? null);
      SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
        
    	return view(user_template_path().'.page', compact('info'));
    }

    public function sitemap(){
        if(!file_exists('uploads/'.domain_info('user_id').'/sitemap.xml')){
            abort(404);
        }
        return response(file_get_contents('uploads/'.domain_info('user_id').'/sitemap.xml'), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function shop(Request $request)
    {
       if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
       }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
       }
       if(!empty($seo)){
         JsonLdMulti::setTitle('Shop - '.$seo->title ?? env('APP_NAME'));
         JsonLdMulti::setDescription($seo->description ?? null);
         JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

         SEOMeta::setTitle('Shop - '.$seo->title ?? env('APP_NAME'));
         SEOMeta::setDescription($seo->description ?? null);
         SEOMeta::addKeyword($seo->tags ?? null);

         SEOTools::setTitle('Shop - '.$seo->title ?? env('APP_NAME'));
         SEOTools::setDescription($seo->description ?? null);
         SEOTools::setCanonical($seo->canonical ?? url('/'));
         SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
         SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
         SEOTools::twitter()->setTitle('Shop - '.$seo->title ?? env('APP_NAME'));
         SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
         SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }     


        $src=$request->src ?? null;
      
    	return view(user_template_path().'.shop', compact('src'));
    }

    public function cart(){
       \Cart::setGlobalTax(tax());
        if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
       }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
       }
       if(!empty($seo)){
        JsonLdMulti::setTitle('Cart - '.$seo->title ?? env('APP_NAME'));
        JsonLdMulti::setDescription($seo->description ?? null);
        JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

        SEOMeta::setTitle('Cart - '.$seo->title ?? env('APP_NAME'));
        SEOMeta::setDescription($seo->description ?? null);
        SEOMeta::addKeyword($seo->tags ?? null);

        SEOTools::setTitle('Cart - '.$seo->title ?? env('APP_NAME'));
        SEOTools::setDescription($seo->description ?? null);
        SEOTools::setCanonical($seo->canonical ?? url('/'));
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
        SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
        SEOTools::twitter()->setTitle('Cart - '.$seo->title ?? env('APP_NAME'));
        SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
        SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }
    	return view(user_template_path().'.cart');
    }

    public function wishlist(){
       if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
       }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
       }
       if(!empty($seo)){
        JsonLdMulti::setTitle('Wishlist - '.$seo->title ?? env('APP_NAME'));
        JsonLdMulti::setDescription($seo->description ?? null);
        JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

        SEOMeta::setTitle('Wishlist - '.$seo->title ?? env('APP_NAME'));
        SEOMeta::setDescription($seo->description ?? null);
        SEOMeta::addKeyword($seo->tags ?? null);

        SEOTools::setTitle('Wishlist - '.$seo->title ?? env('APP_NAME'));
        SEOTools::setDescription($seo->description ?? null);
        SEOTools::setCanonical($seo->canonical ?? url('/'));
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
        SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
        SEOTools::twitter()->setTitle('Wishlist - '.$seo->title ?? env('APP_NAME'));
        SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
        SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }
       
    	return view(user_template_path().'.wishlist');
    }

    public function thanks(){
       if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
       }
       else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
       }
        if(!empty($seo)){
       JsonLdMulti::setTitle('Thank you - '.$seo->title ?? env('APP_NAME'));
       JsonLdMulti::setDescription($seo->description ?? null);
       JsonLdMulti::addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));

       SEOMeta::setTitle('Thank you - '.$seo->title ?? env('APP_NAME'));
       SEOMeta::setDescription($seo->description ?? null);
       SEOMeta::addKeyword($seo->tags ?? null);

       SEOTools::setTitle('Thank you - '.$seo->title ?? env('APP_NAME'));
       SEOTools::setDescription($seo->description ?? null);
       SEOTools::setCanonical($seo->canonical ?? url('/'));
       SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
       SEOTools::opengraph()->addProperty('image', asset('uploads/'.domain_info('user_id').'/logo.png'));
       SEOTools::twitter()->setTitle('Thank you - '.$seo->title ?? env('APP_NAME'));
       SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
       SEOTools::jsonLd()->addImage(asset('uploads/'.domain_info('user_id').'/logo.png'));
       }
       
    	return view(user_template_path().'.thanks');
    }
    
    public function make_local(Request $request){
        
         Session::put('locale',$request->lang);
        \App::setlocale($request->lang);

        return redirect('/');
    }  

    public function checkout(){
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

      list($frontend, $theme) = explode(".", base_view());
      return view($frontend.'.'.domain_info('user_id').'.'.$theme.'.checkout', compact('locations','getways'));

    }

    public function wishlist_remove(){
      $id=request()->route()->parameter('id');
    } 

    public function detail($slug,$id)
    {   
        $this->set_session_curr_rate();
        $id=request()->route()->parameter('id');
        $user_id=domain_info('user_id');


        $info=Term::where('user_id',$user_id)->where('type','product')->where('status',1)->with('affiliate','medias','content','categories','brands','seo','price','options','stock')->findorFail($id);
        $next = Term::where('user_id',$user_id)->where('type','product')->where('status',1)->where('id', '>', $id)->first();
        $previous = Term::where('user_id',$user_id)->where('type','product')->where('status',1)->where('id', '<', $id)->first();

        $variations = collect($info->attributes)->groupBy(function($q){
            return $q->attribute->name;
        });

        $content=json_decode($info->content->value);
        $seo=json_decode($info->seo->value ?? '');

        SEOMeta::setTitle($seo->meta_title ?? $info->title);
        SEOMeta::setDescription($seo->meta_description ?? $content->excerpt ?? null);
        SEOMeta::addMeta('article:published_time', $info->updated_at->format('Y-m-d'), 'property');
        SEOMeta::addKeyword([$seo->meta_keyword ?? null ]);
        
        OpenGraph::setDescription($seo->meta_description ?? $content->excerpt ?? null);
        OpenGraph::setTitle($seo->meta_title ?? $info->title);
        OpenGraph::addProperty('type', 'product');
        
        foreach($info->medias as $row){
            OpenGraph::addImage(asset($row->url));
            JsonLdMulti::addImage(asset($row->url));
            JsonLd::addImage(asset($row->url));
        }  
        
        
        JsonLd::setTitle($seo->meta_title ?? $info->title);
        JsonLd::setDescription($seo->meta_description ?? $content->excerpt ?? null);
        JsonLd::setType('Product');
        
        JsonLdMulti::setTitle($seo->meta_title ?? $info->title);
        JsonLdMulti::setDescription($seo->meta_description ?? $content->excerpt ?? null);
        JsonLdMulti::setType('Product');

        $order_days= Useroption::where('user_id',$user_id)->where('key','estimated_order_days')->first();
        return view(user_template_path().'.details', compact('order_days','info','next','previous','variations','content'));
    }

    public function category($id)
    {
    	$id=request()->route()->parameter('id');
      $user_id=domain_info('user_id');
      $info=Category::where('user_id',$user_id)->where('type','category')->with('preview')->findorFail($id);
    
      
      if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
      }
      else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
      }

      JsonLdMulti::setTitle($info->name ?? env('APP_NAME'));
      JsonLdMulti::setDescription($seo->description ?? null);
      JsonLdMulti::addImage(asset($info->preview->content ?? 'uploads/'.domain_info('user_id').'/logo.png'));

      SEOMeta::setTitle($info->name ?? env('APP_NAME'));
      SEOMeta::setDescription($seo->description ?? null);
      SEOMeta::addKeyword($seo->tags ?? null);

      SEOTools::setTitle($info->name ?? env('APP_NAME'));
      SEOTools::setDescription($seo->description ?? null);
      SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
      SEOTools::opengraph()->addProperty('image', asset($info->preview->content ?? 'uploads/'.domain_info('user_id').'/logo.png'));
      SEOTools::twitter()->setTitle($info->name ?? env('APP_NAME'));
      SEOTools::twitter()->setSite($seo->twitterTitle ?? null);
      SEOTools::jsonLd()->addImage(asset($info->preview->content ?? 'uploads/'.domain_info('user_id').'/logo.png'));


        return view(user_template_path().'.shop',compact('info'));
    }

    public function home_page_products(Request $request)
    {
      if($request->latest_product){
        if($request->latest_product == 1){
          $data['get_latest_products']= $this->get_latest_products();
        }
        else{
          $data['get_latest_products']= $this->get_latest_products($request->latest_product);
        }
      }

      if($request->random_product){
        if ($request->random_product == 1) {
           $data['get_random_products']= $this->get_random_products();
        }
        else{
           $data['get_random_products']= $this->get_random_products($request->random_product);
        }
         
      }
      if($request->get_offerable_products){
        if ($request->get_offerable_products == 1) {
           $data['get_offerable_products']= $this->get_offerable_products();
        }
        else{
           $data['get_offerable_products']= $this->get_offerable_products($request->random_product);
        }
         
      }

      if($request->trending_products){
        if($request->trending_products == 1){
          $data['get_trending_products'] = $this->get_trending_products();
        }
        else{
          $data['get_trending_products'] = $this->get_trending_products($request->trending_products);
        }
           
      }

      if($request->best_selling_product){
        if($request->best_selling_product == 1){
         $data['get_best_selling_product']= $this->get_best_selling_product();
        }
        else{
          $data['get_best_selling_product']= $this->get_best_selling_product($request->best_selling_product);
        }
      }

      if($request->sliders){
        $data['sliders'] = $this->get_slider();
      }

      if($request->menu_category){
        $data['get_menu_category'] = $this->get_menu_category();
      }

      if($request->bump_adds){
        $data['bump_adds']=$this->get_bump_adds();
      }

      if($request->banner_adds){
        $data['banner_adds']=$this->get_banner_adds();
      } 

      if($request->featured_category){
        $data['featured_category']=$this->get_featured_category();
      }   

      if($request->featured_brand){
        $data['featured_brand']=$this->get_featured_brand();
      }

      if($request->category_with_product){
        $data['category_with_product']=$this->get_category_with_product();
      } 

      if($request->brand_with_product){
        $data['brand_with_product']=$this->get_brand_with_product();
      }   
      
      
      
      
      return response()->json($data);

    }

    public  function get_slider(){
        $user_id = domain_info('user_id');
        return Category::where('type','slider')->with('excerpt')->where('user_id',$user_id)->latest()->get()->map(function($q){
         $data['slider']=asset($q->name);
         $data['url']=$q->slug;
         $data['meta']=json_decode($q->excerpt->content ?? '');

        return $data;
       });
    }

    public function get_menu_category(){
        $user_id=domain_info('user_id');
        
        return $data=Category::where('type','category')->where('user_id',$user_id)->where('menu_status',1)->get()->map(function($q){
            $data['id']=$q->id;
            $data['name']= $q->name;
            $data['slug']=$q->slug;
            return $data;
        });
    }


    public function brand($id)
    {
      $id=request()->route()->parameter('id');
      $user_id=domain_info('user_id');
      $info=Category::where('user_id',$user_id)->where('type','brand')->with('preview')->findorFail($id);

      if(Cache::has(domain_info('user_id').'seo')){
        $seo=json_decode(Cache::get(domain_info('user_id').'seo'));
      }
      else{
        $data=Useroption::where('user_id',domain_info('user_id'))->where('key','seo')->first();
        $seo=json_decode($data->value ?? '');
      }

      JsonLdMulti::setTitle($info->name ?? env('APP_NAME'));
      JsonLdMulti::setDescription($seo->description ?? null);
      JsonLdMulti::addImage(asset($info->preview->content ?? 'uploads/'.domain_info('user_id').'/logo.png'));

      SEOMeta::setTitle($info->name ?? env('APP_NAME'));
      SEOMeta::setDescription($seo->description ?? null);
      SEOMeta::addKeyword($seo->tags ?? null);

      SEOTools::setTitle($info->name ?? env('APP_NAME'));
      SEOTools::setDescription($seo->description ?? null);
      SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? null);
      SEOTools::opengraph()->addProperty('image', asset($info->preview->content ?? 'uploads/'.domain_info('user_id').'/logo.png'));
      SEOTools::twitter()->setTitle($info->name ?? env('APP_NAME'));
      SEOTools::twitter()->setSite($info->name ?? null);
      SEOTools::jsonLd()->addImage(asset($info->preview->content ?? 'uploads/'.domain_info('user_id').'/logo.png'));


        return view(user_template_path().'.shop', compact('info'));
    }

    public function get_featured_attributes()
    {
      $user_id=domain_info('user_id');
      $posts=Category::where('user_id',$user_id)->where('type','parent_attribute')->where('featured',1)->with('featured_child_with_post_count_attribute')->get();

      return $posts;
    }

    public function get_ralated_product_with_latest_post(Request $request){
    	$user_id=domain_info('user_id');

    	$this->cats=$request->categories ?? [];
    	$avg=Review::where('term_id',$request->term)->avg('rating');
    	$ratting_count=Review::where('term_id',$request->term)->count();
    	$avg=(int)$avg;
    	$related=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->whereHas('post_categories',function($q){
            $q->whereIn('category_id',$this->cats);
        })->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews')->latest()->take(20)->get();

        foreach($related as $r) {
            if (!empty($r->category->category)) {
                $r->category->category->name = $r->category->category->name;
            }
        }
    	 $get_latest_products=  $this->get_latest_products();
    	 $related = $related->map(function($q){
    	     return $this->product_data($q);
    	 });
    	 $data['get_latest_products']=$get_latest_products;
    	 $data['get_related_products']=$related;
    	 $data['ratting_count']=$ratting_count;
    	 $data['ratting_avg']=$avg;
         
    	 return response()->json($data);
    }

    public function get_reviews($id){
    	$user_id=domain_info('user_id');
    	$id=request()->route()->parameter('id');
    	$reviews=Review::where('term_id',$id)->where('user_id',$user_id)->latest()->paginate(12);
    	$data=[];
    	foreach($reviews as $review){
    		$dta['rating']=$review->rating;
    		$dta['name']=$review->name;
    		$dta['comment']=$review->comment;
    		$dta['created_at']=$review->created_at->diffForHumans();
    		array_push($data,$dta);
    	}
    	$revi['data']=$data;
    	$revi['links']=$reviews;
    	
    	return response()->json($revi);
    }


    public function get_ralated_products(Request $request)
    {
      $user_id=domain_info('user_id');

      $this->cats=$request->cats;

      $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->whereHas('post_categories',function($q){
        $q->whereIn('category_id',$this->cats);
      })->with('preview','attributes','category','price','options','stock','affiliate')->latest()->paginate(30);

      return response()->json($posts);
    }

    public function product_search(Request $request)
    {
      $user_id=domain_info('user_id');
      $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->where('title','LIKE','%'.$request->src.'%')->with('preview','attributes','category','price','options','stock','affiliate')->latest()->paginate(30);
      return response()->json($posts);
    }

    public function get_featured_category()
    {
      $user_id=domain_info('user_id');
      $posts=Category::where('user_id',$user_id)->where('type','category')->with('preview')->where('featured',1)->latest()->get()->map(function($q){
        $data['id']=$q->id;
        $data['name']=$q->name;
        $data['slug']=$q->slug;
        $data['type']=$q->type;
        $data['preview']=asset($q->preview->content ?? 'uploads/default.png');
        return $data;
      });

      return $posts;
    }

    public function get_featured_brand()
    {
      $user_id=domain_info('user_id');
      $posts=Category::where('user_id',$user_id)->where('type','brand')->with('preview')->where('featured',1)->latest()->get()->map(function($q){
        $data['id']=$q->id;
        $data['name']=$q->name;
        $data['slug']=$q->slug;
        $data['type']=$q->type;
        $data['preview']=asset($q->preview->content ?? 'uploads/default.png');
        return $data;
      });
      return $posts;
    }

    public function get_category()
    {
        $user_id=domain_info('user_id');
        return $posts=Category::where('user_id',$user_id)->where('type','category')->withCount('posts')->latest()->get()->map(function($q){
            $data = [];
            $data['id'] = $q->id;
            $data['name']=$q->name;
            $data['posts_count'] = $q->posts_count;
            return $data;
        });

      
    }

    public function get_brand()
    {
      $user_id=domain_info('user_id');
      return $posts=Category::where('user_id',$user_id)->where('type','brand')->withCount('posts')->latest()->get();

      
    }

    public function get_products(Request $request)
    {
        $user_id=domain_info('user_id');
        $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews')->latest()->paginate(30);
        foreach($posts as $p) {
           if (!empty($p->category->category)) {
                $p->category->category->name = $p->category->category->name;
            }
        }
        return response()->json($posts);
    }
    
    public function get_offerable_products($limit=20)
    {
      $user_id=domain_info('user_id');
      $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->with('preview','attributes','category','price','options','stock','affiliate')->whereHas('price',function($q){
        return $q->where('ending_date','>=',date('Y-m-d'))->where('starting_date','<=',date('Y-m-d'));
      })->withCount('reviews')->inRandomOrder()->take(20)->get();
       foreach($posts as $p) {
           if (!empty($p->category->category)) {
                $p->category->category->name = $p->category->category->name;
            }
       }
       return $posts;
    }


    public function get_latest_products($limit=20)
    {   
        $this->set_session_curr_rate();
        $user_id=domain_info('user_id');
        // $currencies=Useroption::where('key','currency')->where('user_id',$user_id)->first();
        // $to_currency = json_decode($currencies->value ?? '')->currency_default->currency_name;
        // if(!Session::has('to_currency') || !Session::has('rate_base')){
        //   Session::put('to_currency', $to_currency);
        //   if($to_currency == "USD"){
        //     $rate = Currency::where('currency_id', $to_currency)->first()->usd;
        //     Session::put('rate_base', $rate);
        //   }else{
        //     $rate = Currency::where('currency_id', $to_currency)->first()->eur;
        //     Session::put('rate_base', $rate);
        //   }
        // }
        $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews')->latest()->take($limit)->get()->map(function($q) {
            return $this->product_data($q);        
        });

        return $posts;
    } 

    public function max_price(){
      $user_id=domain_info('user_id');
     return Attribute::where('user_id',$user_id)->max('price');
     
    }

    public function min_price(){
      $user_id=domain_info('user_id');
     return Attribute::where('user_id',$user_id)->min('price');
     
    }

    public function get_bump_adds(){
      $user_id=domain_info('user_id');
      return Category::where('user_id',$user_id)->where('type','offer_ads')->latest()->get()->map(function($q){
        $data['image']=asset($q->name);
        $data['url']=$q->slug;
        return $data;
      });
     
    }
    public function get_banner_adds(){
      $user_id=domain_info('user_id');
      return Category::where('user_id',$user_id)->where('type','banner_ads')->get()->map(function($q){
        $data['image']=asset($q->name);
        $data['url']=$q->slug;
        return $data;
      });
    }


    public function get_shop_attributes(){
      $data['categories']=$this->get_category();
      $data['brands']=$this->get_brand();
      $data['attributes']=$this->get_featured_attributes();
      return $data;
    }


    public function get_shop_products(Request $request)
    {
     
        if($request->order=='DESC' || $request->order=='ASC'){
          $order=$request->order;
        }
        else{
          $order='DESC';
        }
        if($request->order=='bast_sell'){
          $featured=2;
        }
        elseif($request->order=='trending'){
          $featured=1;
        }
        else{
          $featured=0;
        }

       $user_id=domain_info('user_id');
       $this->attrs = $request->attrs ?? [];
       $this->cats=$request->categories ?? [];

       $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews');

       if(!empty($request->term)){
        $data= $posts->where('title','LIKE','%'.$request->term.'%');
       }

       if(count($this->attrs) > 0){
        $data= $posts->whereHas('attributes_relation',function($q){
             return $q->whereIn('variation_id',$this->attrs);
           });
       }

       if(!empty($request->min_price)){
         $min_price=$request->min_price;
        $data=$posts->whereHas('price',function($q) use ($min_price){
          return $q->where('price','>=',$min_price);
        }); 

       }

       if(!empty($request->max_price)){
        $max_price=$request->max_price;
        $data=$posts->whereHas('price',function($q) use ($max_price){
         return $q->where('price','<=',$max_price);
       }); 
      }

       if(count($this->cats) > 0){
        $data= $posts->whereHas('post_categories',function($q){
             return $q->whereIn('category_id',$this->cats);
           });
       }

       if($featured != 0){
        $data= $posts->orderBy('featured','DESC');
       }
       else{
        $data= $posts->orderBy('id',$order);
       }

       $data= $data ?? $posts;
       $data=$data->paginate($request->limit ?? 18);
       
       $result = $data->map(function($q) {
           return $this->product_data($q);
       });
       
       $data=$data->toArray();
       
       $data['data'] = $result;

       return response()->json($data);
    }

    public function get_random_products($limit=20)
    {  
       $rate_base = Session::get('rate_base');
       $limit=request()->route()->parameter('limit') ?? 20;
       $user_id=domain_info('user_id');
       $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews')->inRandomOrder()->take($limit)->get();
      
      for($i=0; $i<count($posts); $i++){
        $posts[$i]["price"]->price *= $rate_base;
        $posts[$i]["price"]->regular_price *= $rate_base;
        $posts[$i]["price"]->special_price *= $rate_base;
      }
       return $posts;
    }

    public function get_trending_products($limit=20)
    {
       $user_id=domain_info('user_id');
       $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->where('featured',1)->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews')->latest()->take($limit)->get();
       
       return $posts;
    }

    public function get_best_selling_product($limit=20)
    {
       $user_id=domain_info('user_id');
       $posts=Term::where('user_id',$user_id)->where('status',1)->where('type','product')->where('featured',2)->with('preview','attributes','category','price','options','stock','affiliate')->withCount('reviews')->latest()->take($limit)->get();
       return $posts;
    }

    public function get_category_with_product($limit=10)
    {
      $limit=request()->route()->parameter('limit');
      $user_id=domain_info('user_id');
      $posts=Category::where('user_id',$user_id)->where('type','category')->with('take_20_product')->take($limit)->get();

      return $posts;
    }

    public function get_brand_with_product($limit=10)
    {

      $limit=request()->route()->parameter('limit');

      $user_id=domain_info('user_id');
      $posts=Category::where('user_id',$user_id)->where('type','brand')->with('take_20_product')->take($limit)->get();

      return $posts;
    }
          
    public function all_blogs()
    {
        $domain  = $_SERVER['HTTP_HOST'];
        $user = DB::table('domains')->where('domain', $domain)->first();
        $userplan_id = $user->userplan_id;
        $user_id = $user->user_id;
        if ($userplan_id) {
          $check = Userplan::where('id', $userplan_id)->pluck('user_id')->first();
          if ($check) {
            $articles_per_page = DB::table('blog_settings')->where('user_id', $check)->pluck('articles_per_page')->first();
            $articles = Article::with('comments')->where('user_id', $user_id)->paginate($articles_per_page);
            return view('frontend.'.$user_id.'.bigbag.blog.all_blogs', compact('articles','user'));
          } else {
            abort(404);
          }
        } else {
          abort(404);
        }
      }
      
      public function view_blogs($slug)
      {
            $domain = $_SERVER['HTTP_HOST'];
            $user = DB::table('domains')->where('domain', $domain)->first();
            $userplan_id = $user->userplan_id;
            $user_id = $user->user_id;
            if ($userplan_id) {
                  $check = Userplan::where('id', $userplan_id)->pluck('user_id')->first();
                  if ($check) {
                        $slug = request()->segment(2);
                        $article = Article::with(['category', 'subcategory'])->where('slug', $slug)->first();
                        $article->views = $article->views + 1;
                        $article->save();
                        $blog_setting = DB::table('blog_settings')->where('user_id', $check)->first();
                        $articles = Article::get()->take(5);
                        return view('frontend.'.$user_id.'.bigbag.blog.single_blog', compact('article', 'articles', 'blog_setting'));
                  } else {
                        abort(404);
                  }
            } else {
                  abort(404);
            }
      }
      public function Article_comment(Request $request)
      {
            $request->validate([
              'name' => 'required|max:255',
              'email' => 'required|email',
              'comment' => 'required',
            ]);

            $comment = new ArticleComment;
            $comment->name = $request->name;
            $comment->email = $request->email;
            $comment->comment = $request->comment;
            $comment->article_id = $request->article_id;
            $comment->save();
            return response()->json(['Article Comment Created Successfully']);
      }
      
      public function product_data($q) {
        $rate_base = Session::get('rate_base');
        $data = [];
            $data['id'] = $q->id;
            $data['title'] = $q->title;
            $data['slug'] = $q->slug;
            $data['preview'] = $q->preview;
            $q->price->price = number_format($q->price->price * $rate_base, 2, '.','');
            $q->price->regular_price = number_format($q->price->regular_price * $rate_base, 2, '.','');
            $q->price->special_price = number_format($q->price->special_price * $rate_base, 2, '.','');
            $data['price'] = $q->price;
            $data['category']['category']['name'] = $q->category->category ? $q->category->category->name : "";

            $data['attributes'] = $q->attributes;
            $data['options'] = $q->options;
            $data['affiliate'] = $q->affiliate;
            $data['stock'] = $q->stock;
            $data['featured'] = $q->featured;
            $data['reviews_count'] = $q->reviews_count;
            return $data;
      }
      
      public function set_session_curr_rate() {
        $user_id=domain_info('user_id');
        $currencies=Useroption::where('key','currency')->where('user_id',$user_id)->first();
        $to_currency = '';
        if($currencies){
            $to_currency = json_decode($currencies->value ?? '')->currency_default->currency_name;
        }else {
            $to_currency = env("DEFAULT_CURRENCY_NAME");
        }
        if(!Session::has('to_currency') || !Session::has('rate_base')){
          Session::put('to_currency', $to_currency);
          if($to_currency == env("DEFAULT_CURRENCY_NAME")){
            $rate = Currency::where('currency_id', $to_currency)->first()->usd;
            Session::put('rate_base', $rate);
          }else{
            $rate = Currency::where('currency_id', $to_currency)->first()->eur;
            Session::put('rate_base', $rate);
          }
        }

      }
  
    public function set_to_currency(Request $request){
      $to = $request->to;
      $default = $request->default;
      $rate = Currency::where('currency_id', $to)->first();
      if($default == env("DEFAULT_CURRENCY_NAME")) {
        $rate = $rate->usd;
      }else {
        $rate = $rate->eur;
      }
      Session::put("to_currency", $to);
      Session::put("rate_base", $rate);
      
      return redirect('/');
    }
    
    public function abandoned_cart($id){
        $id=request()->route()->parameter('id');
        $id = Crypt::decryptString($id);
        $cart = AbandonedCart::find($id);
        $contents = json_decode($cart->content ?? '');
        Cart::destroy();
        foreach($contents as $content){
            Session::put('to_currency',$content->currency);
            Session::put('rate_base',$content->rate);
            Cart::add(
            $content->id, 
            $content->name, 
            $content->qty,
            $content->price,
            $content->weight, 
            [
                'attribute' => $content->options->attribute,
                'options' => $content->options->options,
                'preview' => $content->options->preview,
            ]);
        }
        AbandonedCart::where('id', $id)->update(['linked' => 1]);
        return view(user_template_path().'.abandonedcart', compact('contents'));
    }
    
    public function all_get_data(){
        //Get all the table names
        foreach(\DB::select('SHOW TABLES') as $table) {
            $all_table_names = get_object_vars($table);
        }
        return 'Success';
    }
}