@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Attribute'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Create Attribute') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform_with_reset" action="{{ route('seller.attribute.store') }}" method="post">
          @csrf
        <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Title') }}</label>
            <div class="col-sm-10 col-md-7">
              <input type="text" class="form-control" id="name">
            </div>
            <div class="col-sm-2 col-md-2">
              <select class="form-control" id="lang_select">
                @foreach($langlist ?? [] as $key => $row)
                    <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                @endforeach
              </select>
            </div>
            <input type="hidden" id="name_translations" name="name_translations" value=""/>
        </div>
            
       
      
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Featured') }}</label>
          <div class="col-sm-12 col-md-7">
            <select class="form-control selectric" name="featured">
              <option value="1">{{ __('Yes') }}</option>
              <option value="0" selected="">{{ __('No') }}</option>
             
            </select>
          </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
          <div class="col-sm-12 col-md-7">
            <button class="btn btn-primary basicbtn" type="submit">{{ __('Save') }}</button>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script>

var name_translations = {};
var local='{{ $local }}';

$('#name').on('change', function(e) {
    name_translations[$('#lang_select').val()] = $(this).val();
    $('#name_translations').val(JSON.stringify(name_translations));
});

$('#lang_select').on('change', function(e) {
    var name = name_translations[$('#lang_select').val()] || "";
    $('#name').val(name);
});
</script>
@endpush