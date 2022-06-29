@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Coupon'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Edit Coupon') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.coupon.update',$info->id) }}" method="post">
          @csrf
          @method('PUT')
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Code') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="text" class="form-control" required="" name="coupon_code" value="{{ $info->name }}">
          </div>
        </div>
         <div class="form-group row mb-4">
        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Expired date') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="date" class="form-control" required="" name="date"  value="{{ $info->slug }}">
          </div>
        </div>
     
       <div class="form-group row mb-4">
       <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Percentage') }} (%)</label>
          <div class="col-sm-12 col-md-7">
            <input type="number" class="form-control" required="" name="percent" value="{{ $info->featured }}">
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