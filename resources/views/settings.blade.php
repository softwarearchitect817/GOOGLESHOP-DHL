@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Profile Settings'])
@endsection
@section('content')
<div class="card">
    <div class="card-body">
       <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger none">
              <ul id="errors">
              </ul>
          </div>
          <div class="alert alert-success none">
              <ul id="success">
              </ul>
          </div>
      </div>
      <div class="col-md-6">


        <form method="post" class="basicform" action="{{ route('my.profile.update') }}">
            @csrf
            <h4 class="mb-20">{{ __('Edit Genaral Settings') }}</h4>
            <div class="custom-form">
                <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control" required placeholder="Enter User's  Name" value="{{ Auth::user()->name }}"> 
                </div>
                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="text" name="email" id="email" class="form-control" required placeholder="Enter Email"  value="{{ Auth::user()->email }}"> 
                </div>
               
                <div class="form-group">
                    <button type="submit" class="btn btn-info basicbtn">{{ __('Update') }}</button>
                </div>
            </div>
        </form>

    </div>
    <div class="col-md-6">

        <form method="post" class="basicform" action="{{ route('my.profile.update') }}">
            @csrf
            <h4 class="mb-20">{{ __('Change Password') }}</h4>
            <div class="custom-form">
                <div class="form-group">
                    <label for="oldpassword">{{ __('Old Password') }}</label>
                    <input type="password" name="password_current" id="oldpassword" class="form-control"  placeholder="Enter Old Password" required> 
                </div>
                <div class="form-group">
                    <label for="password">{{ __('New Password') }}</label>
                    <input type="password" name="password" id="password" class="form-control"  placeholder="Enter New Password" required> 
                </div>
                <div class="form-group">
                    <label for="password1">{{ __('Enter Again Password') }}</label>
                    <input type="password" name="password_confirmation" id="password1" class="form-control"  placeholder="Enter Again" required> 
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary basicbtn">{{ __('Change') }}</button>
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