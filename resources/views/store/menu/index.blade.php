@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Menus'])
@endsection
@section('content')

<div class="row">
	<div class="col-12 col-sm-12 col-lg-12">
		<div class="card">

			<div class="card-body">
       <table class="table table-hover card-table">
        <thead>
         <tr>
          <th>{{ __('Menu Position') }}</th>
          <th class="text-right">{{ __('Customize') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
         <td>{{ __('Header Menu') }}</td>
         <td class="text-right"><a href="{{ route('seller.menu.show','header') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a></td>
       </tr>

       <tr>
         <td>{{ __('Footer Left Menu') }}</td>
         <td class="text-right"><a href="{{ route('seller.menu.show','left') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a></td>
       </tr>

       <tr>
         <td>{{ __('Footer Center Menu') }}</td>
         <td class="text-right"><a href="{{ route('seller.menu.show','center') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a></td>
       </tr>
       <tr>
         <td>{{ __('Footer Right Menu') }}</td>
         <td class="text-right"><a href="{{ route('seller.menu.show','right') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a></td>
       </tr>
     </tbody>
   </table>
 </div>
</div>
</div>
</div>


@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>

@endpush