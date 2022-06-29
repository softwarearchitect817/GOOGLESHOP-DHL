@extends('layouts.app')
@push('style')
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Product Varient'])
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
								<a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/varient') }}"><i class="fas fa-expand-arrows-alt"></i> {{ __('Variants') }}</a>
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
						<div class="card-body">
							<button class="btn btn-primary float-right mb-2 add_attr" data-toggle="modal" data-target="#attribute_modal">{{ __('Create Varient') }}</button>
							<div id="accordion">
								<div class="">
									<form action="{{ route('seller.product.variation',$info->id) }}" class="basicform">
										@csrf
									<table class="table table-hover table-border">
										<thead >
											<tr>
												<th class="text-left" >{{ __('Attribute') }}</th>
												<th>{{ __('Values') }}</th>
												<th >{{ __('Trash')  }}</th>
											</tr>
										</thead>
										<tbody id="data-body">
										@php
										$i=1;	
										@endphp	
										@foreach($variations as $key => $value)
										
										@php
										$i++;	
										@endphp
										<tr class="attr_{{ $i }}">
											<td>
												<select  data-id="{{ $i }}" class="form-control parent_attr selec{{ $i }}">
													<option value="" disabled selected>Select Varient</option>
													@foreach ($posts as $k => $row)
													<option data-parentattribute="{{ $row->childrenCategories }}" value="{{ $row->id }}" @if($key == $row->id) selected @endif>{{ $row->name }}</option>
													@endforeach
												</select>
											</td>
											<td >
												<select name="child[{{ $key }}][]" multiple  class="form-control select2 multislect child{{ $i }}" >
													@foreach ($posts as $post)
													@if($key == $post->id)
														@foreach ($post->childrenCategories as $item)
														<option class="attr{{ $i }}" value="{{ $item->id }}" @if(in_array($item->id,$attribute)) selected @endif>{{ $item->name }}</option>
														@endforeach
														
													@endif
													@endforeach
												</select>
											</td>
											<td>
												<a data-id="{{ $i }}" class="btn btn-danger remove_attr text-white"><i class="fa fa-trash"></i></a>
											</td>
										</tr>	
										@endforeach
											
										
										</tbody>	
									</table>
									<button class="btn btn-primary basicbtn">{{ __('Save Changes') }}</button>
								</form>
								</div>
							
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>

<div class="none">
<div  class="attrs_row">		
		@foreach($posts as $post)
		<option value="{{ $post->id }}" data-parentattribute="{{ $post->childrenCategories }}">{{ $post->name }}</option>
		@endforeach
</div>
</div>
@endsection
@push('js')

<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/seller/product/variants.js') }}"></script>

@endpush