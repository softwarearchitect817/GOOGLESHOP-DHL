@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Facebook Pixel Configuration'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Facebook Pixel') }}</h4><br>
       
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.marketing.store') }}" method="post">
          @csrf
          <input type="hidden" name="type" value="fb_pixel">
        <div class="form-group row mb-4">
         <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" > <a href="https://developers.facebook.com/docs/facebook-pixel/implementation" target="_blank">{{ __('Your Pixel Id') }}</a></label>
          <div class="col-sm-12 col-md-7">
            <input type="text" class="form-control" required="" name="pixel_id" placeholder="31064306894650" value="{{ $fb_pixel->value ?? '' }}">
          </div>
        </div>

       
       
       
      
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Status') }}</label>
          <div class="col-sm-12 col-md-7">
            <select class="form-control selectric" name="status">
              @if(!empty($fb_pixel))
              <option value="1" @if($fb_pixel->status  == 1) selected="" @endif>{{ __('Enable') }}</option>
              <option value="0"  @if($fb_pixel->status  == 0) selected="" @endif>{{ __('Disable') }}</option>
              @else
              <option value="1">{{ __('Enable') }}</option>
              <option value="0" >{{ __('Disable') }}</option>
              @endif
            </select>
          </div>
        </div>
         
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
          <div class="col-sm-12 col-md-7">
            <button class="btn btn-primary basicbtn" type="submit">{{ __('Save') }}</button><br>
            <small>{{ __('Note:') }} </small> <small class="text-danger mt-4">{{ __('After You Update Settings The Action Will Work After 5 Minutes') }}</small>
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