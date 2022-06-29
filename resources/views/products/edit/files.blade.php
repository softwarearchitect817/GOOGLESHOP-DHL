@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Files'])
@endsection
@section('content')

<div class="row">
	<div class="col-lg-12">      
		
		<div class="card">
			<div class="card-body">

				<div class="row">
					<div class="col-sm-3">
						<ul class="nav nav-pills flex-column">
							<li class="nav-item">
								<a class="nav-link" href="{{ route('seller.product.edit',$info->id) }}"><i class="fas fa-cogs"></i> {{ __('Item') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/price') }}"><i class="fas fa-money-bill-alt"></i> {{ __('Price') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link " href="{{ url('seller/product/'.$info->id.'/option') }}"><i class="fas fa-tags"></i> {{ __('Options') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/varient') }}"><i class="fas fa-expand-arrows-alt"></i> {{ __('Variants') }}</a>
							</li>
							
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/image') }}"><i class="far fa-images"></i> {{ __('Images') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/inventory') }}"><i class="fa fa-cubes"></i> {{ __('Inventory') }}</a>
							</li>

							<li class="nav-item">
								<a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/files') }}"><i class="fas fa-file"></i> {{ __('Files') }}</a>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/seo') }}"><i class="fas fa-chart-line"></i> {{ __('SEO') }}</a>
							</li>
							<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/express-checkout') }}"><i class="fas fa-cart-arrow-down"></i> {{ __('Express checkout') }}</a>
								</li>
						</ul>
					</div>
					<div class="col-sm-9">

						<button class="btn btn-primary float-right mb-2" data-toggle="modal" data-target="#attribute_modal">{{ __('Create File') }}</button>
						<p class="text-left text-danger">{{ __('Your customer will automatically receive the download link via email') }}</p>
							
							<div class="table-responsive">
								<table class="table table-hover table-nowrap card-table">
									<thead>
										<tr>
											
											<th>{{ __('URL') }}</th>
											<th width="200">&nbsp;</th>
										</tr>
									</thead>
									<tbody>
										@foreach($info->files as $row)
										<tr>
											
											<td>{{ $row->url }}</td>
											<td class="text-right">
												<div class="btn-group" role="group">
													<button type="button" class="btn btn-primary edit" data-toggle="modal" data-target="#editModal" data-id="{{ $row->id }}" data-attribute="{{ $row->attribute_id }}" data-name="{{ $row->name }}" data-url="{{ $row->url }}"><i class="fas fa-edit"></i></button>
													
														<button type="button" onclick="make_trash('{{ base64_encode($row->id) }}')" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
													
												</div>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						



					</div>
				</div>
			</div>
		</div>
	</div>

</div>

</div>


<!-- Modal -->
<form action="{{ route('seller.file.store') }}" class="basicform">
	@csrf
<div class="modal fade" id="attribute_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">{{ __('Add New File') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
				<input type="hidden" name="term" value="{{ $info->id }}">
				
				<div class="form-group">
					<label>{{ __('Url') }}</label>
					<input type="text" name="url" class="form-control" required="">
				</div>
				
			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
				<button type="submit" class="btn btn-primary basicbtn">{{ __('Save') }}</button>
			</div>
		</div>
	</div>
</div>
</form>

<form method="post" action="{{ route('seller.files.update') }}" class="basicform">
	@csrf
<div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">Edit</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="id" id="id">
				<input type="hidden" name="term" value="{{ $info->id }}">
				
				<div class="form-group">
					<label>{{ __('Url') }}</label>
					<input type="text" name="url" class="form-control" required="" id="url">
				</div>
				
			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
				<button type="submit" class="btn btn-primary basicbtn">{{ __('Save') }}</button>
			</div>
		</div>
	</div>
</div>
</form>


<form action="{{ route('seller.files.destroy') }}" id="basicform">
	@csrf
	<input type="hidden" name="a_id" id="m_id">
</form>
@endsection
@push('js')

<script type="text/javascript" src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/seller/product/files.js') }}"></script>
@endpush