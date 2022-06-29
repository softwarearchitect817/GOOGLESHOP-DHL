@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Make Credits Payment'])
@endsection
@section('content')
@php
$stripe=false;
@endphp

<div class="container py-5">
	<div class="row">
		<div class="col-lg-7 mx-auto">
			<div class="card">
				<div class="card-header">
					<div class="card-body">
						<!-- Credit card form content -->
						<div class="tab-content">
							<!-- credit card info-->
							@foreach($getways as $key => $row)
							<div id="{{ $row->slug }}" class="tab-pane fade @if($key==0) show active  @endif pt-3">
								@if($row->slug == 'stripe')
    								@php
    								$stripe=true;
    								@endphp
    								<script src="https://js.stripe.com/v3/"></script>
    								<form action="{{ route('seller.make_charge_credit', $num) }}" method="post" id="payment-form">
    									@csrf
    									@php
    									 $credentials=json_decode($row->credentials->content ?? '');
    									@endphp
    									<input type="hidden" name="mode" value="{{ $row->id }}">
    									<input type="hidden" id="publishable_key" value="{{ $credentials->publishable_key }}">
    									<input type="hidden" id="amount" name="amount" value="{{ $amount }}">
    									<div class="form-group">
    										<label for="card-element">
    											Credit or debit card
    										</label>
    										<div id="card-element">
    											<!-- A Stripe Element will be inserted here. -->
    										</div>
    
    										<!-- Used to display form errors. -->
    										<div id="card-errors" role="alert"></div>
    									@if($main_price > 0)
    									<button type="submit" class="subscribe btn btn-primary btn-block shadow-sm mt-2"> Make Payment With {{ $row->name }} ({{ $price }}) </button>
    									@else
    										<a href="{{ url(env('APP_URL').'/contact') }}" target="_blank"  class="subscribe btn btn-primary btn-block shadow-sm text-white">{{ __('Please Contact With Us') }}</a>
    									@endif
    									</div>
    									<div class="form-group">
    										<label >
    											<h6>{{ __('Name') }}</h6>
    										</label> 
    										<input type="text" name="name" readonly="" value="{{ Auth::user()->name }}" class="form-control "> 
    									</div>
    									<div class="form-group">
    										<label >
    											<h6>{{ __('Email') }}</h6>
    										</label> 
    										<input type="text" name="email" readonly="" value="{{ Auth::user()->email }}" class="form-control "> 
    									</div>
    									<div class="form-group">
    										<label for="username">
    											<h6>{{ __('Phone Number') }}</h6>
    										</label> 
    										<input type="number" name="phone" placeholder="Enter Your Phone Number" required class="form-control "> 
    									</div>
    								</form>
    							@endif
							</div> 
							@endforeach
							<!-- Paypal info -->
							
							
							<!-- End -->
						</div>
						
					</div>
				</div>
				<div class="card-footer">
					<table class="table table-hover table-borderlress col-12">
								<tr>
									<td>{{ __('Credits Numerical') }}</td>
									<td>{{ $num}}</td>
								</tr>
								<tr>
									<td>{{ __('Plan Price') }}</td>
									<td>{{ number_format($main_price,2)}}</td>
								</tr>
								<tr>
									<td>{{ __('Tax') }}</td>
									<td>{{ number_format($tax,2) }}</td>
								</tr>
								<tr>
									<td>{{ __('Total Amount') }}</td>
									<td>{{ $price }}</td>
								</tr>
						</table>
				</div>
			</div>
		</div>

	</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

@endsection	
@push('js')
@if($stripe == true)
<script src="{{ asset('assets/js/stripe.js') }}"></script>
@endif
@endpush