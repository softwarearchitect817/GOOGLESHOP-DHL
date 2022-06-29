@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Themes'])
@endsection
@section('content')
@if(Session::has('success'))
<div class="row">
	<div class="col-sm-12">
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>{{ Session::get('success') }}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	</div>
</div>
@endif
<div class="row"> 
	@foreach($posts as $row)	
	<div class="col-12 col-sm-6 col-md-6 col-lg-3">
		<article class="article">
			<div class="article-header">
				<div class="article-image" data-background="{{ asset($row->asset_path.'/screenshot.png') }}" >
				</div>
				<div class="article-title">
					<h2><a href="#">{{ $row->name }}</a></h2>
				</div>
			</div>
			<div class="article-details">

				<div class="article-cta">
				    <form method="post" action="{{ route('seller.theme.update',$row->id) }}">
		            @method('PUT')
		            @csrf
					<button type="submit" class="btn btn-primary col-12" @if($active_theme->template_id == $row->id) disabled="" @endif>@if($active_theme->template_id == $row->id) {{ __('Installed') }} @else {{ __('Active') }} @endif</button>
				    </form>
				    
				    @if ($active_theme->template_id == $row->id)
				    <form method="post" action="{{ route('seller.theme.customise',$row->id) }}">
		            @method('GET')
		            @csrf
					<button type="submit" class="btn btn-success col-12 mt-1">
						{{ __('Customise') }}
					</button>
					</form>
					@endif
				</div>
			</div>
		</article>
	</div>
	
	@endforeach

	{{ $posts->links('vendor.pagination.bootstrap-4') }}
</div>


@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>

@endpush