@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Customers'])
@endsection
@section('content')
<div class="card">
	<div class="card-body">
			<div class="float-right">
					<a href="{{ route('seller.customer.create') }}" class="btn btn-primary float-right">{{ __('Create Customer') }}</a>
				</div>
		<br><br>
		<div class="float-right">
			<form>
				<div class="input-group mb-2">

					<input type="text" id="src" class="form-control" placeholder="Search..." required="" name="src" autocomplete="off" value="{{ $src ?? '' }}">
					<select class="form-control selectric" name="type" id="type">
						<option value="name">{{ __('Search By Name') }}</option>
						<option value="email">{{ __('Search By Email') }}</option>
						<option value="id">{{ __('Search By Id') }}</option>
					</select>
					<div class="input-group-append">                                            
						<button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</form>
		</div>
		<form method="post" action="{{ route('seller.customers.destroys') }}" class="basicform">
			@csrf
			<div class="float-left">
				<div class="input-group">
					<select class="form-control selectric" name="type">
						<option selected="">{{ __('Select Action') }}</option>
						<option value="delete" class="text-danger">{{ __('Delete Permanently') }}</option>
					</select>
					<div class="input-group-append">                                            
						<button class="btn btn-primary basicbtn" type="submit">{{ __('Submit') }}</button>
					</div>
				</div>
			</div>
			<div class="table-responsive custom-table">
				<table class="table">
					<thead>
						<tr>
							<th class="am-select">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input checkAll" id="selectAll">
									<label class="custom-control-label checkAll" for="selectAll"></label>
								</div>
							</th>
							<th class="am-title">{{ __('Name') }}</th>
							<th class="am-title">{{ __('Email') }}</th>
							<th class="am-title">{{ __('Total Orders') }}</th>
							<th class="am-date">{{ __('Registered At') }}</th>
							<th class="am-date">{{ __('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						 @php
		                 $plan=user_limit();
		                 $plan=filter_var($plan['customer_panel']);
                		
                         @endphp
						@foreach($posts as $row)
						<tr id="row{{  $row->id }}">
							<td>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $row->id }}" value="{{ $row->id }}">
									<label class="custom-control-label" for="customCheck{{ $row->id }}"></label>
								</div>
							</td>
							<td><a href="{{ route('seller.customer.show',$row->id) }}">{{ $row->name }} (#{{ $row->id }})</a> 
								<div>
									<a href="{{ route('seller.customer.edit',$row->id) }}">{{ __('Edit') }}</a>
								</div>
							</td>
							
							<td><a href="{{ route('seller.customer.show',$row->id) }}">{{ $row->email }}</a></td>
							
							
							<td>{{ number_format($row->orders_count) }}</td>
							
						
							<td>{{ $row->updated_at->diffForHumans() }}</td>
							<td>
								

								 <div class="dropdown d-inline">
                      <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       {{ __('Action') }}
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item has-icon" href="{{ route('seller.customer.edit',$row->id) }}"><i class="fas fa-user-edit"></i> {{ __('Edit Acount') }}</a>
                        <a class="dropdown-item has-icon" href="{{ route('seller.customer.show',$row->id) }}"><i class="fas fa-search"></i> {{ __('View User') }}</a>
                        <a class="dropdown-item has-icon" @if($plan == true) href="{{ route('seller.customer.login',$row->id) }}" @endif>@if($plan !== true) <i class="fa fa-lock text-danger"></i> @else <i class="fas fa-key"></i>  @endif {{ __('Login ') }}</a>

                      </div>
                    </div>

							</td>
						</tr>
						@endforeach
					</tbody>

					<tfoot>
						<tr>
							<th class="am-select">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input checkAll" id="selectAll">
									<label class="custom-control-label checkAll" for="selectAll"></label>
								</div>
							</th>
							<th class="am-title">{{ __('Name') }}</th>
							<th class="am-title">{{ __('Email') }}</th>
							<th class="am-title">{{ __('Total Orders') }}</th>
							<th class="am-date">{{ __('Registered At') }}</th>
							<th class="am-date">{{ __('Action') }}</th>
						</tr>
					</tfoot>
				</table>
				
			</form>
			{{ $posts->links('vendor.pagination.bootstrap-4') }}
			<span>{{ __('Note') }}: <b class="text-danger">{{ __('For Better Performance Remove Unusual Users') }}</b></span>
		</div>
	</div>
</div>


@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/js/success.js') }}"></script>
@endpush