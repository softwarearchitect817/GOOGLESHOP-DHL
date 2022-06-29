@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Product Price'])
@endsection
@section('content')

<div class="row">
	<div class="col-lg-12">      
		
		<div class="card">
			<div class="card-body">

				<div class="row">
					<div class="col-sm-3">
						<ul class="nav nav-pills flex-column">
							<li class="nav-item">
								<a class="nav-link" href="{{ route('seller.product.edit',$info->id) }}"><i class="fas fa-cogs"></i> {{ __('Item') }}</a>
                            </li>
                            
							<li class="nav-item">
								<a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/price') }}"><i class="fas fa-money-bill-alt"></i> {{ __('Price') }}</a>
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
						<form class="basicform" method="post" action="{{ route('seller.products.price',$info->price->id) }}">
                        @csrf
                        @method('PUT')
						<div class="row">
                            <div class="col-md-3">
                                <label for="price" class="mt-2">{{ __('Current Price') }}</label>
                            </div>
                            <div class="form-group col-md-9 col-12">
    
                                <input type="number" disabled value="{{ $info->price->price }}" step="any" class="form-control" id="price" placeholder="Enter Price"  required="">
    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="price" class="mt-2">{{ __('Regular Price') }}</label>
                            </div>
                            <div class="form-group col-md-9 col-12">
    
                                <input  type="number" value="{{ $info->price->regular_price }}" step="any" class="form-control" id="price" placeholder="Enter Price"  name="price" required="">
    
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label for="special_price" class="mt-2">{{ __('Special Price') }}</label>
                            </div>
                            <div class="form-group col-md-9 col-12">
                                <input type="number" value="{{ $info->price->special_price }}" step="any" class="form-control" id="special_price" placeholder=""  name="special_price" >
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label for="special_price_type" class="mt-2">{{ __('Special Price Type') }}</label>
                            </div>
                            <div class="form-group col-md-9 col-12">

                                <select name="price_type" id="special_price_type" class="form-control selectric">
                                    <option value="1" @if($info->price->price_type === 1) selected @endif>{{ __('Fixed') }}</option>
                                    <option value="0" @if($info->price->price_type === 0) selected @endif>{{ __('Percent') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                                    <div class="col-md-3">
                                        <label for="special_price_start" class="mt-2">{{ __('Special Price Start') }}</label>
                                    </div>
                                    <div class="form-group col-md-9 col-12">
                                        <input type="date" class="form-control" value="{{ $info->price->starting_date }}" id="special_price_start" placeholder=""  name="special_price_start" >
                                    </div>	
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label for="special_price_end" class="mt-2">{{ __('Special Price End') }}</label>
                            </div>
                            <div class="form-group col-md-9 col-12">
                                <input type="date" class="form-control" id="special_price_end" value="{{ $info->price->ending_date }}" placeholder=""  name="special_price_end" >
                            </div>
                        </div>			
						

						<button type="submit" class="btn btn-primary basicbtn">{{ __('Save Changes') }}</button>	
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endsection
@push('js')

<script type="text/javascript" src="{{ asset('assets/js/form.js') }}"></script>

@endpush