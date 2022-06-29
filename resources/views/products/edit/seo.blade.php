@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'SEO'])
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
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/inventory') }}"><i class="fa fa-cubes"></i> {{ __('Inventory') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/files') }}"><i class="fas fa-file"></i> {{ __('Files') }}</a>
							</li>

							<li class="nav-item">
								<a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/seo') }}"><i class="fas fa-chart-line"></i> {{ __('SEO') }}</a>
							</li>
							<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/express-checkout') }}"><i class="fas fa-cart-arrow-down"></i> {{ __('Express checkout') }}</a>
								</li>
						</ul>
					</div>
					<div class="col-sm-9">
						<form class="basicform" method="post" action="{{ route('seller.products.seo',$info->id) }}">
						@csrf
						<div class="form-group">
							<label>{{ __('Meta Title') }}</label>
							<input type="text" name="meta_title" class="form-control" required="" value="{{ $json->meta_title }}">
						</div>				
						<div class="form-group">
							<label>{{ __('Meta Keyword') }}</label>
							<input type="text" name="meta_keyword" class="form-control" required="" value="{{ $json->meta_keyword }}">
						</div>
						<div class="form-group">
							<label>{{ __('Meta Description') }}</label>
							<textarea class="form-control" name="meta_description" required="" >{{ $json->meta_description }}</textarea>
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