@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Attributes'])
@endsection
@section('content')
<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('seller.attributes.destroy') }}" class="basicform_with_reload">
          @csrf
          <div class="float-left mb-1">

            <div class="input-group">
              <select class="form-control" name="method">
                <option value="" >{{ __('Select Action') }}</option>
                <option value="delete" >{{ __('Delete Permanently') }}</option>

              </select>
              <div class="input-group-append">                                            
                <button class="btn btn-primary basicbtn" type="submit">{{ __('Submit') }}</button>
              </div>
            </div>

          </div>
          <div class="float-right mb-1">

            <a href="{{ route('seller.attribute.create') }}" class="btn btn-primary">{{ __('Create Attribute') }}</a>

          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover text-center table-borderless">
              <thead>
                <tr>
                  <th><input type="checkbox" class="checkAll"></th>

                  <th>{{ __('Name') }}</th>
                  <th>{{ __('Variations') }}</th>
                  <th>{{ __('Products') }}</th>
                  <th>{{ __('Featured') }}</th>
                  <th>{{ __('Created at') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($posts as $row)
                <tr id="row{{ $row->id }}">
                  <td><input type="checkbox"  name="ids[]" value="{{ $row->id }}"></td>

                  <td>{{ $row->name }}</td>
                   <td>@foreach($row->childrenCategories as $r) <span class="badge badge-primary">{{ $r->name }}</span> @endforeach</td>
                   <td>{{ $row->parent_variation_count }}</td>
                  <td>@if($row->featured==1) <span class="badge badge-success">{{ __('Yes') }}</span> @else <span class="badge badge-danger">{{ __('No') }}</span> @endif</td>
                  <td>{{ $row->created_at->diffForHumans() }}</td>
                  <td><a href="{{ route('seller.attribute.show',$row->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-cog"></i></a> <a href="{{ route('seller.attribute.edit',$row->id) }}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></a></td>

                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                 <th><input type="checkbox" class="checkAll"></th>

                 <th>{{ __('Name') }}</th>
                 <th>{{ __('Variations') }}</th>
                 <th>{{ __('Products') }}</th>
                 <th>{{ __('Featured') }}</th>
                 <th>{{ __('Created at') }}</th>
                 <th>{{ __('Action') }}</th>
               </tfoot>
             </table>

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