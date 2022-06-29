@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Plans'])
@endsection
@section('content') 
<div class="row justify-content-center"> 
	<div class="col-sm-12">
		@if(Session::has('fail'))
		<div class="alert alert-danger alert-dismissible show fade">
			<div class="alert-body">
				<button class="close" data-dismiss="alert">
					<span>×</span>
				</button>
				{{ Session::get('fail') }}
			</div>
		</div>
		@endif
		@if(Session::has('success'))
		<div class="alert alert-success alert-dismissible show fade">
			<div class="alert-body">
				<button class="close" data-dismiss="alert">
					<span>×</span>
				</button>
				{{ Session::get('success') }}
			</div>
		</div>
		@endif
		
	</div>

	@foreach($posts as $row)
	 @php
	 $data=json_decode($row->data);
	 @endphp
	<div class="col-12 col-md-4 col-lg-4">
		<div class="pricing @if($row->featured==1) pricing-highlight @endif">
			<div class="pricing-title">
				{{ $row->name }}
			</div>
			<div class="pricing-padding">
				<div class="pricing-price">
					<div>{{ amount_admin_format($row->price) }}</div>
					<div>@if($row->days == 365) {{ __('Yearly') }} @elseif($row->days == 30) Monthly @else {{ $row->days }}  Days @endif</div>
					<p>{{ $row->description }}</p>
				</div>
				<div class="pricing-details">
					<div class="pricing-item">
						
						<div class="pricing-item-label">{{ __('Products Limit') }} {{ $data->product_limit }}</div>
					</div>
					<div class="pricing-item">
						
						<div class="pricing-item-label">{{ __('Storage Limit') }} {{ number_format($data->storage) }}MB</div>
					</div>
					@if(env('MULTILEVEL_CUSTOMER_REGISTER') == true)
					<div class="pricing-item">						
						<div class="pricing-item-label">{{ __('Customer Limit') }} {{ number_format($data->customer_limit) }}</div>
					</div>
					@endif
					<div class="pricing-item">						
						<div class="pricing-item-label">{{ __('Shipping Zone Limit') }} {{ $data->location_limit ?? '' }}</div>
					</div>
					<div class="pricing-item">						
						<div class="pricing-item-label">{{ __('Category Limit') }} {{ $data->category_limit ?? '' }}</div>
					</div>
					<div class="pricing-item">						
						<div class="pricing-item-label">{{ __('Brand Limit') }} {{ $data->brand_limit ?? '' }}</div>
					</div>
					<div class="pricing-item">						
						<div class="pricing-item-label">{{ __('Product Variation Limit') }} {{ $data->variation_limit ?? '' }}</div>
					</div>

					<div class="pricing-item">
						<div class="pricing-item-label text-left">{{ __('Use your own domain') }} &nbsp&nbsp</div>
						@if($data->custom_domain == 'true' || $data->custom_domain == true)
						<div class="pricing-item-icon "><i class="fas fa-check"></i></div>
						@else
						<div class="pricing-item-icon  bg-danger text-white"><i class="fas fa-times"></i></div>
						@endif
					</div>
					
					<div class="pricing-item">
						<div class="pricing-item-label text-left">{{ __('Google Analytics') }} &nbsp&nbsp</div>
						
						<div class="pricing-item-icon {{ filter_var($data->google_analytics,FILTER_VALIDATE_BOOLEAN) ==  false ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->google_analytics == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
						
					</div>
					
					<div class="pricing-item">
						<div class="pricing-item-label text-left">{{ __('Facebook Pixel') }} &nbsp&nbsp</div>
						
						<div class="pricing-item-icon {{ $data->facebook_pixel == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->facebook_pixel == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
						
					</div>
					
					<div class="pricing-item">
						<div class="pricing-item-label text-left">{{ __('Google Tag Manager') }} &nbsp&nbsp</div>
						
						<div class="pricing-item-icon {{ $data->gtm == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->gtm == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
						
					</div>
					
					<div class="pricing-item">
						<div class="pricing-item-label text-left">{{ __('Whatsapp Plugin') }} &nbsp&nbsp</div>
						
						<div class="pricing-item-icon {{ $data->whatsapp == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->whatsapp == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
						
					</div>
					
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Inventory Management') }} &nbsp&nbsp</div>

						<div class="pricing-item-icon {{ $data->inventory == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->inventory == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('POS') }} &nbsp&nbsp</div>

						<div class="pricing-item-icon {{ $data->pos == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->pos == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('PWA') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon {{ $data->pwa == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->pwa == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('QRCODE') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon {{ $data->qr_code == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->qr_code == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Custom Js') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon {{ $data->custom_js == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->custom_js == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Custom Css') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon {{ $data->custom_css == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->custom_css == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Seller Support') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon {{ $data->live_support == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->live_support == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Customer Panel Access') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon {{ $data->customer_panel == 'false' ? 'bg-danger text-white' : ''  }} "><i class="{{ $data->customer_panel == 'false' ? 'fas fa-times' : 'fas fa-check'  }}"></i></div>
					</div>
					
					
					
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Multi Language') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon"><i class="fas fa-check"></i></div>
					</div>
					<div class="pricing-item">
						<div class="pricing-item-label">{{ __('Image Optimization') }} &nbsp&nbsp</div>
						<div class="pricing-item-icon"><i class="fas fa-check"></i></div>
					</div>
					
				</div>
			</div>
			<div class="pricing-cta">
				@if(url('/') == env('APP_URL'))
				<a href="{{ route('merchant.make_payment',$row->id) }}">{{ __('Subscribe') }} <i class="fas fa-arrow-right"></i></a>
				@else
				<a href="{{ route('seller.make_payment',$row->id) }}">{{ __('Subscribe') }} <i class="fas fa-arrow-right"></i></a>
				@endif
			</div>
		</div>
	</div>
	@endforeach


