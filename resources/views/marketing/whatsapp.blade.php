@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Whatsapp Api Settings'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Whatsapp API Configuration') }}</h4><br>
       
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.marketing.store') }}" method="post">
          @csrf
          <input type="hidden" name="type" value="whatsapp">
        <div class="form-group row mb-4">
         <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" > {{ __('Whatsapp Number') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="number" class="form-control" required="" name="number" placeholder="Enter Your Whatsapp Number" value="{{ $json->phone_number ?? '' }}">
          </div>
        </div>

        <div class="form-group row mb-4">
         <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" > {{ __('Pretext For Product Page') }}</label>
          <div class="col-sm-12 col-md-7">
           <textarea class="form-control" required="" name="shop_page_pretext" placeholder="I want to purchase this">{{ $json->shop_page_pretext ?? '' }}</textarea>
           <span><span class="text-primary">{{ __('The Api Text Will Append Like This') }} : </span>{{ $json->shop_page_pretext ?? '' }} http:://url.com/product/product-name</span>
          </div>
        </div>

          <div class="form-group row mb-4">
         <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" > {{ __('Other Page Pretext') }}</label>
          <div class="col-sm-12 col-md-7">
           <textarea class="form-control" required="" name="other_page_pretext" placeholder="I want to purchase something">{{ $json->other_page_pretext ?? '' }}</textarea>
          
          </div>
        </div>

       
       
       
      
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Status') }}</label>
          <div class="col-sm-12 col-md-7">
            <select class="form-control selectric" name="status">
              @if(!empty($whatsapp))
              <option value="1" @if($whatsapp->status  == 1) selected="" @endif>{{ __('Enable') }}</option>
              <option value="0"  @if($whatsapp->status  == 0) selected="" @endif>{{ __('Disable') }}</option>
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