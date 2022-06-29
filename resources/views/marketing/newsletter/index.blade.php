@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'News Letter'])
@endsection
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-primary">
			<div class="card-icon bg-primary">
				<i class="fas fa-shopping-bag"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Newsletter subscribers') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($subscribers) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-danger">
			<div class="card-icon bg-danger">
				<i class="fas fa-ban"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Unsubscribers') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($unsubscribers) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-warning">
			<div class="card-icon bg-warning">
				<i class="fas fa-history"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Emails sent this month') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($sent_emails) }}
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
		<div class="card card-statistic-1 card-success">
			<div class="card-icon bg-success">
				<i class="far fa-check-square"></i>
			</div>
			<div class="card-wrap">
				<div class="card-header">
					<h4>{{ __('Emails credits available') }}</h4>
				</div>
				<div class="card-body">
					{{ number_format($available) }}
				</div>
			</div>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-body">
	        <div class="float-left">
	            <!-- Button trigger modal -->
	            <a href="#" class="btn btn-info float-left" data-toggle="modal" data-target="#buy_credits">{{ __('Buy Credits') }}</a>
	        </div>
			<div class="float-right">
			    	<a href="{{ route('seller.send-email') }}" class="btn btn-primary float-right">{{ __('Send Email To User') }}</a>
					<a href="{{ route('seller.newsletter') }}" class="btn btn-primary float-right">{{ __('Create News Letter') }}</a>
					
				</div>
		<br><br>
	
		<form method="post" action="{{ route('seller.customers.destroys') }}" class="basicform">
			@csrf
		
			<div class="table-responsive custom-table">
				<table class="table">
					<thead>
						<tr>
						    <th class="am-title">{{ __('#') }}</th>
							<th class="am-title">{{ __('Title') }}</th>
							<th class="am-date">{{ __('Action') }}</th>
						</tr>
					</thead>
					<tbody>
					     @foreach ($newsletters as $newsletter)
                                    <tr id="row{{ $newsletter->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td><img src="{{ asset($row->preview->content ?? 'uploads/default.png') }}"
                                                height="50"></td> --}}
                                       
                                        <td>{{ $newsletter->title }}</td>
                                        <td>
                                            <a href="{{ route('seller.edit-newsletter', $newsletter->id)}}" class="btn btn-warning btn-sm text-center" ><i class="fas fa-edit"></i></a>
                                            <a href="{{ route('seller.delete.newsletter_this', $newsletter->id)}}" class="btn btn-primary btn-sm text-center"><i
                                                    class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
					
					</tbody>

				
				</table>
				
			</form>

			<span>{{ __('Note') }}: <b class="text-danger">{{ __('For Better Performance Remove Unusual Users') }}</b></span>
		</div>
	</div>
</div>

@php $currency_info = currency_info() @endphp
<input type="hidden" id="default_currency_icon" value="{{$currency_info['currency_default']->currency_icon}}">
<input type="hidden" id="currency_position" value="{{$currency_info['currency_position']}}">
<!-- Large modal -->

<div class="modal fade" id="buy_credits" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalScrollableTitle">Purchase Email Credits</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="get" action="{{route('seller.make_payment_credit')}}" class="">
          <div class="modal-body">
            @foreach($credits as $credit)
                <div class="option-wrapper clearfix">
                    <label class="float-left">
                      <input type="radio" class="option-input radio" name="email_plan" value="{{$credit->num}}"/>
                      {{number_format($credit->num).'  Email credits'}}
                      @if($credit->original_price != $credit->discount_price)
                        @if($credit->discount_price == 0) <span class="badge badge-pill badge-primary">SAVE 100%</span>
                        @else
                            <span class="badge badge-pill badge-primary"> SAVE {{number_format($credit->discount_price/$credit->original_price * 100).'%'}}</span>
                        @endif
                      @endif
                    </label>
                    <div class="pricing-details float-right">
                        <del>{{amount_format($credit->original_price)}}</del>
                        {{amount_format($credit->discount_price)}}
                    </div>
                </div>
            @endforeach
            <div class="option-wrapper-text"><h6>OR ENTER CREDIT AMOUNT MANUALLY:</h6></div>
            <div class="option-wrapper clearfix">
                <label class="float-left">
                  <input type="radio" class="option-input radio" id="email_plan" name="email_plan" value="0" />
                  <input type="hidden" id="step_price" value={{$step->discount_price}}>
                  <input type="number" id="credits_num" step="{{$step->num}}" min="0" value="0">
                </label>
                <div class="pricing-details float-right" id="calc_price_by_step">
                  
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-info" id="buy_credits_btn" disabled>Buy Credits</button>
          </div>
      </form>
    </div>
  </div>
</div>

@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/js/success.js') }}"></script>
@endpush