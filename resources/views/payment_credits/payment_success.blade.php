@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Success Credits Payment'])
@endsection
@section('content')

@if ($message = Session::get('success'))
    <div class="success">
        <strong>{{ $message }}</strong>
    </div>
@endif
 
 
@if ($message = Session::get('error'))
    <div class="error">
        <strong>{{ $message }}</strong>
    </div>
@endif

@endsection	