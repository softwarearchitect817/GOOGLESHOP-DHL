@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Variation'])
@endsection
@section('content')
 <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Edit Variation') }}</h4>
      </div>
      <div class="card-body">
        <form class="basicform" action="{{ route('seller.attribute-term.update',$info->id) }}" method="post">
          @csrf
          @method('PUT')
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" >{{ __('Name') }}</label>
          <div class="col-sm-12 col-md-7">
            <input type="text" class="form-control" required="" name="title" value="{{ $info->name }}">
          </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Select Attribute') }}</label>
          <div class="col-sm-12 col-md-7">
            <select class="form-control selectric" name="parent_attribute" id="p_id">
               @foreach(App\Category::where('user_id',Auth::id())->where('type','parent_attribute')->get() as $row)
               <option value="{{ $row->id }}" @if($info->p_id==$row->id) selected="" @endif>{{ $row->name }}</option>
               @endforeach
            </select>
          </div>
        </div>
       
       
      
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Featured') }}</label>
          <div class="col-sm-12 col-md-7">
            <select class="form-control selectric" name="featured">
              <option value="1" @if($info->featured==1) selected="" @endif>{{ __('Yes') }}</option>
              <option value="0"  @if($info->featured==0) selected="" @endif>{{ __('No') }}</option>
             
            </select>
          </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
          <div class="col-sm-12 col-md-7">
            <button class="btn btn-primary basicbtn" type="submit">{{ __('Save') }}</button>
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