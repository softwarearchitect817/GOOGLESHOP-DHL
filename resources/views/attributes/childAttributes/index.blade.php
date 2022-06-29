@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Variations'])
@endsection
@section('content')
<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('seller.attributes-terms.destroy') }}" class="basicform_with_reload">
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

            <a href="{{ route('seller.attribute-term.show',$id) }}" class="btn btn-primary">{{ __('Create variation') }}</a>

          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover text-center table-borderless">
              <thead>
                <tr>
                  <th><input type="checkbox" class="checkAll"></th>

                  <th>{{ __('Name') }}</th>
                  <th>{{ __('Product Count') }}</th>
                  <th>{{ __('Attribute') }}</th>
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
                  <td>{{ $row->variations_count }}</td>
                   <td>{{ $row->parent->name ?? '' }}</td>
                  <td>@if($row->featured==1) <span class="badge badge-success">{{ __('Yes') }}</span> @else <span class="badge badge-danger">{{ __('No') }}</span> @endif</td>
                  <td>{{ $row->created_at->diffForHumans() }}</td>
                  <td><a href="{{ route('seller.attribute-term.edit',$row->id) }}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></a></td>

                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                 <th><input type="checkbox" class="checkAll"></th>

                 <th>{{ __('Name') }}</th>
                  <th>{{ __('Product Count') }}</th>
                 <th>{{ __('Attribute') }}</th>
                 <th>{{ __('Featured') }}</th>
                 <th>{{ __('Created at') }}</th>
                 <th>{{ __('Action') }}</th>
               </tfoot>
             </table>
             <span class="text-danger text-center"><span class="text-dark">Note:</span> {{ __('Before Delete Any Item Please Make Sure Product Count Must Be "0" Otherwise Product Will Not Append On Your Site') }} </span>
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