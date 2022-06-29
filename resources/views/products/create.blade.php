@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Create Product'])
@endsection
@section('content')

<div class="row">
	<div class="col-lg-9">      
		<form method="post" action="{{ route('seller.product.store') }}" id="product_create">
			@csrf

			<div class="card">
				<div class="card-body">
					@if ($errors->any())
    					<div class="alert alert-danger">
     					   <ul>
      				      @foreach ($errors->all() as $error)
               					 <li>{{ $error }}</li>
            			  @endforeach
        				  </ul>
   						</div>
					@endif
					<div class="row">
					    <div class="col-md-3">
					        <label for="price" class="mt-2">{{ __('Product Title') }}</label>
					    </div>
					    <div class="form-group col-md-7 col-12">
        					<input type="text" class="form-control" id="title">
        				</div>
    					<div class="form-group col-md-2 col-12">
    					    <select class="form-control" id="lang_select">
                                @foreach($langlist ?? [] as $key => $row)
                                    <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                                @endforeach
                            </select>
    					</div>
                        <input type="hidden" id="title_translations" name="title_translations" value=""/>
                    </div>
					<div class="row">
						<div class="col-md-3">
							<label for="price" class="mt-2">{{ __('Price') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">

							<input type="number" step="any" class="form-control" id="price" placeholder="Enter Price"  name="price" required="">

						</div>
					</div>
					
					<div class="row">
						<div class="col-md-3">
							<label for="special_price" class="mt-2">{{ __('Special Price') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">
							<input type="number" step="any" class="form-control" id="special_price" placeholder=""  name="special_price" >
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-3">
							<label for="special_price_type" class="mt-2">{{ __('Special Price Type') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">
							<select name="price_type" id="special_price_type" class="form-control selectric">
								<option value="1">Fixed</option>
								<option value="0">Percent</option>
							</select>
						</div>
					</div>
					<div class="row">
								<div class="col-md-3">
									<label for="special_price_start" class="mt-2">{{ __('Special Price Start') }}</label>
								</div>
								<div class="form-group col-md-9 col-12">
									<input type="date" class="form-control" id="special_price_start" placeholder=""  name="special_price_start" >
								</div>	
					</div>
					
					<div class="row">
						<div class="col-md-3">
							<label for="special_price_end" class="mt-2">{{ __('Special Price End') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">
							<input type="date" class="form-control" id="special_price_end" placeholder=""  name="special_price_end" >
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<label for="sku" class="mt-2">{{ __('SKU') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">

							<input type="text"  class="form-control" id="sku" placeholder="#ABC-123"  name="sku" >

						</div>
					</div>

					
					
					<div class="row">
						<div class="col-md-3">
							<label for="sku" class="mt-2">{{ __('Manage Stock') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">

							<label>
								<input type="checkbox" name="stock_manage" class="custom-switch-input sm" value="1">
								<span class="custom-switch-indicator"></span>
							</label>

						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<label for="qty" class="mt-2">{{ __('Stock Quantity') }}</label>
						</div>
						<div class="form-group col-md-9 col-12">

							<input type="number"  class="form-control" id="qty" placeholder="Enter Quantity"  name="stock_qty" >

						</div>
					</div>
				
					

				</div>
			</div>

		</div>
		<div class="col-lg-3">
			<div class="single-area">
				<div class="card">
					<div class="card-body">

						<div class="btn-publish">
							<button type="submit" class="btn btn-primary col-12" id="submit_btn"><i class="fa fa-save"></i> {{ __('Save') }}</button>
						</div>
					</div>
				</div>
			</div>
		</div>    
	</div>
</form>

@endsection
@push('js')
<script src="{{ asset('assets/seller/product/index.js') }}"></script>
<script>
var title_translations = {};
var local='{{ $local }}';

$('#title').on('change', function(e) {
    title_translations[$('#lang_select').val()] = $(this).val();
    $('#title_translations').val(JSON.stringify(title_translations));
});

$('#lang_select').on('change', function(e) {
    var title = title_translations[$('#lang_select').val()] || "";
    $('#title').val(title);
});
</script>
@endpush