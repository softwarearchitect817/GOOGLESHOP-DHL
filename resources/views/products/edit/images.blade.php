@extends('layouts.app')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/css/dropzone.css') }}">

@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Product Images'])
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
									<a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/image') }}"><i class="far fa-images"></i> {{ __('Images') }}</a>
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
												
							<form action="{{ route('seller.media.store') }}" enctype="multipart/form-data" class="dropzone" id="mydropzone">
								@csrf
								<input type="hidden" name="term" value="{{ $info->id }}">	
							</form>
							<div class="row">
								@foreach($info->medias as $key => $row)
								<div class="col-sm-3" id="m_area{{ $key }}">
									<div class="card">
										<div class="card-body">
											<img src="{{ asset($row->url) }}" alt="" height="100" width="150">
										</div>
										<div class="card-footer">
											<button class="btn btn-danger col-12" onclick="remove_image('{{ base64_encode($row->id) }}',{{ $key }})">{{ __('Remove') }}</button>
										</div>
									</div>
								</div>
								@endforeach	
							</div>

							
								
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>

<form class="basicform" action="{{ route('seller.medias.destroy') }}">
	@csrf
	<input type="hidden" name="m_id" id="m_id">
</form>
@endsection
@push('js')
<script type="text/javascript" src="{{ asset('assets/js/dropzone.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/seller/product/images.js') }}"></script>

@endpush