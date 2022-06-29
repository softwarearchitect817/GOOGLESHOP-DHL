@extends('layouts.app')
@push('style')
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Edit'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Edit Shipping Method') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.shipping.update',$info->id) }}" method="post">
          @csrf
          @method('PUT')
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Title') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="text" class="form-control" required="" name="title" value="{{ $info->name }}">
          </div>
        </div>
         <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Price') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="number" step="any" class="form-control" required="" name="price" value="{{ $info->slug }}">
          </div>
        </div>
         <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Locations') }}</label>
          <div class="col-sm-12 col-md-7">
            <select multiple class="form-control select2" name="locations[]" required="">
                  {{ ConfigCategoryMulti('city',$data) }}
                </select>
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
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/form.js') }}"></script>
@endpush