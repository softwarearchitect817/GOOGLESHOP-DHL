@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Inventory'])
@endsection
@section('content')
<div class="row">
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-body">
        @php
        $url=domain_info('full_domain');
        @endphp
        <div class="float-left">
         
           <a href="{{ route('seller.inventory.index') }}" class="btn  btn-outline-primary @if($status == '') active @endif">{{ __('Total') }} ({{ $total }})</a>
           <a href="?status=in" class="btn  btn-outline-success @if($status=='in') active @endif">{{ __('In Stock') }}({{ $in_stock }})</a>
           <a href="?status=out" class="btn  btn-outline-danger @if($status=='out') active @endif">{{ __('Stock Out') }} ({{ $out_stock }})</a>
          
        </div>

        <div class="float-right">
          <form>
            <div class="input-group mb-2">

              <input type="text" id="src" class="form-control " placeholder="#ABC-123" required="" name="src" autocomplete="off" value="{{ $src ?? '' }}">

              <div class="input-group-append">                                            
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
              </div>
            </div>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th class="am-title">{{ __('Product') }}</th>
                <th class="am-title">{{ __('SKU') }}</th>
                <th class="am-title">{{ __('Stock Manage') }}</th>
                <th class="am-title">{{ __('Status') }}</th>

                <th class="text-right">{{ __('Edit Quantity Available') }}</th>
              </tr>
            </thead>
            <tbody>

              @foreach($posts as $row)
              <form class="basicform" method="post" action="{{ route('seller.inventory.update',base64_encode($row->id)) }}">
                @csrf
                @method('PUT')
              <tr>
              <td>
                <div>
                  <a href="{{ route('seller.product.edit',$row->term_id) }}"  class="d-flex">
                    <img class="product-img" src="{{ asset($row->term->preview->media->url ?? 'uploads/default.png') }}" alt="">
                    <div class="product-flex">
                      <span>
                        {{ Str::limit($row->term->title,20) }}
                      </span>
                     
                    </div>
                  </a>
                </div>
              </td>
              <td>{{ $row->sku }}</td>
              <td>@if($row->stock_manage==1) <span class="badge badge-success">{{ __('Yes') }}</span> @elseif($row->stock_manage==0) <span class="badge badge-danger">{{ __('No') }}</span>  @endif</td>
              
              <td>
                <select class="form-control" name="stock_status">
                  <option  @if($row->stock_status== 1) selected="" @endif value="1">{{ __('In Stock') }}</option>
                  <option  @if($row->stock_status== 0) selected="" @endif value="0">{{ __('Out Of Stock') }}</option>
                </select>
                
                

              </td>
              
              <td>
                
                <div class="edit-product-quantity float-right">
                  @if($row->stock_manage==1)
                  <input type="number" name="stock_qty" class="form-control" min="0" required value="{{ $row->stock_qty }}" placeholder="Quantity">
                  @endif
                  <button class="btn btn-primary basicbtn float-right" type="submit">{{ __('Save') }}</button>
                  </form>
                </div>
                
              </td>
            </tr>
          </form>
            @endforeach


            </tbody>

         </table>
         {{ $posts->appends(array('status'=>$status))->links('vendor.pagination.bootstrap-4') }}
       </div>
       
     </div>
   </div>
 </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>

@endpush