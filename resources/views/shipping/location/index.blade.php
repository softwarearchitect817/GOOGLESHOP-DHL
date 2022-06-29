@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Country'])
@endsection
@section('content')
<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-body">
          <form method="post" action="{{ route('seller.locations.destroy') }}" class="basicform_with_reload">
            @csrf
            <div class="float-left mb-1">
              
              <div class="input-group">
                <select class="form-control" name="method">
                  <option value="" >{{ __('Select Action') }}</option>
                  <option value="delete" >{{ __('Delete Permanently') }}</option>

                </select>
                <div class="input-group-append">                                            
                  <button class="btn btn-primary basicbtn" type="submit">Submit</button>
                </div>
              </div>
             
            </div> 
              <div class="float-right mb-1">
              
           {{--   <a href="{{ route('seller.location.create') }}" class="btn btn-primary">Add Country</a> --}}
              
               <a  data-toggle="modal" data-target="#country" class=" d-inline dropdown  btn btn-primary text-white">Add Country</a>
             
            </div>
        
          <div class="table-responsive">
            <table class="table table-striped table-hover text-center table-borderless">
              <thead>
                <tr>
                  <th><input type="checkbox" class="checkAll"></th>

                  <th>{{ __('Name') }}</th>
                
                  <th>{{ __('Created at') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>

              <tbody>
                @foreach($posts as $row)
                <tr id="row{{ $row->id }}">
                  <td><input type="checkbox" name="ids[]" value="{{ $row->id }}"></td>

                  <td>{{ $row->name  }}</td>
                  
                  <td>{{ $row->created_at->diffforHumans()  }}</td>
                  <td>
                    <a href="{{ route('seller.location.edit',$row->id) }}" class="btn btn-primary btn-sm text-center"><i class="far fa-edit"></i></a>
                  </td>
                </tr>
                @endforeach
              </tbody>
             
           </table>
           {{ $posts->links('vendor.pagination.bootstrap-4') }}
         </div>
       </form>
     </div>
   </div>
 </div>
</div>

  <!-- Hidden country div -->
  
  <div  class="modal fade" id="country" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    
    <form class="basicform_with_resetss" action="{{ route('seller.location.store') }}" method="post"> @csrf
      <div class="modal-body">
        
      <div class="hidden_currency  m-auto ">

         <table class="table table-striped table-hover text-center table-borderless">
              

          

              <tbody>
               @foreach($json as  $k=>$v)  
               
                <tr id="">
                  <td><input type="checkbox" name="title[]" value="{{$v}}"></td>

                 <td>  <input type="text" class="form-control p-2" required="" name="name" value="{{$v}}">   </td>

                 
                </tr>
               @endforeach

               
               
              </tbody>

             
           </table>
 
  
  </div>
    
    
      </div>
    
    
      <div class="modal-footer">

       <button type="button" class="btn btn-danger p-2 close" data-dismiss="modal" aria-label="Close">
          Cancel
        </button>

          <input type="submit"class="btn btn-info" value="Done" /> 
       
      </div>
           </form>

    </div>
  </div>
</div>
  
  
  
  <!-- Hidden country div -->

@endsection

@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>

@endpush