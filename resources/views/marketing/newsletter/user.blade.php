@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Users'])
@endsection
@section('content')
<div class="card">
	<div class="card-body">
		
		<br><br>
	
		<form method="post" action="{{ route('seller.send.customer')}}" method="post" class="basicform_with_reload">
			@csrf
		
			<div class="table-responsive custom-table">
			     <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputState">Templete Messege</label>
                      <select id="inputState" class="form-control" name ="template_id">
                        <option selected disabled>Choose...</option>
                        @foreach($templates as $template)
                            <option value = "{{$template->id}}"> {{ $template->title }}</option>
                        @endforeach
                      </select>
                    </div>
                    	<div class="float-right send-btn-email">
                    	    <center>
			    	            <button type="submit" class="btn btn-primary float-right basicbtn">{{ __('Send Email To User') }}</button>
                    	    </center>
				</div>
                </div>
				<table class="table">
					<thead>
						<tr>
						    <th class="am-title">
						        <input type="checkbox" id="selectAll" class="css-checkbox" name="selectAll"/>
						    </th>
							<th class="am-title">{{ __('Name') }}</th>
							<th class="am-title">{{ __('Email') }}</th>
						</tr>
					</thead>
					<tbody>
					     @foreach ($users as $user)
                                    <tr id="row{{ $user->id }}">
                                        <td><input type="checkbox" class="checkboxAll" name="emails[]" value="{{$user->email}}"/></td>
                                        {{-- <td><img src="{{ asset($row->preview->content ?? 'uploads/default.png') }}"
                                                height="50"></td> --}}

                                        <td>{{ $user->name }}</td>
                                         <td>{{ $user->email }}</td>
                                        
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

<script>
    $(document).ready(function(){
$("#selectAll").click(function(){
        if(this.checked){
            $('.checkboxAll').each(function(){
                $(".checkboxAll").prop('checked', true);
            })
        }else{
            $('.checkboxAll').each(function(){
                $(".checkboxAll").prop('checked', false);
            })
        }
    });
});
</script>
@endpush