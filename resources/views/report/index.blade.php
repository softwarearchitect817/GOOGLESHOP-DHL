@extends('layouts.app')
@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}">
@endpush
@section('content')
<div class="row">
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-primary">
			<div class="card-icon bg-primary">
				<i class="fas fa-shopping-bag"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Total Orders') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($total) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-danger">
			<div class="card-icon bg-danger">
				<i class="fas fa-ban"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Order Cancelled') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($canceled) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-warning">
			<div class="card-icon bg-warning">
				<i class="fas fa-history"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('In Processing') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($proccess) }}
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-success">
			<div class="card-icon bg-success">
				<i class="far fa-check-square"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Order Complete') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($canceled) }}
				</div>
			</div>
		</div>
	</div>


	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-primary">
			<div class="card-icon bg-primary">
				<i class="fas fa-money-check-alt"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Total Amount') }}</h4>
				</div>
				<div class="card-body">
					{{ amount_format_order($amounts, $default_currency['icon']) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-danger">
			<div class="card-icon bg-danger">
				<i class="fas fa-money-bill"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Canceled Amount') }}</h4>
				</div>
				<div class="card-body">
					{{ amount_format_order($amount_cancel,$default_currency['icon']) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-warning">
			<div class="card-icon bg-warning">
				<i class="fas fa-money-bill"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Pending Amount') }}</h4>
				</div>
				<div class="card-body">
					{{ amount_format_order($amount_proccess,$default_currency['icon']) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-success">
			<div class="card-icon bg-success">
				<i class="fas fa-money-bill"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Earnings Amount') }}</h4>
				</div>
				<div class="card-body">
					{{ amount_format_order($amount_completed, $default_currency['icon']) }}
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-12">
		<div class="card card-primary">
			<div class="card-header">
				<h4>{{ __('Orders') }}</h4>
				<form class="card-header-form">
					<div class="d-flex">
						<input type="text" name="start" class="form-control datepicker" value="{{ $start }}">
						
						<input type="text" name="end" class="form-control datepicker" value="{{ $end }}">

						<button class="btn btn-primary btn-icon" type="submit"><i class="fas fa-search"></i></button>
					</div>					
				</form>
			</div>
			<div class="card-body">
				

				<div class="table-responsive">
					<table class="table table-striped table-md table-hover">
						<tbody><tr>
							<th class="text-left" >{{ __('Invoice No') }}</th>
							<th >{{ __('Date') }}</th>
							<th>{{ __('Customer') }}</th>
							<th class="text-right">{{ __('Order total') }}</th>
							<th>{{ __('Payment') }}</th>
							<th>{{ __('Fulfillment') }}</th>
							<th class="text-right">{{ __('Item(s)') }}</th>
							<th class="text-right">{{ __('Invoice') }}</th>
						</tr>
						
						@foreach($orders as $key => $row)
						@php $currency = json_decode($row->currency); @endphp
						<tr>

							<td><a href="{{ route('seller.invoice',$row->id) }}" >{{ $row->order_no }}</a></td>
							<td><a href="{{ route('seller.order.show',$row->id) }}">{{ $row->created_at->format('d-F-Y') }}</a></td>
							<td> @if(empty($row->user_id)) {{ __('Guest Order') }} @else <a href="{{ route('seller.customer.show',$row->user_id) }}">{{ $row->customer->name ?? '' }}</a>  @endif</td>
							<td class="text-right">{{ amount_format_order($row->total, $currency->currency_icon) }}</td>
							<td>
								@if($row->payment_status==2)
								<span class="badge badge-warning">{{ __('Pending') }}</span>

								@elseif($row->payment_status==1)
								<span class="badge badge-success">{{ __('Complete') }}</span>

								@elseif($row->payment_status==0)
								<span class="badge badge-danger">{{ __('Cancel') }}</span> 
								@elseif($row->payment_status==3)
								<span class="badge badge-danger">{{ __('Incomplete') }}</span> 

								@endif
							</td>
							<td>
								@if($row->status=='pending')
								<span class="badge badge-warning">{{ __('Awaiting processing') }}</span>

								@elseif($row->status=='processing')
								<span class="badge badge-primary">{{ __('Processing') }}</span>

								@elseif($row->status=='ready-for-pickup')
								<span class="badge badge-info">{{ __('Ready for pickup') }}</span>

								@elseif($row->status=='completed')
								<span class="badge badge-success">{{ __('Completed') }}</span>

								@elseif($row->status=='archived')
								<span class="badge badge-warning">{{ __('Archived') }}</span>
								@elseif($row->status=='canceled')
								<span class="badge badge-warning">{{ __('Canceled') }}</span>

								@else
								<span class="badge badge-info">{{ $row->status }}</span>

								@endif
							</td>

							<td class="text-right"> {{ $row->order_items_count }}</td>
							<td class="text-right">
								<a href="{{ route('seller.order.show',$row->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
							</td>
						</tr>
						@endforeach
						
					</tbody></table>
					{{ $orders->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@push('js')
<script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
@endpush