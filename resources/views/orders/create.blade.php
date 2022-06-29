@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Create Order'])
@endsection
@section('content')
@php
$url=domain_info('full_domain');
@endphp
<div class="card card-primary">
	<div class="card-header">
		<div class="input-group-btn  mr-3">
			<button class="btn btn-primary btn-sm ml-3" data-toggle="modal" data-target="#cartModal"><i class="fas fa-cart-arrow-down"></i> <span id="cart_count">{{ Cart::count() }}</span></button>
		</div>
		<h4></h4>

		<form class="card-header-form">
			<div class="input-group">
				<input type="number" required="" name="search" class="form-control" placeholder="Product id">
				
				<div class="input-group-btn  mr-3">
					<button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
				</div>
			</div>
		</form>
	</div>
	<div class="card-body">
		<div class="col-sm-12">
			<div class="table-responsive">
				<table class="table table-striped ">
					<tbody><tr>
						<th >{{ __('Id') }}</th>
						<th ><i class="far fa-image"></i></th>
						<th >{{ __('Name') }}</th>
						<th >{{ __('Price') }}</th>
						
						<th>{{ __('Variation') }}</th>
						<th>{{ __('Options') }}</th>
						<th class="text-right"><span class="mr-3">{{ __('Cart') }}</span></th>
					</tr>
					
					@foreach($posts as $row)
					@php
					
					$price=amount_format($row->price->price);
					$qty=$row->stock->stock_qty;
					$term_id=$row->id;
					
					@endphp
					<form method="post" class="basicform" action="{{ route('seller.order.store') }}">
						@csrf
						<input type="hidden" name="term_id" value="{{ $term_id }}">
						<tr>
							<td><a href="{{ route('seller.product.edit',$row->id) }}">#{{ $row->id }}</a></td>
							<td><img src="{{ asset($row->preview->media->url ?? 'uploads/default.png') }}" height="50"></td>
							<td><a href="{{ url($url.'/product/'.$row->slug.'/'.$term_id) }}" target="_blank">{{ Str::limit($row->title,50) }}</a></td>
							<td><span id="price{{ $row->id }}">{{ $price }}</span> </td>
							
							<td>
								<select class="form-control" name="variation[]" >
									<option disabled selected>{{ __('Select Variation') }}</option>
									@foreach($row->attributes as $attribute)
									<option  value="{{ $attribute->id }}"  >{{ $attribute->attribute->name }} - {{ $attribute->variation->name }}</option>
									@endforeach
								</select> 
							</td>
							<td>
								@if(count($row->options) > 0)
								<select class="form-control option" name="option[]" >
									
									<option disabled selected>{{ __('Select Option') }}</option>
								
									@foreach($row->options as $option)
									
									<option @if($option->type == 1) disabled @endif>{{ $option->name }}</option>
									
									@foreach ($option->childrenCategories as $item)
									<option  value="{{ $item->id }}" data-mainprice="{{ $row->price->price }}" data-productid="{{ $row->id }}" data-price="{{ $item->amount }}" data-amounttype="{{ $item->amount_type }}">&nbsp&nbsp {{ $item->name }}</option>
									@endforeach
									
									@endforeach
								</select> 
								@endif
							</td>

							<td>
								<div class="input-group">
									<input type="number" class="form-control"  @if($row->stock->stock_manage == 1) max="{{ $row->stock->stock_qty }}" min="0" @endif required="" value="1" name="qty">
									<div class="input-group-append">
										<button onclick="assignId({{ $row->id }})" @if($row->stock->stock_status == 0) disabled=""  @endif class="btn btn-primary btn-icon" id="submitbtn{{ $row->id }}" type="submit"><i class="fas fa-cart-arrow-down"></i></button>
									</div>
								</div>
							</td>
						</tr>
					</form>
					@endforeach
				</tbody></table>

			</div>
		</div>
	</div>
	<div class="card-footer">
		{{ $posts->links('vendor.pagination.bootstrap-4') }}
	</div>
</div>



<!-- Cart modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">{{ __('Cart Items') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>

								<th><i class="far fa-image"></i></th>
								<th>{{ __('Name') }}</th>
								<th>{{ __('Price') }}</th>
								<th>{{ __('Qty') }}</th>
								
								<th class="text-right">{{ __('Remove') }}</th>
							</tr>
						</thead>
						<tbody id="cart-content">

							@foreach(Cart::content() as $row)
							
							<tr class="cart-row cart{{ $row->rowId }}">
								
								<td><img src="{{ asset($row->options->preview) }}" height="50"></td>
								<td>{{ $row->name }}</td>
								<td>{{ number_format($row->price,2) }}</td>
								<td>{{ $row->qty }}</td>
								
								<td class="text-right"><a href="{{ route('seller.cart.remove',$row->rowId) }}"   class="btn btn-danger btn-sm" ><i class="fa fa-trash"></i></a></td>
								
							</tr>
							
							@endforeach
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
					<a href="{{ route('seller.checkout') }}" class="btn btn-primary">{{ __('Proceed to checkout') }}</a>
				</div>
			</div>
		</div>
	</div>
<input type="hidden" id="removecart_url" value="{{ url('/seller/order/cart/remove/') }}">
@endsection
@push('js')
<script src="{{ asset('assets/js/order_create.js') }}"></script>
@endpush
