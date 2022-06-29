@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Syatem Template'])
@endsection
@section('content')
<div class="card">
	<div class="card-body">
	    	<div class="float-right">
					<a href="{{ route('seller.create-systememail') }}" class="btn btn-primary float-right">{{ __('Create System Email') }}</a>
					
				</div>
		
		<br><br>
	
		<form method="post" action="{{ route('seller.customers.destroys') }}" class="basicform">
			@csrf
		
			<div class="table-responsive custom-table">
				<table class="table">
					<thead>
						<tr>
						    <th class="am-title">{{ __('#') }}</th>
							<th class="am-title">{{ __('Title') }}</th>
							<th class="am-title">{{ __('Template For') }}</th>
							<th class="am-date">{{ __('Action') }}</th>
						</tr>
					</thead>
					<tbody>
					    		@foreach ($system_templates as $system_template)
                                    <tr id="row{{ $system_template->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td><img src="{{ asset($row->preview->content ?? 'uploads/default.png') }}"
                                                height="50"></td> --}}
                                       
                                        <td>{{ $system_template->title }}</td>
                                         <td>{{ $system_template->template_for }}</td>
                                        <td>
                                            <a href="{{ route('seller.update-systememail', $system_template->id )}}" class="btn btn-warning btn-sm text-center" ><i class="fas fa-edit"></i></a>
                                          
                                        </td>
                                    </tr>
                                @endforeach
					
					
					</tbody>

				
				</table>
				
			</form>

			<span>{{ __('Note') }}: <b class="text-danger">{{ __('For Better Performance Remove Unusual Users') }}</b></span>
		</div>
	</div>
</div>


@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/js/success.js') }}"></script>
@endpush