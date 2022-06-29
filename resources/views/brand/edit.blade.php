@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Brand'])
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Edit Brand') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.brand.update',$info->id) }}" method="post" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Name') }}</label>
            <div class="col-sm-12 col-md-7">
              <input type="text" class="form-control" required="" name="name" value="{{ $info->name }}">
            </div>
          </div>
         



          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Featured') }}</label>
            <div class="col-sm-12 col-md-7">
              <select class="form-control selectric" name="featured">
                <option value="1" @if($info->featured==1) selected="" @endif>{{ __('Yes') }}</option>
                <option value="0"  @if($info->featured==0) selected="" @endif>{{ __('No') }}</option>

              </select>
            </div>
          </div>
          <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Thumbnail') }}</label>
          <div class="col-sm-12 col-md-7">
           <input type="file" name="file" accept="image/*" class="form-control">
          </div>
        </div>
          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
            <div class="col-sm-12 col-md-7">
              <button class="btn btn-primary basicbtn" type="submit">{{ __('Update') }}</button>
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

@endpush