</div>

<div class="card">
	<div class="card-header">
		<h4>{{ __('Order History') }}</h4>
	</div>
	<div class="card-body">
		@php
		$posts=\App\Models\Userplan::with('plan','category')->where('user_id',Auth::id())->where('amount','>',0)->latest()->paginate(20);
		@endphp
	
	<div class="table-responsive">
		<table class="table table-hover table-nowrap card-table text-center">
			<thead>
				<tr>
					

					<th class="text-left" >{{ __('Order') }}</th>
					<th>{{ __('Name') }}</th>
					<th >{{ __('Purchase Date') }}</th>
					<th >{{ __('Expiry date') }}</th>
					
					<th>{{ __('Total') }}</th>
					<th>{{ __('Tax') }}</th>
					<th>{{ __('Payment Method') }}</th>
					<th>{{ __('Payment Status') }}</th>
					<th>{{ __('Fulfillment') }}</th>
					
				</tr>
			</thead>
			<tbody class="list font-size-base rowlink" data-link="row">
				@foreach($posts as $row)
				<tr>

					<td class="text-left">{{ $row->order_no }}</td>
					<td>{{ $row->plan_info->name ?? '' }}</td>
					<td>{{ $row->created_at->format('Y-m-d') }}</td>
					<td>{{ $row->will_expire }}</td>

					<td>{{ amount_admin_format($row->amount,2) }}</td>
					<td>{{ amount_admin_format($row->tax,2) }}</td>
					<td>{{ $row->category->name ?? '' }}</td>
					<td>
						@if($row->payment_status==1)
						<span class="badge badge-success">{{ __('Paid') }}</span>
						
						@elseif($row->payment_status==2)
						<span class="badge badge-warning">{{ __('Pending') }}</span>
						@else
						<span class="badge badge-danger">{{ __('Fail') }}</span>
						@endif
						
					</td>

					<td>
						@if($row->status == 1) <span class="badge badge-success">Approved</span> @elseif($row->status == 2) <span class="badge badge-warning">{{ __('Pending') }}</span>@elseif($row->status == 3) <span class="badge badge-danger">{{ __('Expired') }}</span>@else <span class="badge badge-danger">{{ __('Cancelled') }}</span> @endif

					</td>

				</tr>	
				@endforeach
			</tbody>
		</table>
	</div>
	</div>
	<div class="card-footer d-flex justify-content-between">
		{{ $posts->links('vendor.pagination.bootstrap-4') }}
	</div>
</div>
</div>
@endsection	