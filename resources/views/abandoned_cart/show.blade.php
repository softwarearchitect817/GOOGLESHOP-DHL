@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Abandoned Cart No: '.$abd_cart->id])
@endsection
@section('content')
@php $content = json_decode($abd_cart->content) @endphp
<div class="row" id="abandoned_cart">
	<div class="col-12 col-lg-8">
		<divw class="card card-warning">
		    <div class="card-body">
				<ul class="list-group list-group-lg list-group-flush list">
					<li class="list-group-item">
						<div class="row align-items-center">
							<div class="col-6">
								<strong>{{ __('Product') }}</strong>
							</div>
							<div class="col-3 text-right">
								<strong>{{ __('Amount') }}</strong>
							</div>
							<div class="col-3 text-right">
								<strong>{{ __('Total') }}</strong>
							</div>
						</div> 
					</li>
					@php
					    $total = 0;
					    $rate = 0;
					    $currency = '';
					@endphp
                    @foreach($content as $product)
                        @php
                            $rate = $product->rate;
                            $total = $total + $product->subtotal + $product->tax + $product->discount;
                            $currency = $product->currency;
                        @endphp
                        <li class="list-group-item">
    						<div class="row align-items-center">
    							<div class="col-6">
    							    {{ $product->name }}
    							</div>
    							<div class="col-3 text-right">
    								{{ $product->currency.' '.$product->price * $product->rate }} × {{ $product->qty }}
    							</div>
    							<div class="col-3 text-right">
    								{{  $product->currency.' '.$product->rate * $product->price * $product->qty }}
    							</div>
    						</div> 
    					</li>
    
    					<li class="list-group-item">
    						<div class="row align-items-center">
    							<div class="col-9 text-right">{{ __('Tax') }}</div>
    							<div class="col-3 text-right"> {{ $product->currency.' '.$product->tax * $product->rate }} </div>
    						</div> 
    					</li>
    
    
    					<li class="list-group-item">
    						<div class="row align-items-center">
    							<div class="col-9 text-right">{{ __('Discount') }}</div>
    							<div class="col-3 text-right"> {{ $product->currency.' '.$product->discount * $product->rate }} </div>
    						</div> 
    					</li>
    					<li class="list-group-item">
    						<div class="row align-items-center">
    							<div class="col-9 text-right">{{ __('Subtotal') }}</div>
    							<div class="col-3 text-right"> {{ $product->currency.' '.$product->subtotal * $product->rate }} </div>
    						</div> 
    					</li>
                    @endforeach
				</ul>
			</div>
		</div>

		<div class="col-12 col-lg-4">
			<div class="card-grouping">
				<div class="card">
					<div class="card-header">
						<h4 class="card-header-title">{{ __('Customer') }}</h4>
					</div>
					<div class="card-body">
                        @if(!$abd_cart->is_guest)
						    {{__($abd_cart->customer->name)}}
                        @else
						    {{ __('Guest Customer') }}
						@endif

					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h4 class="card-header-title">{{ __('Total') }}</h4>

					</div>
					<div class="card-body">
						<p class="mb-0">{{ $currency.' '.$total * $rate }}</p>
					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h4 class="card-header-title">{{ __('Created At') }}</h4>
					</div>
					<div class="card-body">
                        {{ $abd_cart->created_at }}
					</div>
				</div>
			</div>
		</div>


	</div>		




@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
@endpush