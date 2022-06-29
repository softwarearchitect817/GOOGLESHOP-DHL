@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Support'])
@endsection
@section('content')


@endsection
@push('js')
<script type="text/javascript" src="{{ asset('uploads/support.js') }}"></script>
@endpush