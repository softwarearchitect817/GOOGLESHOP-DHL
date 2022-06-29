@extends('layouts.app')
@push('style')
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Edit Product'])
@endsection
@section('content')

<div class="row">
	<div class="col-lg-12">      
		<form method="post" action="{{ route('seller.product.update',$info->id) }}" id="productform">
			@csrf
			@method('PUT')
			<div class="card">
				<div class="card-body">
					@if (session()->has('flash_notification.message'))
					<div class="alert alert-{{ session()->get('flash_notification.level') }}">
						<button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">×</button>
						{!! session()->get('flash_notification.message') !!}
					</div>
					@endif 
					<div class="row">
						<div class="col-sm-3">
							<ul class="nav nav-pills flex-column">
								<li class="nav-item">
									<a class="nav-link active" href="{{ route('seller.product.edit',$info->id) }}"><i class="fas fa-cogs"></i> {{ __('Item') }}</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/price') }}"><i class="fas fa-money-bill-alt"></i> {{ __('Price') }}</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " href="{{ url('seller/product/'.$info->id.'/option') }}"><i class="fas fa-tags"></i> {{ __('Options') }}</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/varient') }}"><i class="fas fa-expand-arrows-alt"></i> {{ __('Variants') }}</a>
								</li>
								
								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/image') }}"><i class="far fa-images"></i> {{ __('Images') }}</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/inventory') }}"><i class="fa fa-cubes"></i> {{ __('Inventory') }}</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/files') }}"><i class="fas fa-file"></i> {{ __('Files') }}</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/seo') }}"><i class="fas fa-chart-line"></i> {{ __('SEO') }}</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/express-checkout') }}"><i class="fas fa-cart-arrow-down"></i> {{ __('Express checkout') }}</a>
								</li>
							</ul>
						</div>
						<div class="col-sm-9">
							<div class="form-group">
							    <label>{{ __('Product Name') }}</label>
							    <div class="row">
    							    <div class="col-md-9">
    							        <input type="text" class="form-control" id="title">
    							    </div>
    							    <div class="col-md-3">
    							        <select class="form-control" id="title_lang_select">
                                        @foreach($langlist ?? [] as $key => $row)
                                            <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                                        @endforeach
                                        </select>
							            <input type="hidden" id="title_translations" name="title_translations"/>
    							    </div>
							    </div>
							</div>
							<div class="form-group">
								<label>{{ __('Slug') }}</label>
								<input type="text" name="slug" class="form-control" required="" value="{{ $info->slug }}">
							</div>

							<div class="form-group">
								<label>{{ __('Short Description') }}</label>
								<div class="row">
    							    <div class="col-md-9">
    							        <textarea class="form-control" id="excerpt"></textarea>
    							    </div>
    							    <div class="col-md-3">
    							        <select class="form-control" id="excerpt_lang_select">
                                        @foreach($langlist ?? [] as $key => $row)
                                            <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                                        @endforeach
                                        </select>
							            <input type="hidden" id="content_translations" name="content_translations"/>
    							     </div>
    							 </div>
							</div>
							<div class="form-group">
							    <label>{{ __('Product Content') }}</label>
							    <select class="form-control" id="content_lang_select">
                                        @foreach($langlist ?? [] as $key => $row)
                                            <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                                        @endforeach
                                </select>
							</div>
							{{ editor(array('name'=>'content','class'=>'content')) }}

							<div class="form-group">
								<label>{{ __('Brand') }}</label>
								<select  class="form-control" name="brand">
									<option value="">None</option>
									{{ ConfigCategoryMulti('brand',$cats) }}
								</select>
							</div>

							<div class="form-group">
								<label>{{ __('Category') }}</label>
								<select multiple class="form-control select2" name="cats[]">
									<option value="">None</option>
									{{ ConfigCategoryMulti('category',$cats) }}
								</select>
							</div>
							<div class="form-group">
								<label>{{ __('Featured') }}</label>
								<select class="form-control" name="featured">
									<option value="0" @if($info->featured==0) selected="" @endif>{{ __('None') }}</option>
									<option value="1" @if($info->featured==1) selected="" @endif>{{ __('Trending products') }}</option>
									<option value="2" @if($info->featured==2) selected="" @endif>{{ __('Best selling products') }}</option>
									
								</select>
							</div>
							
							<div class="form-group">

								<label>
									<input type="checkbox" @if(!empty($info->affiliate)) checked @endif name="affiliate" id="affiliate"  class="custom-switch-input sm" value="1">
									<span class="custom-switch-indicator"></span>
									{{ __('External Product') }}
								</label>

							</div>
							<div class="form-group order_link  @if(empty($info->affiliate)) none @endif" >
								<label>{{ __('Order Link') }}</label>
								<input type="text"  class="form-control" id="purchase_link" value="{{ $info->affiliate->value ?? '' }}"  name="purchase_link" >
							</div>
							


							<div class="form-group">

								<label>
									<input type="checkbox" name="status" @if($info->status==1) checked="" @endif class="custom-switch-input sm" value="1">
									<span class="custom-switch-indicator"></span>
									{{ __('Published') }}
								</label>

							</div>
							<div class="form-group">
								<button class="btn btn-primary basicbtn" type="submit">{{ __('Save Changes') }}</button>
							</div>		
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>
</form>

