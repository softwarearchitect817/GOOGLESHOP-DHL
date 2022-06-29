@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Order No: '.$info->order_no])
@endsection
@section('content')
@php
 $info_currency = json_decode($info->currency);
@endphp
<div class="row" id="order">

	<div class="col-12 col-lg-8">
		@if($info->status=='pending')
		<div class="card card-warning">

		@elseif($info->status=='processing')
		<div class="card card-primary">

		@elseif($info->status=='ready-for-pickup')
		<div class="card card-info">

		@elseif($info->status=='completed')
		<div class="card card-success">

		@elseif($info->status=='archived')
		<div class="card card-danger">
		@elseif($info->status=='canceled')
		<div class="card card-danger">

		@else
		<div class="card card-primary">

		@endif
		
			

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
					@foreach($info->order_item as $row)
					<li class="list-group-item">
						<div class="row align-items-center">
							<div class="col-6">
								<a href="{{ url('/seller/product/'.$row->term->id.'/edit') }}">{{ $row->term->title ?? '' }}<br>
									</a>
									@php
									$variations=json_decode($row->info);
									 
									@endphp
									@foreach ($variations->attribute ?? [] as $item)
									
									<span>{{ __('Variation') }} :</span> <small>{{ $item->attribute->name ?? '' }} - {{ $item->variation->name ?? '' }}</small>
									@endforeach
									<br>
									@foreach ($variations->options ?? [] as $option)
									<span>{{ __('Options') }} :</span> <small>{{ $option->name ?? '' }}</small>
									@endforeach
									
								</div>
								<div class="col-3 text-right">
								    @php $currency = json_decode($row->currency); @endphp
									{{ amount_format_order($row->amount, $currency->currency_icon) }} × {{ $row->qty }}
								</div>
								<div class="col-3 text-right">
									{{  amount_format_order($row->amount*$row->qty, $currency->currency_icon) }}
								</div>
							</div> 
						</li>
						@endforeach

						<li class="list-group-item">
							<div class="row align-items-center">


								<div class="col-6" data-toggle="modal" data-target="#shippingmethodModal" style="cursor : point">
									{{ $info->shipping_info->shipping_method->name ?? '' }}
								</div>
								<div class="col-3 text-right">
									{{ __('Shipping Fee') }}
								</div>
								<div class="col-3 text-right">
									{{ amount_format_order($info->shipping, $info_currency->currency_icon) }}
								</div>
							</div> 
						</li>
						<li class="list-group-item">
							<div class="row align-items-center">
								<div class="col-9 text-right">{{ __('Tax') }}</div>
								<div class="col-3 text-right"> {{ amount_format_order($info->tax, $info_currency->currency_icon) }} </div>
							</div> 
						</li>


						<li class="list-group-item">
							<div class="row align-items-center">
								<div class="col-9 text-right">{{ __('Discount') }}</div>
								<div class="col-3 text-right"> {{ amount_format_order($order_content->coupon_discount, $info_currency->currency_icon) }} </div>
							</div> 
						</li>
						<li class="list-group-item">
							<div class="row align-items-center">
								<div class="col-9 text-right">{{ __('Subtotal') }}</div>
								<div class="col-3 text-right"> {{ amount_format_order($order_content->sub_total, $info_currency->currency_icon) }} </div>
							</div> 
						</li>
						<li class="list-group-item">
							<div class="row align-items-center">
								<div class="col-9 text-right">{{ __('Total') }}</div>
								<div class="col-3 text-right">{{ amount_format_order($info->total, $info_currency->currency_icon) }}</div>
							</div> 
						</li>
					</ul>
				</div>

				<div class="card-footer">
					
					<div class="text-right">
						
						<form method="POST" action="{{ route('seller.order.update',$info->id) }}" accept-charset="UTF-8" class="d-inline basicform">
							@csrf
							@method('PUT')
								
							<input type="checkbox" id="vehicle3" name="mail_notify" value="1">
							<label for="vehicle3"> {{ __('Notify To Customer') }}</label>
							
							

							<div class="btn-group">
							<select class="form-control" name="payment_status">
								<option disabled=""><b>{{ __('Select Payment Status') }}</b></option>
								<option value="1" @if($info->payment_status=='1') selected="" @endif>{{ __('Payment Complete') }}</option>
								<option value="2" @if($info->payment_status=='2') selected="" @endif>{{ __('Payment Pending') }}</option>
								<option value="0" @if($info->payment_status=='0') selected="" @endif>{{ __('Payment Cancel') }}</option>
								<option value="3" @if($info->payment_status=='3') selected="" @endif>{{ __('Payment Incomplete') }}</option>
							</select>
							&nbsp&nbsp
							<select class="form-control" name="status">
								<option disabled=""><b>{{ __('Select Order Status') }}</b></option>
								<option value="pending" @if($info->status=='pending') selected="" @endif>{{ __('Awaiting processing') }}</option>
								<option value="processing" @if($info->status=='processing') selected="" @endif>{{ __('Processing') }}</option>
								<option value="ready-for-pickup" @if($info->status=='ready-for-pickup') selected="" @endif>{{ __('Ready for pickup') }}</option>
								<option value="completed" @if($info->status=='completed') selected="" @endif>{{ __('Completed') }}</option>
								<option value="archived" @if($info->status=='archived') selected="" @endif>{{ __('Archived') }}</option>
								<option value="canceled" @if($info->status=='canceled') selected="" @endif>{{ __('Canceled') }}</option>
								
							</select>
								
							</div>

						

					</div>
					
					<button type="submit" class="btn btn-primary float-right mt-2 ml-2 basicbtn">{{ __('Save Changes') }}</button>
					<a href="{{ route('seller.invoice',$info->id) }}" class="btn btn-primary text-right float-right mt-2">{{ __('Print Invoice') }}</a>
					</form>
				</div>
			</div>
		</div>

		<div class="col-12 col-lg-4">
			<div class="card-grouping">
				<div class="card">
					<div class="card-header">
						<h4>{{ __('Status') }}</h4>

					</div>
					<div class="card-body">

						<p>{{ __('Payment Status') }} 
							@if($info->payment_status==2)
							<span class="badge badge-warning float-right">{{ __('Pending') }}</span>

							@elseif($info->payment_status==1)
							<span class="badge badge-success float-right">{{ __('Paid') }}</span>

							@elseif($info->payment_status==0)
							<span class="badge badge-danger float-right">{{ __('Cancel') }}</span> 
							@elseif($info->payment_status==3)
							<span class="badge badge-danger float-right">{{ __('Incomplete') }}</span> 

						@endif</p>


						<p>{{ __('Order Status') }} @if($info->status=='pending')
							<span class="badge badge-warning float-right">{{ __('Awaiting processing') }}</span>

							@elseif($info->status=='processing')
							<span class="badge badge-primary float-right">{{ __('Processing') }}</span>

							@elseif($info->status=='ready-for-pickup')
							<span class="badge badge-info float-right">{{ __('Ready for pickup') }}</span>

							@elseif($info->status=='completed')
							<span class="badge badge-success float-right">{{ __('Completed') }}</span>

							@elseif($info->status=='archived')
							<span class="badge badge-danger float-right">{{ __('Archived') }}</span>
							@elseif($info->status=='canceled')
							<span class="badge badge-danger float-right">{{ __('Canceled') }}</span>

							@else
							<span class="badge badge-primary float-right">{{ $info->status }}</span>

						@endif</p>
					</div>
				</div>

				<div class="card">
					<div class="card-header">
						<h4>{{ __('Payment Mode') }}</h4>

					</div>
					<div class="card-body">
						@if($info->category_id  != null)
						<p>{{ __('Transaction Method') }}  <span class="badge  badge-success  float-right">{{ $info->getway->name ?? '' }} </span></p>
						<p>{{ __('Transaction Id') }} <span class="float-right">{{ $info->transaction_id ?? '' }}</span></p>
						@else
						<p>{{ __('Incomplete Payment') }}</p>
						@endif
					</div>
				</div>

				<div class="card">
					<div class="card-header">
						<h4 class="card-header-title">{{ __('Note') }}</h4>

					</div>
					<div class="card-body">
						<p class="mb-0">{{ $order_content->comment }}</p>
					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h4 class="card-header-title">{{ __('Customer') }}</h4>
					</div>
					<div class="card-body">
						@if($info->customer != null)
						<a href="{{ route('seller.customer.show',$info->customer->id) }}">{{ $info->customer->name }} (#{{ $info->customer->id }})</a>
						@else
						{{ __('Guest Customer') }}
						@endif
					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h4 class="card-header-title">{{ __('Shipping details') }}</h4>
					</div>
					<div class="card-body">
						<p class="mb-0">{{ __('Customer Name') }}: {{ $order_content->name ?? '' }}</p>
						<p class="mb-0">{{ __('Customer Email') }}: {{ $order_content->email ?? '' }}</p>
						<p class="mb-0">{{ __('Customer Phone') }}: {{ $order_content->phone ?? '' }}</p>
						<p class="mb-0">{{ __('Location') }}: {{ $info->shipping_info->city->name ?? '' }}</p>
						<p class="mb-0">{{ __('Zip Code') }}: {{ $order_content->zip_code ?? '' }}</p>
						<p class="mb-0">{{ __('Address') }}: {{ $order_content->address ?? '' }}</p>

						<p class="mb-0">{{ __('Shipping Method') }}: {{ $info->shipping_info->shipping_method->name ?? '' }}</p>
					</div>
				</div>

			</div>
		</div>

        <!-- Modal -->
        <div class="modal fade" id="shippingmethodModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('Set Tracking Number') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="col-md-12">
                <div class="fom-group">
               	<label>{{ __('Tracking Number') }}</label>
               	<br>
               	<input type="text" step="any" id="new_tracking_number" class="form-control" required="">
               </div>   
              </div>
               
               
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary basicbtn" onclick=set_tracking_number()>{{ __('Save') }}</button>
              </div>
          </form>
            </div>
          </div>
        </div>
	</div>		




@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script>
    function set_tracking_number(){
        
    }
</script>
@endpush