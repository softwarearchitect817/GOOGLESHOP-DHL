@extends('layouts.app')

@section('content')
<div class="">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-10">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link @if(url()->current() == route('seller.orders.status','all')) active @endif" href="{{ route('seller.orders.status','all') }}">{{ __('All') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($type == 'pending') active @endif" href="{{ route('seller.orders.status','pending') }}">{{ __('Awaiting processing') }} <span class="badge badge-secondary">{{ $pendings }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link  @if($type == 'processing') active @endif" href="{{ route('seller.orders.status','processing') }}">{{ __('Processing') }} <span class="badge badge-secondary">{{ $processing }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($type == 'pickup') active @endif" href="{{ route('seller.orders.status','pickup') }}">{{ __('Ready for pickup') }} <span class="badge badge-secondary">{{ $pickup }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($type == 'completed') active @endif" href="{{ route('seller.orders.status','completed') }}">{{ __('Completed') }} <span class="badge badge-secondary">{{ $completed }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($type == 'canceled') active @endif" href="{{ route('seller.orders.status','canceled') }}">{{ __('Cancelled') }} <span class="badge badge-secondary">{{ $canceled }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($type == 'archived') active @endif" href="{{ route('seller.orders.status','archived') }}">{{ __('Archived') }} <span class="badge badge-secondary">{{ $archived }}</span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-2">
                        <a href="{{ route('seller.order.create') }}" class="btn btn-primary float-right">{{ __('Create order') }}</a>
                    </div>
                </div>
            </div>
            @if(Session::has('error'))
            <div class="col-sm-12">
              <div class="col-md-12">
                <div class="alert alert-warning">
                    {{ Session::get('error') }}
                </div>
            </div>
            </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Orders') }}</h4>


                    <form class="card-header-form">
                        <div class="input-group">
                            <input type="text" name="src" value="{{ $request->src ?? '' }}" class="form-control" required=""  placeholder="ABC-123" />
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-sm btn-primary  ml-1" type="button" data-toggle="modal" data-target="#searchmodal">
                        <i class="fe fe-sliders mr-1"></i> {{ __('Filter') }} <span class="badge badge-primary ml-1 d-none">0</span>
                    </button>

                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('seller.orders.method') }}" class="basicform">
                        @csrf
                  
                    <div class="float-left">
                        @if(count($orders) > 0)
                        <div class="input-group mb-1">
                            <select class="form-control selectric" name="method">
                                <option disabled selected="">{{ __('Select Fulfillment') }}</option>
                                <option value="pending">{{ __('Awaiting processing') }}</option>
                                <option value="processing">{{ __('Processing') }}</option>
                                <option value="ready-for-pickup">{{ __('Ready for pickup') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="archived">{{ __('Archived') }}</option>
                                <option value="canceled">{{ __('Cancel') }}</option>
                               

                                @if($type== 'canceled')
                                <option value="delete" class="text-danger">{{ __('Delete Permanently') }}</option>
                                
                                @endif
                            </select>
                            <div class="input-group-append">                                            
                                <button class="btn btn-primary basicbtn" type="submit">{{ __('Submit') }}</button>
                            </div>
                        </div>
                        @endif
                    </div>  
                   <div class="float-right">
                    @if(count($request->all()) > 0)
                    {{ $orders->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
                    @else
                    {{ $orders->links('vendor.pagination.bootstrap-4') }}
                    @endif
                   </div>
               


                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap card-table text-center">
                            <thead>
                                <tr>
                                    <th class="text-left" ><div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input checkAll" id="selectAll">
                                    <label class="custom-control-label checkAll" for="selectAll"></label>
                                    </div></th>
                                    <th class="text-left" >{{ __('Order') }}</th>
                                    <th >{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th class="text-right">{{ __('Order total') }}</th>
                                    <th>{{ __('Payment') }}</th>
                                    <th>{{ __('Fulfillment') }}</th>
                                    <th class="text-right">{{ __('Item(s)') }}</th>
                                    <th class="text-right">{{ __('Invoice') }}</th>
                                     <th class="text-right">{{ __('Labels') }}</th>
                                </tr>
                            </thead>
                            <tbody class="list font-size-base rowlink" data-link="row">
                                @foreach($orders as $key => $row)
                                @php
                                    $currency = json_decode($row->currency);
                                @endphp
                                <tr>
                                    <td  class="text-left">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $row->id }}" value="{{ $row->id }}">
                                            <label class="custom-control-label" for="customCheck{{ $row->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <a href="{{ route('seller.order.show',$row->id) }}">{{ $row->order_no }}</a>
                                    </td>
                                    <td><a href="{{ route('seller.order.show',$row->id) }}">{{ $row->created_at->format('d-F-Y') }}</a></td>
                                    <td>@if($row->customer_id !== null)<a href="{{ route('seller.customer.show',$row->customer_id) }}">{{ $row->customer->name }}</a> @else {{ __('Guest User') }} @endif</td>
                                    <td >{{ amount_format_order($row->total, $currency->currency_icon) }}</td>
                                    <td>
                                        @if($row->payment_status==2)
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>

                                        @elseif($row->payment_status==1)
                                        <span class="badge badge-success">{{ __('Complete') }}</span>

                                        @elseif($row->payment_status==0)
                                        <span class="badge badge-danger">{{ __('Cancel') }}</span> 
                                        @elseif($row->payment_status==3)
                                        <span class="badge badge-danger">{{ __('Incomplete') }}</span> 

                                        @endif
                                    </td>
                                    <td>
                                        @if($row->status=='pending')
                                        <span class="badge badge-warning">{{ __('Awaiting processing') }}</span>
                                        
                                        @elseif($row->status=='processing')
                                        <span class="badge badge-primary">{{ __('Processing') }}</span>

                                        @elseif($row->status=='ready-for-pickup')
                                        <span class="badge badge-info">{{ __('Ready for pickup') }}</span>

                                        @elseif($row->status=='completed')
                                        <span class="badge badge-success">{{ __('Completed') }}</span>

                                        @elseif($row->status=='archived')
                                        <span class="badge badge-warning">{{ __('Archived') }}</span>
                                        @elseif($row->status=='canceled')
                                        <span class="badge badge-danger">{{ __('Canceled') }}</span>
                                        
                                        @else
                                        <span class="badge badge-info">{{ $row->status }}</span>

                                        @endif
                                    </td>

                                    <td>{{ $row->order_items_count }}</td>
                                    <td><a href="{{ route('seller.invoice',$row->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-file-invoice"></i></a></td>

                                    <td class="my-3 ">  <a href="{{route('dhl_label')}}" class="py-0 btn badge badge-info my-1">{{ __('Create Shipping Labels') }}</a>
                                      <a href="{{route('dhl_label')}}" class="btn badge badge-info py-0 ">{{ __('Return Labels') }}</a>
                                  </td>

                                    

                                </tr>
                                @endforeach
                            </tbody>
                              <tfoot>
                                <tr>
                                    <th class="text-left" ><div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input checkAll" id="selectAll">
                                    <label class="custom-control-label checkAll" for="selectAll"></label>
                                    </div></th>
                                    <th class="text-left" >{{ __('Order') }}</th>
                                    <th >{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th class="text-right">{{ __('Order total') }}</th>
                                    <th>{{ __('Payment') }}</th>
                                    <th>{{ __('Fulfillment') }}</th>
                                    <th class="text-right">{{ __('Item(s)') }}</th>
                                    <th class="text-right">{{ __('Invoice') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>


    
</div>


<!-- Modal -->
<div class="modal fade" id="searchmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="card-header-title">{{ __('Filters') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
            <div class="modal-body">
                <div class="form-group row mb-4">
                    <label class="col-sm-7">{{ __('Payment Status') }}</label>
                    <div class="col-sm-5">
                        <select class="form-control" name="payment_status" id="payment_status">
                            <option value="2">{{ __('Pending') }}</option>
                            <option value="1" >{{ __('Complete') }}</option>
                            <option value="3" >{{ __('Incomplete') }}</option>
                            <option value="cancel" >{{ __('Cancel') }}</option>
                           
                        </select>
                    </div>
                </div>

                <hr />

                <div class="form-group row mb-4">
                    <label class="col-sm-7">{{ __('Fulfillment status') }}</label>
                    <div class="col-sm-5">
                        <select class="form-control" name="status" id="status" >
                            <option value="pending" >{{ __('pending') }}</option>
                            <option value="processing" >{{ __('processing') }}</option>
                            <option value="ready-for-pickup" >{{ __('ready-for-pickup') }}</option>
                            <option value="completed" >{{ __('completed') }}</option>
                            <option value="archived" >{{ __('archived') }}</option>
                            <option value="canceled" >{{ __('canceled') }}</option>
                        </select>
                    </div>
                </div>

                <hr />

                <div class="form-group row mb-4">
                    <label class="col-sm-3">{{ __('Starting date') }}</label>
                    <div class="col-sm-9">
                        <input type="date" name="start" class="form-control" value="{{ $request->start }}" />
                    </div>
                </div>

                <hr />

                <div class="form-group row mb-4">
                    <label class="col-sm-3">{{ __('Ending date') }}</label>
                    <div class="col-sm-9">
                        <input type="date" name="end" class="form-control" value="{{ $request->end }}" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ url()->current() }}" class="btn btn-secondary">{{ __('Clear Filter') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
            </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="payment" value="{{ $request->payment_status ?? '' }}">
<input type="hidden" id="order_status" value="{{ $request->status ?? '' }}">
@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/js/order_index.js') }}"></script>
@endpush
