@extends('layouts.app')
@push('style')
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Create Page'])
@endsection
@section('content')


<div class="row">
	<div class="col-lg-12">      
		<form method="post" action="{{ route('seller.page.store') }}" id="productform">
			@csrf
			
			<div class="card">
				<div class="card-body">
					
					<div class="row">
						
						<div class="col-sm-12">
							
							{{ input((array('title'=>'Page Title','name'=>'title','is_required'=>true))) }}

							{{ textarea(array('title'=>'Page Excerpt','name'=>'excerpt','is_required'=>true)) }}
							
							{{ editor(array('title'=>'Page Content','name'=>'content','class'=>'content')) }}
							
							<div class="form-group">
								<button class="btn btn-primary basicbtn" type="submit">{{ __('Save') }}</button>
							</div>		
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>
</form>

@endsection
@push('js')
<script type="text/javascript" src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/form.js?v=1.0') }}"></script>
@endpush