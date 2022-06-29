@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Generated Product Feed'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Generated Feed') }}</h4><br>
       
      </div>
      <div class="card-body">
        @dump($data)
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>

@endpush