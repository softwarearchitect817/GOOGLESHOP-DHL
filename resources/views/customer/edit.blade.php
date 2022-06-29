@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Edit Customer'])
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Edit Customer') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.customer.update',$info->id) }}" method="post">
          @csrf
          @method('PUT')
          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Name') }}</label>
            <div class="col-sm-12 col-md-7">
              <input type="text" class="form-control" required="" name="name" value="{{ $info->name }}">
            </div>
          </div>

          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Email') }}</label>
            <div class="col-sm-12 col-md-7">
              <input type="email" class="form-control" required="" name="email" value="{{ $info->email }}">
            </div>
          </div>

          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Password') }}</label>
            <div class="col-sm-12 col-md-7">
              <input type="password" class="form-control" name="password">
            </div>
          </div>
          <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" ></label>
            <div class="col-sm-12 col-md-7">
              <input type="checkbox" name="change_password" id="change_password" value="1">
              <label for="change_password">{{ __('Password') }}</label>
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