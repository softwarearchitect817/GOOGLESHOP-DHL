@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Pages'])
@endsection
@section('content')
@php
$url=domain_info('full_domain');
@endphp
@if(Session::has('error')) 
<div class="row">
	<div class="col-sm-12">
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>{{ Session::get('error') }}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	</div>
</div>
@endif
<div class="card">
	<div class="card-body">

		<div class="float-right">
			@if($post_limit==true)
			<a href="{{ route('seller.page.create') }}" class="btn btn-primary float-right">{{ __('Add New') }}</a>
			@endif
			

		</div>
		<form method="post" action="{{ route('seller.pages.destroys') }}" class="basicform_with_remove">
			@csrf
			<div class="float-left mb-2">

				
				<div class="input-group">


					<select class="form-control selectric" name="method">
						<option disabled selected="">{{ __('Select Action') }}</option>

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
							
							<th class="am-title">{{ __('Url') }}</th>
							
							
							<th class="text-right">{{ __('Last Update') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($posts as $row)
						<tr id="row{{  $row->id }}">
							<td>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $row->id }}" value="{{ $row->id }}">
									<label class="custom-control-label" for="customCheck{{ $row->id }}"></label>
								</div>
							</td>
							
							<td>{{ $row->title }}
								<div>
									<a href="{{ route('seller.page.edit',$row->id) }}">{{ __('Edit') }}</a> | <a href="{{ $url.'/page/'.$row->slug.'/'.$row->id }}" target="_blank">{{ __('Show') }}</a> 
								</div>
							</td>
							<td>{{ $url.'/page/'.$row->slug.'/'.$row->id }}</td>
							

							
							
							<td class="text-right">{{ $row->updated_at->diffForHumans() }}</td>
						</tr>
						@endforeach
					</tbody>

					
				</table>
				
			</form>
			{{ $posts->links('vendor.pagination.bootstrap-4') }}
		</div>
	</div>
</div>


@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
@endpush