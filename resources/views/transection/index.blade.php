@extends('layouts.app')
@section('content')
<div class="">
    <div class="row justify-content-center">
        <div class="col-12">
           
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Transactions') }}</h4>
                    <form class="card-header-form">
                        <div class="input-group">
                            <input type="text" name="src" value="{{ $request->src ?? '' }}" class="form-control" required=""  placeholder="transactions id..." />
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form> 
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap card-table text-center">
                            <thead>
                                <tr>
                                    <th class="text-left" >{{ __('Order No') }}</th>
                                    <th class="text-left" >{{ __('Transaction Id') }}</th>
                                    <th >{{ __('Last Update') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment') }}</th>
                                    <th>{{ __('Method') }}</th>
                                </tr>
                            </thead>
                            <tbody class="list font-size-base rowlink" data-link="row">
                                @foreach($orders as $key => $row)
                                @php $currency = json_decode($row->currency); @endphp
                                <tr>
                                    <td class="text-left">
                                        <a href="{{ route('seller.order.show',$row->id) }}">{{ $row->order_no }}</a>
                                    </td>
                                     <td class="text-left">
                                       <a href="#" data-toggle="modal" class="edit" data-target="#editModal" data-oid="{{ $row->id }}" data-td="{{ $row->id }}"  data-mode="{{ $row->getway->id ?? '' }}" data-transaction="{{ $row->transaction_id }}">{{ $row->transaction_id }}</a>
                                    </td>
                                    <td>
                                    	<a href="{{ route('seller.order.show',$row->id) }}">{{ $row->updated_at->format('d-F-Y') }}</a>
                                    	<br>
                                    	<small>{{ $row->updated_at->diffForHumans() }}</small>
                                    </td>
                                    <td>@if($row->customer_id != null)<a href="{{ route('seller.customer.show',$row->customer_id) }}">{{ $row->customer->name }}</a>@else {{ __('Guest Transaction') }} @endif</td>
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
                                 	<td>{{ $row->getway->name ?? '' }}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    @if(count($request->all()) > 0)
                    {{ $orders->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
                    @else
                    {{ $orders->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
            </div>
        </div>
    </div>  
</div>




<form method="post" action="{{ route('seller.transection.store') }}" class="basicform">
	@csrf
<div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">{{ __('Edit') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
				<input type="hidden" name="o_id" id="o_id" value="">
				<input type="hidden" name="t_id" id="t_id"  value="">
				<div class="form-group">
					<label>{{ __('File Name') }}</label>
					<select class="form-control" name="method" id="method">
						@foreach($getways as $row)
						<option value="{{ $row->method->id }}">{{ $row->method->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label>{{ __('Transection Id') }}</label>
					<input type="text" name="transection_id" class="form-control" required="" id="transection_id">
				</div>
				
			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
				<button type="submit" class="btn btn-primary basicbtn">{{ __('Save') }}</button>
			</div>
		</div>
	</div>
</div>
</form>
@endsection
@push('js')
<script type="text/javascript" src="{{ asset('assets/js/form.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/seller/transaction/index.js') }}"></script>
@endpush

