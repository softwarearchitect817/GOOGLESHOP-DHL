@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Coupon'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Create Coupon') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform_with_reset" action="{{ route('seller.coupon.store') }}" method="post">
          @csrf
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Code') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="text" class="form-control" required="" name="coupon_code">
          </div>
        </div>
        
         <div class="form-group row mb-4">
        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Expired date') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="date" class="form-control" required="" name="date">
          </div>
        </div>
     
       <div class="form-group row mb-4">
       <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Percentage') }} (%)</label>
          <div class="col-sm-12 col-md-7">
            <input type="number" class="form-control" required="" name="percent">
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
@endpush