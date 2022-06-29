@extends('layouts.app')
@section('content')

@if(Auth::user()->status == 2 || Auth::user()->status == 3)
<div class="row mt-3">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <p>
          {{ __('Dear,') }} <b>{{ Auth::user()->name }}</b>{{ __(' Your Account Currently') }} <b class="text-danger"> @if(Auth::user()->status == 2) {{ __('Suspened') }} @elseif(Auth::user()->status == 3) {{ __('Pending') }} @endif </b> {{ __('Mode And Also Disabled All Functionality If You Are Not Complete Your Payment Please Complete Your Payment From') }} <a href="{{ route('merchant.plan') }}">{{ __('Here') }}</a> {{ __('Or Also Contact With Support Team') }}
        </p> 
      </div>
    </div>
  </div>
</div>
@endif
@php
$plan=user_limit();
@endphp
<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-stats">
        <div class="card-stats-title">{{ __('Order Statistics') }} -
          <div class="dropdown d-inline">
            <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month" id="orders-month">{{ Date('F') }}</a>
            <ul class="dropdown-menu dropdown-menu-sm">
              <li class="dropdown-title">{{ __('Select Month') }}</li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='January') active @endif" data-month="January" >{{ __('January') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='February') active @endif" data-month="February" >{{ __('February') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='March') active @endif" data-month="March" >{{ __('March') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='April') active @endif" data-month="April" >{{ __('April') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='May') active @endif" data-month="May" >{{ __('May') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='June') active @endif" data-month="June" >{{ __('June') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='July') active @endif" data-month="July" >{{ __('July') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='August') active @endif" data-month="August" >{{ __('August') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='September') active @endif" data-month="September" >{{ __('September') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='October') active @endif" data-month="October" >{{ __('October') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='November') active @endif" data-month="November" >{{ __('November') }}</a></li>
              <li><a href="#" class="dropdown-item month @if(Date('F')=='December') active @endif" data-month="December" >{{ __('December') }}</a></li>
            </ul>
          </div>
        </div>
        <div class="card-stats-items">
          <div class="card-stats-item">
            <div class="card-stats-item-count" id="pending_order"></div>
            <div class="card-stats-item-label">{{ __('Pending') }}</div>
          </div>

          <div class="card-stats-item">
            <div class="card-stats-item-count" id="completed_order"></div>
            <div class="card-stats-item-label">{{ __('Completed') }}</div>
          </div>

          <div class="card-stats-item">
            <div class="card-stats-item-count" id="shipping_order"></div>
            <div class="card-stats-item-label">{{ __('Processing') }}</div>
          </div>
        </div>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-archive"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>{{ __('Total Orders') }}</h4>
        </div>
        <div class="card-body" id="total_order">
          
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-chart">
        <canvas id="sales_of_earnings_chart" height="80"></canvas>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>{{ __('Total Sales Of Earnings') }} - {{ date('Y') }}</h4>
        </div>
        <div class="card-body" id="sales_of_earnings">
            <img src="{{ asset('uploads/loader.gif') }}">
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-chart">
        <canvas id="total-sales-chart" height="80"></canvas>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-shopping-bag"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>{{ __('Total Sales') }} - {{ date('Y') }}</h4>
        </div>
        <div class="card-body" id="total_sales">
            <img src="{{ asset('uploads/loader.gif') }}" class="loads">
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  @php
   $date= \Carbon\Carbon::now()->addDays(7)->format('Y-m-d');
  @endphp
  @if(Auth::user()->user_domain->will_expire <= $date)
  <div class="col-sm-12">
      <div class="col-md-12">
        <div class="alert alert-warning">
            {{  __('Your subscription is ending in')  }} {{ \Carbon\Carbon::parse(Auth::user()->user_domain->will_expire)->diffForHumans() }}
            {{ __('Please') }} <ins><a class="text" href="{{  Auth::user()->user_domain->is_trial == 1 ? url('/seller/settings/plan') :  url('/seller/plan-renew') }}">{{ Auth::user()->user_domain->is_trial == 1 ? __('Enroll in a plan!') : __('Renew the plan') }}</a></ins> 
        </div>
    </div>
  </div>
  @endif
  
    <div class="col-12 col-xl-9">

        <div class="row">
            <div class="col-12">

                <div class="card mt-4">
                    <div class="card-header">

                        <h4 class="card-header-title">{{ __('Earnings performance') }} <img src="{{ asset('uploads/loader.gif') }}" height="20" id="earning_performance"></h4>
                        <div class="card-header-action">
                            
                        <select class="form-control" id="perfomace">
                            <option value="7">{{ __('Last 7 Days') }}</option>
                            <option value="15">{{ __('Last 15 Days') }}</option>
                            <option value="30">{{ __('Last 30 Days') }}</option>
                            <option value="365">{{ __('Last 365 Days') }}</option>
                        </select>

                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" height="158"></canvas> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6">

            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2">{{ __('Today\'s Total Sales') }}</h6>

                            <span class="h2 mb-0" id="today_total_sales"><img src="{{ asset('uploads/loader.gif') }}"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">

            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2">{{ __('Today\'s Orders') }}</h6>

                            <span class="h2 mb-0" id="today_order"><img src="{{ asset('uploads/loader.gif') }}"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
      
        <div class="col-6">

            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2">{{ __('Yesterday') }}</h6>

                            <span class="h2 mb-0" id="yesterday_total_sales"><img src="{{ asset('uploads/loader.gif') }}"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-6">

            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2">{{ __('7 days') }}</h6>

                            <span class="h2 mb-0" id="last_seven_days_total_sales"><img src="{{ asset('uploads/loader.gif') }}"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-6">

            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2">{{ __('This Month') }}</h6>

                            <span class="h2 mb-0" id="monthly_total_sales"><img src="{{ asset('uploads/loader.gif') }}"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-6">

            <div class="card">
               <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">

                        <h6 class="text-uppercase text-muted mb-2">{{ __('Last Month') }}</h6>

                        <span class="h2 mb-0" id="last_month_total_sales"><img src="{{ asset('uploads/loader.gif') }}"></span>
                    </div>
                </div> 
            </div>
        </div>
    </div>

 @if(filter_var($plan['qr_code']) == true && filter_var($plan['google_analytics']) == true)
    <div class="col-12">
        <div class="card">
      <div class="card-header">
        <h4>{{ __('Site Analytics') }}</h4>
        <div class="card-header-action">
          <select class="form-control" id="days"> 
            <option value="7">{{ __('Last 7 Days') }}</option>
            <option value="15">{{ __('Last 15 Days') }}</option>
            <option value="30">{{ __('Last 30 Days') }}</option>
            <option value="180">{{ __('Last 180 Days') }}</option>
            <option value="365">{{ __('Last 365 Days') }}</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <canvas id="google_analytics" height="80"></canvas>
        <div class="statistic-details mt-sm-4">
          <div class="statistic-details-item">

            <div class="detail-value" id="total_visitors"></div>
            <div class="detail-name">{{ __('Total Vistors') }}</div>
          </div>
          <div class="statistic-details-item">

            <div class="detail-value" id="total_page_views"></div>
            <div class="detail-name">{{ __('Total Page Views') }}</div>
          </div>

          <div class="statistic-details-item">

            <div class="detail-value" id="new_vistors"></div>
            <div class="detail-name">{{ __('New Visitor') }}</div>
          </div>

          <div class="statistic-details-item">

            <div class="detail-value" id="returning_visitor"></div>
            <div class="detail-name">{{ __('Returning Visitor') }}</div>
          </div>

        </div>
      </div>
    </div>
    </div>
@endif
</div>
</div>
<div class="col-12 col-xl-3">
    <div class="row">
        <div class="col-12">
            <div class="card mt-4">
                <div class="card-header">

                    <h4 class="card-header-title plan_name" ></h4>

                    <span class="badge badge-soft-secondary plan_expire"></span>
                    <img src="{{ asset('uploads/loader.gif') }}"  class="plan_load">
                </div>
                <div class="card-header">

                    <h4 class="card-header-title">{{ __('Storage usage') }}</h4>

                    <span class="badge badge-soft-secondary" id="storage_used"><img src="{{ asset('uploads/loader.gif') }}"  class="storrage_used"></span>
                </div>
                <div class="card-header">

                    <h4 class="card-header-title">{{ __('Products') }}</h4>

                    <span class="badge badge-soft-secondary posts_used"><img src="{{ asset('uploads/loader.gif') }}"  class="product_used"></span>
                </div>
                 <div class="card-header">

                    <h4 class="card-header-title">{{ __('Pages') }}</h4>

                    <span class="badge badge-soft-secondary pages"> <img src="{{ asset('uploads/loader.gif') }}"  class="product_used"></span>
                </div>
               
            </div>

            @if(Auth::user()->status == 1)
           
            @if(filter_var($plan['qr_code']) == true)
              <div class="card">
                <div class="card-header">

                    <h4 class="card-header-title">{{ __('Scan your site') }}</h4>
                </div>
                <div class="card-body qr-code-main">

                    {!! QrCode::size(200)->generate(url('/')) !!}
                </div>
                <div class="card-footer">
                  <button class="btn btn-primary col-12" onclick="downloadPng()" type="button">{{ __('Download') }}</button>
                 </div> 
            </div>
            @endif
            @endif
            <div class="card">
                <div class="card-header">

                    <h4 class="card-header-title">{{ __('Products Limit') }} <span><span class="text-danger posts_created"></span>/<span class="product_capacity"> </span></span></h4>
                </div>
                <div class="card-body">
                    <div class="sparkline-pie-product d-inline"></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <h4 class="card-header-title">{{ __('Storage Uses') }} <span><span class="text-danger storage_used"></span>/<span class="storage_capacity"> </span></span></h4>
                </div>
                <div class="card-body">

                     <div class="sparkline-pie-storage d-inline" height="50"></div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@if(filter_var($plan['google_analytics']) == true)
<div class="row">
  <div class="col-lg-12 col-md-12 col-12 col-sm-12">
     <div class="col-12">
        <div class="card">
      <div class="card-header">
        <h4>{{ __('Site Analytics') }}</h4>
        <div class="card-header-action">
          <select class="form-control" id="days"> 
            <option value="7">{{ __('Last 7 Days') }}</option>
            <option value="15">{{ __('Last 15 Days') }}</option>
            <option value="30">{{ __('Last 30 Days') }}</option>
            <option value="180">{{ __('Last 180 Days') }}</option>
            <option value="365">{{ __('Last 365 Days') }}</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <canvas id="google_analytics" height="80"></canvas>
        <div class="statistic-details mt-sm-4">
          <div class="statistic-details-item">

            <div class="detail-value" id="total_visitors"></div>
            <div class="detail-name">{{ __('Total Vistors') }}</div>
          </div>
          <div class="statistic-details-item">

            <div class="detail-value" id="total_page_views"></div>
            <div class="detail-name">{{ __('Total Page Views') }}</div>
          </div>

          <div class="statistic-details-item">

            <div class="detail-value" id="new_vistors"></div>
            <div class="detail-name">{{ __('New Visitor') }}</div>
          </div>

          <div class="statistic-details-item">

            <div class="detail-value" id="returning_visitor"></div>
            <div class="detail-name">{{ __('Returning Visitor') }}</div>
          </div>

        </div>
      </div>
    </div>
    </div>

    <div class="row">
      <div class="col-lg-6 col-md-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Referral URL') }}</h4>
          </div>
          <div class="card-body refs" id="refs" >



          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Top Browser') }}</h4>
          </div>
          <div class="card-body">
            <div class="row" id="browsers"></div>
          </div>
        </div>

      </div>

      <div class="col-lg-6 col-md-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Top Most Visit Pages') }}</h4>
          </div>
          <div class="card-body tmvp" id="table-body">
           
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endif
<input type="hidden" id="base_url" value="{{ url('/') }}">
<input type="hidden" id="site_url" value="{{ url('/') }}">
<input type="hidden" id="dashboard_static" value="{{ route('seller.dashboard.static') }}">
<input type="hidden" id="dashboard_perfomance" value="{{ url('/seller/dashboard/perfomance') }}">
<input type="hidden" id="dashboard_order_statics" value="{{ url('/seller/dashboard/order_statics') }}">
<input type="hidden" id="gif_url" value="{{ asset('uploads/loader.gif') }}">
<input type="hidden" id="month" value="{{ date('F') }}">
@endsection
@push('js')
<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('assets/seller/dashboard.js') }}"></script>
@if(Auth::user()->status == 1)
           
@if(filter_var($plan['qr_code']) == true)
<script type="text/javascript">
"use strict";

  function downloadPng() {
    var img = new Image();
    img.onload = function() {
        var canvas = document.createElement("canvas");
        canvas.width = img.naturalWidth;
        canvas.height = img.naturalHeight;
        var ctxt = canvas.getContext("2d");
        ctxt.fillStyle = "#fff";
        ctxt.fillRect(0, 0, canvas.width, canvas.height);
        ctxt.drawImage(img, 0, 0);
        var a = document.createElement("a");
        a.href = canvas.toDataURL("image/png");
        a.download = "qrcode.png"
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    var innerSvg = document.querySelector(".qr-code-main svg");
    var svgText = (new XMLSerializer()).serializeToString(innerSvg);
    img.src = "data:image/svg+xml;utf8," + encodeURIComponent(svgText);
} {
    // "mode"=> "full",
    // "isActive": false
}
</script>
@endif
@endif
@endpush
