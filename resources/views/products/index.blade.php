@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Products'])
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
<div class="card @if($type==1) card-success @elseif($type==2) card-info @elseif($type==3) card-warning @elseif($type== 0 && $type != 'all') card-danger @endif">
	<div class="card-body">
		<div class="row mb-2">
			<div class="col-lg-8">
				<div class="">
					
					<a href="{{ route('seller.product.list',1) }}" class="mr-2 btn btn-outline-success @if($type==1) active @endif">{{ __('Publish') }} ({{ $actives }})</a>

					<a href="{{ route('seller.product.list',2) }}" class="mr-2 btn btn-outline-info @if($type==2) active @endif">{{ __('Draft') }} ({{ $drafts }})</a>

					<a href="{{ route('seller.product.list',3) }}" class="mr-2 btn btn-outline-warning @if($type==3) active @endif">{{ __('Incomplete') }} ({{ $incomplete }})</a>
					<a href="{{ route('seller.product.list',0) }}" class="mr-2 btn btn-outline-danger @if($type== 0 && $type != 'all') active @endif">{{ __('Trash') }} ({{ $trash }})</a>
				</div>
			</div>
			<div class="col-lg-4">
				
				<a href="#" class="btn btn-info float-right mr-3" data-toggle="modal" data-target="#import">{{ __('Import') }}</a>
				
				<div class="float-right mr-3">
					<a href="{{ route('seller.product.create') }}" class="btn btn-primary float-right">{{ __('Add New') }}</a>
				</div>
				
			</div>
		</div>
		<br>
		<div class="float-right">
			<form>
				<div class="input-group mb-2">

					<input type="text" id="src" class="form-control" placeholder="Search..." required="" name="src" autocomplete="off" value="{{ $src ?? '' }}">
					<select class="form-control selectric" name="type" id="type">
						<option value="title">{{ __('Search By Name') }}</option>
						<option value="id">{{ __('Search By Id') }}</option>
					</select>
					<div class="input-group-append">                                            
						<button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</form>
		</div>
		<form method="post" action="{{ route('seller.products.destroys') }}" class="basicform">
			@csrf
			<div class="float-left">
				<div class="input-group">
					<select class="form-control selectric" name="method">
						<option disabled selected="">{{ __('Select Action') }}</option>
						<option value="1">{{ __('Publish Now') }}</option>
						<option value="2">{{ __('Draft') }}</option>
						<option value="4">{{ __('Clone') }}</option>
						@if($type== 0 && $type != 'all')
						<option value="delete" class="text-danger">{{ __('Delete Permanently') }}</option>
						@else
						<option value="0">{{ __('Move To Trash') }}</option>
						@endif
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
							<th class="am-title"><i class="far fa-image"></i></th>
							<th class="am-title">{{ __('Name') }}</th>
							
							<th class="am-title">{{ __('Total Sales') }}</th>
							
							<th class="am-title">{{ __('Status') }}</th>
							<th class="am-date">{{ __('Last Update') }}</th>
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
							<td><img src="{{ asset($row->preview->media->url ?? 'uploads/default.png') }}" height="50" alt=""></td>
							<td>{{ $row->title }} (#{{ $row->id }}) 
								<div>
									<a href="{{ route('seller.product.edit',$row->id) }}">{{ __('Edit') }}</a> | <a href="{{ $url.'/product/'.$row->slug.'/'.$row->id }}" target="_blank">{{ __('Show') }}</a> 
								</div>
							</td>
							
							<td>{{ $row->order_count }}</td>
							
							<td>
								@if($row->status==1)
								<span class="badge badge-success">{{ __('Active') }}</span>
								@elseif($row->status==2)
								<span class="badge badge-info">{{ __('Draft') }}</span>
								@elseif($row->status==3)
								<span class="badge badge-warning">{{ __('Incomplete') }}</span>	
								@elseif($row->status==0)
								<span class="badge badge-danger">{{ __('Trash') }}</span>	

								@endif

							</td>
							<td>{{ $row->updated_at->diffForHumans() }}</td>
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
							<th class="am-title"><i class="far fa-image"></i></th>

							<th class="am-title">{{ __('Name') }}</th>
							
							<th class="am-title">{{ __('Total Sales') }}</th>
							
							<th class="am-title">{{ __('Status') }}</th>
							<th class="am-date">{{ __('Last Update') }}</th>
						</tr>
					</tfoot>
				</table>
				
			</form>
			{{ $posts->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="import" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">{{ __('Product Import') }}</h5>


				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('seller.products.import') }}" method="POST" class="basicform" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<div class="form-control">
							<input type="file" name="file" accept=".csv">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					

					<div class="import_area">
						<div>
							<p class="text-left"><a href="{{ asset('uploads/demo.csv') }}">{{ __('Download Sample') }}</a>
							</p>
						</div>

						
						<div>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
							<button type="submit" class="btn btn-primary basicbtn">{{ __('Import') }}</button>
						</div>

					</div>
					

				</div>
			</form>
		</div>
	</div>
</div>

@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/seller/product/index.js') }}"></script>
@endpush