@endsection
@push('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/form.js?v=1.0') }}"></script>
<script>
	$('#affiliate').on('change',function(){
		if(this.checked) {
          $('.order_link').show();
        }
        else{
        	$('.order_link').hide();
        }
	});
	
	var local='{{ $local }}';
    
    var title_translations = '{{ json_encode($info->getTranslations("title")) ?? "{}" }}';
    title_translations = title_translations.replaceAll('&quot;', '"');


    $('#title_translations').val(title_translations);

    title_translations = JSON.parse(title_translations);

    $('#title').val(title_translations[local] || "");

    $('#title').on('change', function(e) {
        title_translations[$('#title_lang_select').val()] = $(this).val();
        $('#title_translations').val(JSON.stringify(title_translations));
    });

    $('#title_lang_select').on('change', function(e) {
        var title = title_translations[$('#title_lang_select').val()] || "";
        $('#title').val(title);
    });
    
    var content_translations = '{{ $content_translations }}';
    content_translations = content_translations.replace(/&quot;/g,'"');
    content_translations = content_translations.replace(/&lt;/g, '<');
    content_translations = content_translations.replace(/&gt;/g, '>');
    content_translations = content_translations.replace(/\n/g, '\\n');

    $('#content_translations').val(content_translations);
    content_translations = JSON.parse(content_translations);
    console.log(content_translations);
    
    if (content_translations[local]) {
        $('#excerpt').val(content_translations[local]['excerpt'] || "");
        CKEDITOR.instances.content.setData(content_translations[local]['content'] || "");
    }

    $('#excerpt').on('change', function(e) {
        if (!content_translations[$('#excerpt_lang_select').val()]) content_translations[$('#excerpt_lang_select').val()] = {};
        content_translations[$('#excerpt_lang_select').val()]['excerpt'] = $(this).val();
        $('#content_translations').val(JSON.stringify(content_translations));
    });
    
    CKEDITOR.instances.content.on('change', function() { 
        if (!content_translations[$('#content_lang_select').val()]) content_translations[$('#content_lang_select').val()] = {};
        content_translations[$('#content_lang_select').val()]['content'] =  CKEDITOR.instances.content.getData();
        $('#content_translations').val(JSON.stringify(content_translations));
    });

    $('#excerpt_lang_select').on('change', function(e) {
        var excerpt = content_translations[$('#excerpt_lang_select').val()] ? (content_translations[$('#excerpt_lang_select').val()]['excerpt'] || "") : "";
        $('#excerpt').val(excerpt);
    });
    
    $('#content_lang_select').on('change', function(e) {
        var content = content_translations[$('#content_lang_select').val()] ? (content_translations[$('#content_lang_select').val()]['content'] || "") : "";
        CKEDITOR.instances.content.setData(content);
    });
</script>
@endpush