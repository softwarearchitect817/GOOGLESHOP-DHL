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
                                <a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/inventory') }}"><i class="fa fa-cubes"></i> {{ __('Inventory') }}</a>
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
						<form class="basicform" method="post" action="{{ route('seller.products.stock_update',$info->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="sku">{{ __('SKU') }}</label>
                            <input type="text" name="sku"  value="{{ $info->stock->sku }}" class="form-control">
                        </div>
						
                        <div class="form-group">
                            <label for="stock_manage">{{ __('Manage Stock') }}</label>
                           <select name="stock_manage" id="stock_manage" class="form-control">
                               <option value="1" @if($info->stock->stock_manage == 1) selected @endif>{{ __('Manage Stock') }}</option>
                               <option value="0" @if($info->stock->stock_manage == 0) selected @endif>{{ __('Dont Need To Manage Stock') }}</option>
                           </select>
                        </div>
						<div class="stock_area @if($info->stock->stock_manage == 0) none @endif">
                        <div class="form-group">
                            <label for="stock_status">{{ __('Stock Status') }}</label>
                           <select name="stock_status" id="stock_status" class="form-control">
                               <option value="1" @if($info->stock->stock_status == 1) selected @endif>{{ __('In Stock') }}</option>
                               <option value="0" @if($info->stock->stock_status == 0) selected @endif>{{ __('Out Of Stock') }}</option>
                           </select>
                        </div>
						
                        
                        <div class="form-group">
                            <label for="stock_qty">{{ __('Stock Quantity') }}</label>
                            <input type="text" name="stock_qty"  value="{{ $info->stock->stock_qty }}" class="form-control">
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
<script type="text/javascript" src="{{ asset('assets/js/stock.js') }}"></script>

@endpush