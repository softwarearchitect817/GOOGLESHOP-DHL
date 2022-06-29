@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Product Feed Generate'])
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="float-left">
	            <a href="{{'#'}}" class="btn btn-primary float-left">{{ __('Add Category') }}</a>
	        </div>
	        <div class="float-right">
	            <a href="{{ '/seller/marketing/download/fb' }}" class="btn btn-danger float-right">{{ __('Download Meta') }}</a>
	            <a href="{{'/seller/marketing/download/google'}}" class="btn btn-danger float-right">{{ __('Download Google') }}</a>
	        </div>
            <form method="post" action={{route('seller.marketing.feedgen')}} class="basicform_with_reload">
                @csrf
                <div class="table-responsive custom-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="am-title">
                                    <input type="checkbox" id="selectAll" class="css-checkbox" name="selectAll" />
                                </th>
                                <th class="am-title">{{_('Id')}}</th>
                                <th class="am-title">{{_('Name')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $row)
                                <tr id="row{{ $row->id }}">
                                    <td><input type="checkbox" class="checkboxAll" name="ids[]" value="{{$row->id}}"></td>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->name}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-info">@if($exist_files == 1) {{'Re-Generate'}} @else {{'Generate'}} @endif</button>
            </form>
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