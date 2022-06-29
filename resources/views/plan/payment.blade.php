@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Make Payment'])
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
						<ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
							@foreach($getways as $key => $row)
							<li class="nav-item"> <a data-toggle="pill" href="#{{ $row->slug }}" class="nav-link @if($key == 0) active @endif"> <img height="50" src="{{ asset($row->preview->content) }}"></a> </li>
							@endforeach							
						</ul>

						<!-- Credit card form content -->
						<div class="tab-content">
							@php
							if(url('/') == env('APP_URL')){
								$url=url('/merchant/make-charge/');
							}
							else{
								$url=url('/seller/make-charge/');
							}
							
							@endphp
							<!-- credit card info-->
							@foreach($getways as $key => $row)
							<div id="{{ $row->slug }}" class="tab-pane fade @if($key==0) show active  @endif pt-3">
								@if($row->slug == 'stripe')
								@php
								$stripe=true;
								@endphp
								<script src="https://js.stripe.com/v3/"></script>
								<form action="{{ url($url.'/'.$info->id) }}" method="post" id="payment-form">
									@csrf
									@php
									 $credentials=json_decode($row->credentials->content ?? '');
									@endphp
									<input type="hidden" name="mode" value="{{ $row->id }}">
									<input type="hidden" id="publishable_key" value="{{ $credentials->publishable_key }}">
									<div class="form-group">

										
										<label for="card-element">
											Credit or debit card
										</label>
										<div id="card-element">
											<!-- A Stripe Element will be inserted here. -->
										</div>

										<!-- Used to display form errors. -->
										<div id="card-errors" role="alert"></div>

									@if($info->price > 0)
									<button type="submit" class="subscribe btn btn-primary btn-block shadow-sm mt-2"> Make Payment With {{ $row->name }} ({{ $price }}) </button>
									@else
										<a href="{{ url(env('APP_URL').'/contact') }}" target="_blank"  class="subscribe btn btn-primary btn-block shadow-sm text-white">{{ __('Please Contact With Us') }}</a>
									@endif
									</div>
								</form>
								@else
								<form role="form" method="post" action="{{ url($url.'/'.$info->id) }}">
									@csrf
									<input type="hidden" name="mode" value="{{ $row->id }}">
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
									@if($info->price > 0)
									<button type="submit" class="subscribe btn btn-primary btn-block shadow-sm"> {{ __('Make Payment With') }} {{ $row->name }} ({{ $price }}) </button>
									@else
									<a href="{{ url(env('APP_URL').'/contact') }}" target="_blank"  class="subscribe btn btn-primary btn-block shadow-sm text-white">{{ __('Please Contact With Us') }}</a>
									@endif
									
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
									<td>{{ __('Plan Name') }}</td>
									<td>{{ $info->name}}</td>
								</tr>
								<tr>
									<td>{{ __('Plan Price') }}</td>
									<td>{{ number_format($info->price,2)}}</td>
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