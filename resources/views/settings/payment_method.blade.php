@extends('layouts.app')
@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-colorpicker.min.css') }}">
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Payment Options'])
@endsection
@section('content')
<div class="row">
  <div class="col-12 col-sm-12 col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Settings') }}</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-sm-12 col-md-4">
            <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
              <li class="nav-item">
                <a class="nav-link active show" id="home-tab4" data-toggle="tab" href="#home4" role="tab" aria-controls="home" aria-selected="true">{{ __('Manual Payments') }}</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="profile-tab4" data-toggle="tab" href="#profile4" role="tab" aria-controls="profile" aria-selected="false">{{ __('Alternative Payments') }}</a>
              </li>

            </ul>
          </div>
          <div class="col-12 col-sm-12 col-md-8">
            <div class="tab-content no-padding" id="myTab2Content">
              <div class="tab-pane fade active show" id="home4" role="tabpanel" aria-labelledby="home-tab4">

                <table class="table table-hover card-table">
                  <tbody>
                    @foreach($cod as $row)
                    <tr>
                      <td>
                        <p class="mb-0"><b>{{ $row->name }}</b></p>
                        <small class="show">{{ $row->description->content }}</small>
                        @if(!empty($row->active_getway))
                        <p class="mb-0 text-muted small">{{ __('Installed') }}</p>
                        @endif
                      </td>
                      <td width="70" class="text-right">
                        @if(!empty($row->active_getway))

                        <a href="{{ route('seller.payment.show',$row->slug) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        @else
                        <form method="POST" action="{{ route('seller.payment.store') }}">
                          @csrf
                          <input type="hidden" name="id" value="{{ $row->id }}">
                          <button type="submit" class="btn btn-primary">{{ __('Install') }}</button>
                        </form>
                        @endif
                      </td>
                    </tr>
                    @endforeach

                  </tbody>
                </table>

              </div>
              <div class="tab-pane fade" id="profile4" role="tabpanel" aria-labelledby="profile-tab4">
                  <span class="text-danger">Note: {{ __('before activating the payment gateway make sure your currency is supported with your currency') }}</span>
               <table class="table table-hover card-table">
                <tbody>
                  @foreach($posts as $row)
                  <tr>
                    <td>
                      <p class="mb-0"><b>{{ $row->name }}</b></p>
                      <small class="show">{{ $row->slug }}</small>
                      @if(!empty($row->active_getway))
                      <p class="mb-0 text-muted small">{{ __('Installed') }}</p>
                      @endif
                    </td>
                    <td width="70" class="text-right">
                      @if(!empty($row->active_getway))

                      <a href="{{ route('seller.payment.show',$row->slug) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                      @else
                       <form method="POST" action="{{ route('seller.payment.store') }}" class="basicform{{ $row->id }}">
                          @csrf
                          <input type="hidden" name="id" value="{{ $row->id }}">
                          <button type="submit" class="btn btn-primary basicbtn" data-id="{{ $row->id }}">{{ __('Install') }}</button>
                        </form>

                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

@endsection
@push('js')
<script type="text/javascript" src="{{ asset('assets/js/payment_method.js') }}"></script>
@endpush