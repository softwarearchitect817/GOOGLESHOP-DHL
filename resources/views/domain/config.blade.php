@extends('layouts.app')
@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-colorpicker.min.css') }}">
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Domain Settings'])
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4>{{ __('Current Domain') }}</h4>
                            
                        </div>
                        <div class="card-body">
                           
                            <p>{{ url('/') }}</p>
                            
                            
                        </div>
                    </div>
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4>{{ __('Requested domain') }}</h4>
                            <div class="card-header-action">
                                <a href="#" class="btn btn-success" data-toggle="modal" data-target="#createModal"><i class="{{ !empty($request) ? 'fa fa-edit' : 'fas fa-plus-circle' }}"></i></a>
                                
                                 
                            </div>
                        </div>
                        <div class="card-body">
                            @if(!empty($request))
                            <p> {{ $request->domain ?? '' }}</p>
                            @if($request->status == 1)
                            <span class="badge badge-success">{{ __('Connected') }} </span>
                            @elseif($request->status == 2)
                            <span class="badge badge-warning">{{ __('Pending') }}   </span>
                            @else
                            <span class="badge badge-danger">{{ __('Disabled') }}   </span>
                            @endif
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@if($info['custom_domain'] == true)
@if(empty($request->domain))
<div class="modal fade" tabindex="-1" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" class="basicform_with_reload"  action="{{ route('seller.customdomain.store') }}">
                @csrf
                <div class="modal-card card">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="customdomain">{{ __('Add existing domain') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                    <div class="card-body">
                        <div id="form-errors"></div>
                        <div class="form-group">
                            <label>{{ __('Custom domain') }}</label>
                            <input class="form-control" autofocus="" name="domain" type="text" placeholder="example.com" required="">
                            <small class="form-text text-muted">{{ __('Enter the domain you want to connect.') }}</small>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Configure your DNS records') }}</label>
                            <small class="form-text text-muted">{{ $dns->dns_configure_instruction ?? '' }}</small>
                            <table class="table table-nowrap card-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Record') }}</th>
                                        <th>{{ __('Value') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('A') }}</td>
                                        <td>&nbsp;</td>
                                        <td>{{ env('SERVER_IP') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('CNAME') }}</td>
                                        <td>{{ __('www') }}</td>
                                        <td>{{ env('CNAME_DOMAIN') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <small class="form-text text-muted">{{ $dns->support_instruction ?? '' }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary basicbtn" ><span class="ladda-label">{{ __('Connect') }}</span><span class="ladda-spinner"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

@else
<div class="modal fade" tabindex="-1" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" class="basicform_with_reload" accept-charset="UTF-8" action="{{ route('seller.customdomain.update',$request->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-card card">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="customdomain">{{ __('Add existing domain') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                    <div class="card-body">
                        <div id="form-errors"></div>
                        <div class="form-group">
                            <label>{{ __('Custom domain') }}</label>
                            <input class="form-control" autofocus="" name="domain" type="text" placeholder="example.com" required="" value="{{ $request->domain ?? '' }}">
                            <small class="form-text text-muted">{{ __('Enter the domain you want to connect.') }}</small>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Configure your DNS records') }}</label>
                            <small class="form-text text-muted">{{ $dns->dns_configure_instruction ?? '' }}</small>
                            <table class="table table-nowrap card-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Record') }}</th>
                                        <th>{{ __('Value') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('A') }}</td>
                                        <td>&nbsp;</td>
                                        <td>{{ env('SERVER_IP') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('CNAME') }}</td>
                                        <td>{{ __('www') }}</td>
                                        <td>{{ env('CNAME_DOMAIN') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <small class="form-text text-muted">{{ $dns->support_instruction ?? '' }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary basicbtn" data-style="expand-left" data-loading-text="Verify..."><span class="ladda-label">{{ __('Connect') }}</span><span class="ladda-spinner"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif
@endif

@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
@endpush