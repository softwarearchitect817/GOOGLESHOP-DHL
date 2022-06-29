<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Term;
use App\Stock;
use App\Attribute;
use App\Attributeprice;
use App\Meta;
use App\Postcategory;
use Auth;
use Str;
use Session;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Price;
use App\Models\Termoption;
use App\Models\Termoptionvalue;
use Carbon\Carbon;
use App\Useroption;
use App\Rules\Translation;
use DB;

class ProductController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$type=1)
    {
        $auth_id= seller_id();
        if ($request->src) {
            $posts=Term::where('type','product')->with('preview')->where('status',$type)->where('user_id',$auth_id)->where($request->type,'LIKE','%'.$request->src.'%')->latest()->paginate(30);
        }
        else{
            $posts=Term::where('type','product')->with('preview')->withCount('order')->where('status',$type)->where('user_id',$auth_id)->latest()->paginate(30);
        }
       
        $src=$request->src ?? '';
    
        $actives=Term::where('type','product')->where('status',1)->where('user_id',$auth_id)->count();
        $drafts=Term::where('type','product')->where('status',2)->where('user_id',$auth_id)->count();
        $incomplete=Term::where('type','product')->where('status',3)->where('user_id',$auth_id)->count();
        $trash=Term::where('type','product')->where('status',0)->where('user_id',$auth_id)->count();
        $clones=Term::where('type','product')->where('status',4)->where('user_id',$auth_id)->count();
        return view('seller.products.index',compact('posts','src','type','actives','drafts', 'clones','incomplete','trash','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $limit=user_limit();
        $posts_count=Term::where('user_id', seller_id())->count();
        if ($limit['product_limit'] <= $posts_count) {
            Session::flash('error', 'Maximum posts limit exceeded');
            return back();
        }

        /* Add multi-lang edit by nurs */
        $user_id = seller_id();
        $languages= Useroption::where('user_id',$user_id)->where('key','languages')->first();
        $langlist = json_decode($languages->value ?? '');
        
        $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
        return view('seller.products.create', compact('langlist', 'local'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            // 'title' => 'required|max:100',
            'title_translation' => [new Translation],
            'price' => 'required|max:50',
        ]);
        
        if ($request->affiliate) {
            $request->validate([
                'purchase_link' => 'required|max:100'
            ]);
        }
        
        $title_translations = (array)json_decode($request->title_translations);
        
        $user_id=seller_id();
                
        $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
        $slug=Str::slug($title_translations[$local]);

        if($request->special_price_start <= Carbon::now()->format('Y-m-d') && $request->special_price != null){
            if($request->special_price != null){
                if($request->price_type == 1){
                    $price=$request->price-$request->special_price;
                }
                else{
                    $percent= $request->price * $request->special_price / 100;
                    $price= $request->price-$percent;
                    $price=str_replace(',','',number_format($price,2));
                }
            }
            else{
                $price=$request->price;
            } 
        }
        else{
            $price=$request->price;
        }

        DB::beginTransaction();
        try {
            $term= new Term;
            $term->setTranslations('title', $title_translations);
            $term->slug=$slug;
            $term->status=3;
            $term->type='product';
            $term->user_id=$user_id;
            $term->save();

            $term_price=new Price;
            $term_price->term_id=$term->id;      
            $term_price->price=$price;      
            $term_price->regular_price=$request->price;      
            $term_price->special_price=$request->special_price ?? null;      
            $term_price->price_type=$request->price_type;      
            $term_price->starting_date=$request->special_price_start ?? null;      
            $term_price->ending_date=$request->special_price_end ?? null;      
            $term_price->save();

            $stock=new Stock;
            $stock->term_id = $term->id;
            $stock->stock_manage = $request->stock_manage ?? 0;
            $stock->stock_status = 1;
            $stock->stock_qty = $request->stock_qty ?? 999;
            $stock->sku = $request->sku ?? null;
            $stock->save();

            $dta['content']=null;
            $dta['excerpt']=null;

            $meta=new Meta;
            $meta->term_id = $term->id;  
            $meta->key = 'content';
            $meta->value = json_encode($dta); 
            $meta->save(); 

            if ($request->affiliate) {
                $meta=new Meta;
                $meta->term_id = $term->id;  
                $meta->key = 'affiliate';  
                $meta->value = $request->purchase_link; 
                $meta->save(); 
            }
            
            $meta=new Meta;
            $meta->term_id = $term->id;  
            $meta->key = 'content';
            $meta->value = json_encode($dta); 
            $meta->save(); 

            $meta= new Meta;
            $meta->term_id = $term->id;  
            $seo['meta_title']=$request->title;
            $seo['meta_description']='';
            $seo['meta_keyword']='';
            $meta->key = 'seo';
            $meta->value = json_encode($seo); 
            $meta->save();

            Session::flash("flash_notification", [
                "level"     => "success",
                "message"   => "Product Created Successfully"
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back();
        }
        return redirect()->route('seller.product.edit',$term->id);
    }

    public function store_group(Request $request,$id)
    {
        $request->validate([
            // 'name' => 'required|max:100',
            'name_translations' => [new Translation]
        ]);
  
        $term=Term::where('user_id', seller_id())->findorFail($id);

        $group= new Termoption;
        $group->user_id=seller_id();
        $group->term_id=$id;
        
        $name_translations = (array)json_decode($request->name_translations);
        $group->setTranslations('name', $name_translations);

        $group->type=1;
        $group->is_required=$request->is_required ?? 0;
        $group->select_type=$request->select_type ?? 0;
        $group->save();

        return response()->json('Option Created Successfully....!!!');
    }

    public function stock_update(Request $request,$id)
    {
        
  
        $term=Term::where('user_id', seller_id())->findorFail($id);

        $stock= Stock::where('term_id',$id)->first();
        $stock->stock_manage = $request->stock_manage ?? 0;
        $stock->stock_status = $request->stock_status ?? 0;
        $stock->stock_qty = $request->stock_qty ?? 999;
        $stock->sku = $request->sku ?? null;
        $stock->save();

        return response()->json('Stock Updated Successfully....!!!');
    }

    public function add_row(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);
  
        $term=Termoption::where('user_id',seller_id())->where('type',1)->findorFail($request->row_id);

        $group= new Termoption;
        $group->user_id=seller_id();
        $group->term_id=$term->term_id;
        $group->name=$request->name;
        $group->amount=$request->price ?? 0.00;
        $group->amount_type=$request->amount_type;
        $group->type=0;
        $group->p_id=$request->row_id;
        $group->save();

        return response()->json('Row Created Successfully....!!!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$type="edit")
    {
        /* Add multi-lang edit by nurs */
        $user_id = seller_id();
        $languages= Useroption::where('user_id',$user_id)->where('key','languages')->first();
        $langlist = json_decode($languages->value ?? '');
        
        $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
        if ($type=='edit') {
            $info=Term::with('content','post_categories','affiliate')->where('user_id',seller_id())->findorFail($id);

            $cats=[];

            foreach ($info->post_categories as $key => $value) {
               array_push($cats, $value->category_id);
            }

            $content_translations = $info->content->getTranslations("value");
            
            foreach($content_translations as $key=>$value) {
                $content_translations[$key] = json_decode($value, true);
            }
            
            $content_translations = json_encode($content_translations);
        
            return view('seller.products.edit.item',compact('info','cats','content_translations', 'langlist', 'local'));
        }
        if ($type=='varient') {
            $user_id=seller_id();
            $info=Term::with('attributes')->where('user_id',$user_id)->findorFail($id);
            $attribute=[];
            $variation=[];

            $variations = collect($info->attributes)->groupBy(function($q){
                return $q->category_id;
            });
            foreach ($variations as $key => $value) {
                array_push($variation,$key);
                foreach($value as $row){
                    array_push($attribute,$row->variation_id);
                }
               
            }
            

            $posts=\App\Category::where([
              ['user_id',$user_id],
              ['type','parent_attribute'],
            ])->whereHas('childrenCategories')->with('childrenCategories')->get();
            return view('seller.products.edit.variants',compact('info','posts','variations','attribute'));
        } 
        if ($type=='price') {
            $info=Term::with('price')->where('user_id',seller_id())->findorFail($id);

            return view('seller.products.edit.price',compact('info'));
        }
        if ($type=='image') {
            $info=Term::with('medias')->where('user_id',seller_id())->findorFail($id);

            return view('seller.products.edit.images',compact('info'));
        }
         if ($type=='files') {
            $info=Term::with('attributes','files')->where('user_id',seller_id())->findorFail($id);

            return view('seller.products.edit.files',compact('info'));
        }
         if ($type=='option') {
            $info=Term::where('user_id',seller_id())->with('options')->findorFail($id);

            return view('seller.products.edit.option',compact('info', 'langlist', 'local'));
        }

        if ($type=='seo') {
            $info=Term::with('seo')->where('user_id',seller_id())->findorFail($id);
            $json=json_decode($info->seo->value);
            return view('seller.products.edit.seo',compact('info','json'));
        }
        if ($type=='inventory') {
            $info=Term::with('stock')->where('user_id',seller_id())->findorFail($id);
            
            return view('seller.products.edit.stock',compact('info'));
        }
       
        
        if($type == 'express-checkout'){
          $user_id=seller_id();
            $info=Term::with('attributes','options')->where('user_id',$user_id)->findorFail($id);
            $variations = collect($info->attributes)->groupBy(function($q){
                return $q->attribute->name;
            });
            //return $request=Request()->all();
            return view('seller.products.edit.express',compact('info','variations'));
        }

        abort(404);

    }

    public function variation(Request $request,$id){
       
        $data=[];
        foreach ($request->child ?? [] as $key => $value) {
           
            foreach ($value as $r => $child) {
                $dat['category_id']=$key;
                $dat['variation_id']=$child;
                $dat['term_id']=$id;
                array_push($data,$dat);
            }
           
        }
        Term::where('user_id',seller_id())->findorFail($id);
        Attribute::where('term_id',$id)->delete();
        if(count($data) > 0){
            Attribute::insert($data);
        }
        
       return response()->json('Attributes Updated');
      
    }

    public function option_delete(Request $request)
    {
        
        Termoption::where('p_id',$request->id)->where('user_id',seller_id())->delete();
        Termoption::where('user_id',seller_id())->where('id',$request->id)->delete();

        return response()->json('Option Deleted Successfully....!!');
    }
    public function row_update(Request $request)
    {
        $request->validate([
            'edit_name_translations' => [new Translation],
            // 'name' => 'required|max:100',
        ]);
       
       $edit_name_translations = (array)json_decode($request->edit_name_translations);
       
        $option=Termoption::where('user_id',seller_id())->findorFail($request->id);

        $option->setTranslations('name', $edit_name_translations);
        
        $option->is_required=$request->is_required ?? 0;
        $option->select_type=$request->select_type ?? 0;
        $option->save();
        return response()->json('Option Updated Successfully....!!');
    }

    public function option_update(Request $request,$id)
    {
        
        $user_id=seller_id();
        foreach($request->options as $key => $option){
            foreach($option as $row){
                foreach($row as $k=> $item){
                    $data['name']=$item['label'];
                    $data['amount']=$item['price'];
                    $data['amount_type']=$item['price_type'];
                    Termoption::where('user_id',$user_id)->where('type',0)->where('p_id',$key)->where('id',$k)->update($data);
     
                }
              
            }
        }

        return response()->json(['Option Updated....!!!']);
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
        $request->validate([
            'title_translations' => [new Translation],
        ]);
        
        if ($request->affiliate) {
            $request->validate([
              'purchase_link' => 'required|max:100'
            ]);
        }
       
        DB::beginTransaction();
        try {
       $info=Term::where('user_id',seller_id())->with('affiliate')->findorFail($id);
        
        $title_translations = (array)json_decode($request->title_translations);
      
       $info->title=$request->title;
       $info->setTranslations('title', $title_translations);
       
       $user_id=seller_id();
       $local= Useroption::where('user_id',$user_id)->where('key','local')->first();
        $local=$local->value ?? ''; 
        
       $info->slug=Str::slug($title_translations[$local]);
       
       $info->featured=$request->featured;
       $info->status=$request->status ?? 2;
       $info->save();
       
       $content_translations = (array)json_decode($request->content_translations);
        foreach($content_translations as $key =>$value) {
            $content_translations[$key] = json_encode($value);
        }
        
       $meta=Meta::where('key','content')->where('term_id',$id)->first();
       if (empty($meta)) {
           $meta=new Meta;
           $meta->term_id=$id;
           $meta->key='content';
       }
       
    //   $dta['content']=$request->content;
    //   $dta['excerpt']=$request->excerpt;
       
        $meta->setTranslations('value', $content_translations);
    //   $meta->value=json_encode($dta);
       $meta->save();
       
       $catsArr=[];
       foreach ($request->cats ?? [] as $key => $value) {
        if (!empty($value)) {
             $data['category_id']=$value;
             $data['term_id']=$id;
           
           array_push($catsArr, $data);
        }
         
       }

       if (!empty($request->brand)) {
           $data['category_id']=$request->brand;
           $data['term_id']=$id;
          array_push($catsArr, $data);
       }
      
       Postcategory::where('term_id',$id)->delete();
       if (count($catsArr) > 0) {
          Postcategory::insert($catsArr);
       }

        if ($request->affiliate) {
            $meta=Meta::where('key','affiliate')->where('term_id',$id)->first();
            if (empty($meta)) {
               $meta=new Meta;
               $meta->term_id=$id;
               $meta->key='affiliate';
            }
            $meta->value = $request->purchase_link; 
            $meta->save(); 
        }
        else{
          if (!empty($info->affiliate)) {
           Meta::where('key','affiliate')->where('term_id',$id)->delete();
          }

        }
         DB::commit();
        
        } catch (Exception $e) {
            DB::rollback();

            return back();
        }
       
       return response()->json(['Item Updated']);

   }

   public function price(Request $request, $id){
       
    if($request->special_price_start <= Carbon::now()->format('Y-m-d') && $request->special_price != null){
       if($request->special_price != null){
        if($request->price_type == 1){
            $price=$request->price-$request->special_price;
        }
        else{
            $percent= $request->price * $request->special_price / 100;
            $price= $request->price-$percent;
            $price=str_replace(',','',number_format($price,2));
        }
       
      }
      else{
        $price=$request->price;
       } 
    }
    else{
        $price=$request->price;
    }
    

    $term_price=Price::with('term')->findorFail($id);
    if($term_price->term->user_id != seller_id()){
        die();
    }   
    $term_price->price=$price;      
    $term_price->regular_price=$request->price;      
    $term_price->special_price=$request->special_price ?? null;      
    $term_price->price_type=$request->price_type;      
    $term_price->starting_date=$request->special_price_start ?? null;      
    $term_price->ending_date=$request->special_price_end ?? null;      
    $term_price->save();
    return response()->json(['Price Updated....!!']);

   } 

   public function seo(Request $request, $id)
   {
       $info=Term::where('user_id',seller_id())->findorFail($id);
       
       $meta=Meta::where('key','seo')->where('term_id',$id)->first();
       if (empty($meta)) {
           $meta=new Meta;
           $meta->term_id=$id;
           $meta->key='seo';
       }
       $data['meta_title']=$request->meta_title;
       $data['meta_description']=$request->meta_description;
       $data['meta_keyword']=$request->meta_keyword;
      
       $meta->value=json_encode($data);
       $meta->save();

       return response()->json(['Seo Updated']);
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param   \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $auth_id=seller_id();
        if ($request->method=='delete') {
            if ($request->ids) {
                foreach ($request->ids as $id) {
                  
                  $term= Term::with('medias')->where('user_id',$auth_id)->find($id);
                  if (!empty($term)) {
                      foreach ($term->medias as $key => $row) {
                          mediaRemove($row->id);
                      }

                      Term::destroy($id);
                  }
                }
            }
            echo 'delete';
        }
        else{
         if ($request->ids) {
            foreach ($request->ids as $id) {
                
                $term= Term::where('user_id',$auth_id)->find($id);
                if (!empty($term)) {
                 if($request->method == 4){
                     $new_term = $term->replicate();
                     $new_term->created_at = now();
                     $new_term->save();
                 }
                 else{
                     $term->status=$request->method;
                     $term->save();
                 }
             }
          }
        

       }
     }
        return response()->json('Success');
    }

    public function import(Request $request)
    {
        $limit=user_limit();
        $posts_count=Term::where('user_id',seller_id())->count();
        if ($limit['product_limit'] <= $posts_count) {
         $error['errors']['error']='Maximum posts limit exceeded';
         return response()->json($error,401);
        }
        Excel::import(new ProductImport,  $request->file('file'));

        return response()->json(['Product Imported Successfully']);
    }
}