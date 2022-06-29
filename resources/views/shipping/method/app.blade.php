@extends('layouts.app')
@push('style')
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@section('head')
@include('layouts.partials.headersection',['title'=>'Create'])
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Calculate Shipping Rates') }}</h4>
      </div>
      <div class="card-body">
        <div class="form-group row mb-4 w-75 m-auto">
          <div class="col-sm-4">
            <div class="form-group form-check">
              <input onclick="activateShipmentMethod('ups')" type="checkbox" class="form-check-input" id="exampleCheck1"
                @if(is_array(json_decode($user->shipment_methods)) && in_array("ups",
              json_decode($user->shipment_methods))) checked @endif>
              <label class="form-check-label" for="exampleCheck1">UPS</label>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group form-check">
              <input onclick="activateShipmentMethod('usps')" type="checkbox" class="form-check-input"
                id="exampleCheck1" @if(is_array(json_decode($user->shipment_methods)) && in_array("usps",
              json_decode($user->shipment_methods))) checked @endif>
              <label class="form-check-label" for="exampleCheck1">USPS</label>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group form-check">
              <input onclick="activateShipmentMethod('dhl')" type="checkbox" class="form-check-input" id="exampleCheck1"
                @if(is_array(json_decode($user->shipment_methods)) && in_array("dhl",
              json_decode($user->shipment_methods))) checked @endif>
              <label class="form-check-label" for="exampleCheck1">DHL</label>
            </div>
          </div>
        </div>
        <form class="basicform_with_reset " action="{{ route('seller.shipping.store') }}" method="post">
          @csrf
          <div class="form-group row mb-4 w-75 m-auto">
            @if(is_array(json_decode($user->shipment_methods)) && in_array("ups", json_decode($user->shipment_methods)))
            <div class="col-sm-4">
              <a data-toggle="modal" data-target="#ups" class="w-100 p-3 text-white btn btn-primary">UPS </a>
            </div>
            @endif


            @if(is_array(json_decode($user->shipment_methods)) && in_array("usps",
            json_decode($user->shipment_methods)))
            <div class="col-sm-4">
              <a data-toggle="modal" data-target="#usps" class="w-100 p-3 text-white btn btn-primary">USPS </a>
            </div>
            @endif



            @if(is_array(json_decode($user->shipment_methods)) && in_array("dhl", json_decode($user->shipment_methods)))
            <div class="col-sm-4">
              <a data-toggle="modal" data-target="#dhl" class="w-100 p-3 text-white btn btn-primary">DHL </a>
            </div>
          </div>
          @endif
        </form>


      </div>
    </div>
  </div>
</div>



<div id="accordion" class="col-12">
  <div class="card">
    <div class="card-header" id="dHLAccount">
      <h5 class="mb-0">
        <button class="btn btn-link" data-toggle="collapse" data-target="#configureDHLAccount" aria-expanded="true"
          aria-controls="configureDHLAccount">
          Configure DHL Shipping Account
        </button>
      </h5>
    </div>

    <div id="configureDHLAccount" class="collapse show" aria-labelledby="dHLAccount" data-parent="#accordion">
      <div class="card-body">
        <form class="needs-validation" novalidate>
          <div class="form-group row">
            <label for="customRadioInline1" class="col-sm-2 col-form-label text-right">Test Mode</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline1" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="customRadioInline1">Yes</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline2" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="customRadioInline2">No</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="siteID" class="col-sm-2 col-form-label text-right">Site ID</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="siteID" value="CIMGBTest" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password" value="CIMGBTest" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="accountNumber" class="col-sm-2 col-form-label text-right">Account Number</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="accountNumber" value="130000279" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="status" class="col-sm-2 col-form-label text-right">Status</label>
            <div class="col-sm-10">
              <select class="custom-select" id="status">
                <option selected value="">Enabled</option>
                <option>Disabled</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="sortOrder" class="col-sm-2 col-form-label text-right">Sort Order</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="sortOrder" value="2">
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
          'use strict';
          window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
              form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              }, false);
            });
          }, false);
        })();
        </script>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" id="dHLAddress">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#configureDHLAddress"
          aria-expanded="false" aria-controls="configureDHLAddress">
          Configure DHL Shipping Address
        </button>
      </h5>
    </div>

    <div id="configureDHLAddress" class="collapse" aria-labelledby="dHLAddress" data-parent="#accordion">
      <div class="card-body">
        <form class="needs-validation" novalidate>
          <div class="form-group row">
            <label for="shipperName" class="col-sm-2 col-form-label text-right">Shipper Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="shipperName" value="Demo Man" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="companyName" class="col-sm-2 col-form-label text-right">Company Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="companyName" value="Test Company" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="phoneNumber" class="col-sm-2 col-form-label text-right">Phone Number</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="phoneNumber" value="1234567890" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="emailAddress" class="col-sm-2 col-form-label text-right">Email Address</label>
            <div class="col-sm-10">
              <input type="email" class="form-control" id="emailAddress" value="test@test.com" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="addressLine1" class="col-sm-2 col-form-label text-right">Address Line 1</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="addressLine1" value="3 Alexander House" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="addressLine2" class="col-sm-2 col-form-label text-right">Address Line 2</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="addressLine2">
            </div>
          </div>
          <div class="form-group row">
            <label for="city" class="col-sm-2 col-form-label text-right">City</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="city" value="London" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="state" class="col-sm-2 col-form-label text-right">State</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="state" value="LO" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="countryCode" class="col-sm-2 col-form-label text-right">Country Code</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="countryCode" value="GB" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="postCode" class="col-sm-2 col-form-label text-right">Post Code</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="postCode" value="WC1E 7HU" required>
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
          (function() {
            'use strict';
            window.addEventListener('load', function() {
              // Fetch all the forms we want to apply custom Bootstrap validation styles to
              var forms = document.getElementsByClassName('needs-validation');
              // Loop over them and prevent submission
              var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                  if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                  }
                  form.classList.add('was-validated');
                }, false);
              });
            }, false);
          })();
        </script>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" id="dHLServices">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#configureDHLServices"
          aria-expanded="false" aria-controls="configureDHLServices">
          Configure DHL Shipping rates & Services
        </button>
      </h5>
    </div>

    <div id="configureDHLServices" class="collapse" aria-labelledby="dHLServices" data-parent="#accordion">
      <div class="card-body">
        <form class="needs-validation" novalidate>
          <div class="form-group row">
            <label for="RTRate1" class="col-sm-2 col-form-label text-right">Enable Real Time Rates</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="RTRate1" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="RTRate1">Yes</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="RTRate2" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="RTRate2">No</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="insurance1" class="col-sm-2 col-form-label text-right">Enable Insurance</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="insurance1" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="insurance1">Yes</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="insurance2" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="insurance2">No</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="deliveryTime1" class="col-sm-2 col-form-label text-right">Display Delivery Time ?</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="deliveryTime1" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="deliveryTime1">Yes</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="deliveryTime2" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="deliveryTime2">No</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="rateType" class="col-sm-2 col-form-label text-right">Rate Type</label>
            <div class="col-sm-10">
              <select class="custom-select" id="rateType">
                <option selected disabled value="">List Rate</option>
                <option>Rate 1</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="services" class="col-sm-2 col-form-label text-right">Services</label>
            <div class="col-sm-10" id="services" style="max-height: 100px; overflow:auto">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll" value="selectAll">
                <label class="form-check-label" for="defaultCheck1">
                  Select / Deselect All
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                <label class="form-check-label" for="defaultCheck1">
                  [1] DOMESTIC EXPRESS 12:00
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                <label class="form-check-label" for="defaultCheck2">
                  [2] B2C
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck3">
                <label class="form-check-label" for="defaultCheck3">
                  [3] B2C
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck4">
                <label class="form-check-label" for="defaultCheck4">
                  [4] JETLINE
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck5">
                <label class="form-check-label" for="defaultCheck5">
                  [5] SPRINTLINE
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck6">
                <label class="form-check-label" for="defaultCheck6">
                  [6] SPRINTLINE
                </label>
              </div>
              {{-- <script>
                $('#selectAll').click(function() {
                  if (this.checked) {
                    $(':checkbox').each(function() {
                       this.checked = true;
                    });
                  } else {
                    $(':checkbox').each(function() {
                      this.checked = false;
                    });
                  }
                });
              </script> --}}
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
          (function() {
            'use strict';
            window.addEventListener('load', function() {
              // Fetch all the forms we want to apply custom Bootstrap validation styles to
              var forms = document.getElementsByClassName('needs-validation');
              // Loop over them and prevent submission
              var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                  if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                  }
                  form.classList.add('was-validated');
                }, false);
              });
            }, false);
          })();
        </script>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" id="dHLPackage">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#configureDHLPackage"
          aria-expanded="false" aria-controls="configureDHLPackage">
          Configure DHL Shipping Package
        </button>
      </h5>
    </div>

    <div id="configureDHLPackage" class="collapse" aria-labelledby="dHLPackage" data-parent="#accordion">
      <div class="card-body">
        <form class="needs-validation" novalidate>
          <div class="form-group row">
            <label for="customRadioInline1" class="col-sm-2 col-form-label text-right">Weight/Dimension Unit</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline1" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="customRadioInline1">LBS-IN</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline2" name="customRadioInline" class="custom-control-input">
                <label class="custom-control-label" for="customRadioInline2">KG-CM</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="packingType" class="col-sm-2 col-form-label text-right">Choose packing type</label>
            <div class="col-sm-10">
              <select class="custom-select" id="packingType">
                <option selected disabled value="">Default: Pack items individually</option>
                <option>Pack items individually</option>
              </select>
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
            (function() {
              'use strict';
              window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                  form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                      event.preventDefault();
                      event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                  }, false);
                });
              }, false);
            })();
        </script>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" id="packItems">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#configurePackItems"
          aria-expanded="false" aria-controls="configurePackItems">
          Configure Pack items individually (if choosed)
        </button>
      </h5>
    </div>

    <div id="configurePackItems" class="collapse" aria-labelledby="packItems" data-parent="#accordion">
      <div class="card-body">
        <form class="needs-validation" novalidate>
          <div class="form-group row">
            <label for="packingType2" class="col-sm-2 col-form-label text-right">Choose packing type</label>
            <div class="col-sm-10">
              <select class="custom-select" id="packingType2">
                <option selected value="">DHL Box</option>
                <option>DHL</option>
              </select>
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
              (function() {
                'use strict';
                window.addEventListener('load', function() {
                  // Fetch all the forms we want to apply custom Bootstrap validation styles to
                  var forms = document.getElementsByClassName('needs-validation');
                  // Loop over them and prevent submission
                  var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                      if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                      }
                      form.classList.add('was-validated');
                    }, false);
                  });
                }, false);
              })();
        </script>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" id="weight">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#configureWeight"
          aria-expanded="false" aria-controls="configureWeight">
          Configure Weight based (if choosed)
        </button>
      </h5>
    </div>

    <div id="configureWeight" class="collapse" aria-labelledby="weight" data-parent="#accordion">
      <div class="card-body">
        <form class="needs-validation" novalidate>
          <div class="form-group row">
            <label for="max" class="col-sm-2 col-form-label text-right">Maximum Weight/Packing</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="max" value="Maximum Weight/Packing">
            </div>
          </div>
          <div class="form-group row">
            <label for="packingType" class="col-sm-2 col-form-label text-right">Choose packing type</label>
            <div class="col-sm-10">
              <select class="custom-select" id="packingType">
                <option selected value="">Pack heavier items first</option>
                <option>Pack heavier items first</option>
              </select>
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
                (function() {
                  'use strict';
                  window.addEventListener('load', function() {
                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    var forms = document.getElementsByClassName('needs-validation');
                    // Loop over them and prevent submission
                    var validation = Array.prototype.filter.call(forms, function(form) {
                      form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                          event.preventDefault();
                          event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                      }, false);
                    });
                  }, false);
                })();
        </script>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" id="headingTwo">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
          aria-controls="collapseTwo">
          Collapsible Group Item #2
        </button>
      </h5>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
      <div class="card-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon
        officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf
        moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim
        keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur
        butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably
        haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingThree">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
          aria-controls="collapseThree">
          Collapsible Group Item #3
        </button>
      </h5>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
      <div class="card-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon
        officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf
        moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim
        keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur
        butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably
        haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
</div>












































@if( Session::has('ratesUSPS') && Session::get('ratesUSPS') == 'ups' && isset($array_data) )
<div class="showRates">
  <h4 class="bg-dark">UPS RATES</h4>

  <table class="table table-striped table-hover text-center table-borderless bg-light ">
    <tbody>


      <tr id="">
        <td>
          <div class="row">
            <div class="col-sm-3 font-weight-bold">
              <p>Weight: </p>
            </div>
            <div class="col-sm-9">
              <p class="text-center bg-info"></p>{{round($array_data['BillingWeight']['Weight']/2.20462,1)}} KG
            </div>
          </div>
        </td>
      </tr>

      <tr id="">
        <td>
          <div class="row">
            <div class="col-sm-3 font-weight-bold">
              <p>Transportation Charges: </p>
            </div>
            <div class="col-sm-9">
              <p class="text-center bg-info"></p>{{$array_data['TransportationCharges']['MonetaryValue']}}$
            </div>
          </div>
        </td>
      </tr>

      <tr id="">
        <td>
          <div class="row">
            <div class="col-sm-3 font-weight-bold">
              <p>Guaranteed Days To Delivery: </p>
            </div>
            <div class="col-sm-9">
              <p class="text-center bg-info"></p>{{$array_data['GuaranteedDaysToDelivery']}} Days
            </div>
          </div>
        </td>
      </tr>






    </tbody>
  </table>
  @endif



  @if(Session::has('ratesUSPS') && Session::get('ratesUSPS') == 'usps' && isset($array_data))
  <div class="showRates">
    <div class="showRates">
      <h4 class="bg-dark">USPS RATES</h4>

      <table class="table table-striped table-hover text-center table-borderless bg-light ">
        <tbody>


          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>Origin Zip: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['ZipOrigination']}}
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>Destination Zip: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['ZipOrigination']}}
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>(Pounds,Ounces): </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>({{$array_data['Pounds']}},{{$array_data['Ounces']}})
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>Service Name: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>@php $service=explode('&',$array_data['Postage']['MailService']);
                  @endphp
                  {{$service[0]}}
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>Rate: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['Postage']['Rate']}}$
                </div>
              </div>
            </td>
          </tr>

        </tbody>
      </table>
      <hr>
      <h4 class="bg-light">Special Services</h4>



      <table class="table table-striped table-hover text-center table-borderless">
        <tbody>

          @foreach($specialService as $key => $value)
          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>Service Name: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>@php $service=explode('&',$value['ServiceName']); @endphp
                  {{$service[0]}}
                </div>
              </div>

            </td>

            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">Rate: </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p> {{$value['Price']}}$
                </div>
              </div>
            </td>
          </tr>
          @endforeach


        </tbody>
      </table>
    </div>
    @endif



    @if( Session::has('ratesUSPS') && Session::get('ratesUSPS') == 'dhl' && isset($array_data) )
    <div class="showRates">
      <h4 class="bg-dark">DHL RATES</h4>

      <table class="table table-striped table-hover text-center table-borderless bg-light ">
        <tbody>


          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>PickupDate: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['PickupDate']}}
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>DeliveryDate: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['DeliveryDate']}}
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>WeightCharge: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['WeightCharge']}}$
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>ShippingCharge: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['ShippingCharge']}}$
                </div>
              </div>
            </td>
          </tr>

          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>DimensionalWeight: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$array_data['DimensionalWeight']}}KG
                </div>
              </div>
            </td>
          </tr>




        </tbody>
      </table>
      <hr>
      <h4 class="bg-light">Special Services</h4>



      <table class="table table-striped table-hover text-center table-borderless">
        <tbody>

          @foreach($specialService as $key => $value)
          <tr id="">
            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">
                  <p>Service Name: </p>
                </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p>{{$value['GlobalServiceName']}}
                </div>
              </div>

            </td>

            <td>
              <div class="row">
                <div class="col-sm-3 font-weight-bold">Rate: </div>
                <div class="col-sm-9">
                  <p class="text-center bg-info"></p> {{$value['ChargeValue']}}$
                </div>
              </div>
            </td>
          </tr>
          @endforeach


        </tbody>
      </table>
    </div>
    @endif




    <!-- API User input FORM Starts -->


    <!-- Hidden UPS div -->

    <div class="modal fade w-100" id="ups" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form class="basicform_with_resetss" action="{{url('seller/ups')}}"> @csrf
            <div class="modal-body">

              <div class="hidden_currency  m-auto ">

                <table class="table table-striped table-hover text-center table-borderless">
                  <tbody>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">UPS Access Key: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="accessKey" value="">
                          </div>
                        </div>
                      </td>
                    </tr>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">UPS User Id: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="userId" value="">
                          </div>
                        </div>
                      </td>
                    </tr>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">UPS Password: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="password" value="">
                          </div>
                        </div>
                        <p class="border border-dark"> </p>
                      </td>
                    </tr>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Origin City: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="fromCity" value="">
                          </div>
                        </div>
                      </td>
                    </tr>


                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Origin Country Code: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="fromCC" value="">
                          </div>
                        </div>
                      </td>
                    </tr>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Origin Zip: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="fromPC" value="">
                          </div>
                        </div>
                      </td>
                    </tr>



                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Destination City: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="toCity" value="">
                          </div>
                        </div>
                      </td>
                    </tr>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Destination Country Code: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="toCC" value="">
                          </div>
                        </div>
                      </td>
                    </tr>

                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Destination Zip: </div>
                          <div class="col-sm-9">
                            <input type="text" class="form-control p-2" required="" name="toPC" value="">
                          </div>
                        </div>
                      </td>
                    </tr>


                    <tr id="">
                      <td>
                        <div class="row">
                          <div class="col-sm-3 font-weight-bold">Item Weight(KG): </div>
                          <div class="col-sm-9">
                            <input type="number" step="any" class="form-control p-2" required="" name="pounds" value="">
                          </div>
                        </div>
                      </td>
                    </tr>




                  </tbody>


                </table>
                <input type="submit" class="btn btn-info m-auto d-block" value="Calculate" />
          </form>



        </div>


      </div>


      <div class="modal-footer">

        <button type="button" class="btn btn-danger p-2 close" data-dismiss="modal" aria-label="Close">
          Cancel
        </button>

      </div>

    </div>
  </div>
</div>

<!-- Hidden UPS div -->



<!-- Hidden USPS div -->

<div class="modal fade" id="usps" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="basicform_with_resetss" action="{{url('seller/usps')}}"> @csrf
        <div class="modal-body">

          <div class="hidden_currency  m-auto ">

            <table class="table table-striped table-hover text-center table-borderless">
              <tbody>


                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">USPS User Id: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="username" value="">
                      </div>
                    </div>
                    <p class="border border-dark"> </p>
                  </td>
                </tr>

                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Origin Zip: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="origin" value="">
                      </div>
                    </div>
                  </td>
                </tr>

                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Destination Zip: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="dest" value="">
                      </div>
                    </div>
                  </td>
                </tr>


                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Item Pounds: </div>
                      <div class="col-sm-9">
                        <input type="number" class="form-control p-2" required="" name="pounds" value="">
                      </div>
                    </div>
                  </td>
                </tr>


                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Ounces: </div>
                      <div class="col-sm-9">
                        <input type="number" class="form-control p-2" required="" name="ounces" value="">
                      </div>
                    </div>
                  </td>
                </tr>

              </tbody>


            </table>
            <input type="submit" class="btn btn-info m-auto d-block" value="Calculate" />
      </form>



    </div>


  </div>


  <div class="modal-footer">

    <button type="button" class="btn btn-danger p-2 close" data-dismiss="modal" aria-label="Close">
      Cancel
    </button>

  </div>

</div>
</div>
</div>

<!-- Hidden USPS div -->



<!-- Hidden DHL div -->

<div class="modal fade" id="dhl" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="basicform_with_resetss" action="{{url('seller/dhl')}}"> @csrf
        <div class="modal-body">

          <div class="hidden_currency  m-auto ">

            <table class="table table-striped table-hover text-center table-borderless">
              <tbody>
                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Test Mode: </div>
                      <div class="col-sm-9 d-flex">
                        <input type="radio" id="html" name="mode" value="yes">
                          <label for="yes">Yes</label><br>
                          <input type="radio" id="css" name="mode" value="no">
                          <label for="no">No</label><br>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Site ID: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="siteID" value="">
                      </div>
                    </div>
                  </td>
                </tr>
                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Password: </div>
                      <div class="col-sm-9">
                        <input type="password" class="form-control p-2" required="" name="password" value="">
                      </div>
                    </div>
                  </td>
                </tr>
                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Account Number: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="accNumber" value="">
                      </div>
                    </div>
                  </td>
                </tr>
                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Origin Country Code: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="fromCC" value="">
                      </div>
                    </div>
                  </td>
                </tr>

                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Origin Zip: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="fromPC" value="">
                      </div>
                    </div>
                  </td>
                </tr>


                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Destination Country Code: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="toCC" value="">
                      </div>
                    </div>
                  </td>
                </tr>

                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Destination Zip: </div>
                      <div class="col-sm-9">
                        <input type="text" class="form-control p-2" required="" name="toPC" value="">
                      </div>
                    </div>
                  </td>
                </tr>


                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Item Weight(KG): </div>
                      <div class="col-sm-9">
                        <input type="number" class="form-control p-2" required="" name="pounds" value="">
                      </div>
                    </div>
                  </td>
                </tr>


                <tr id="">
                  <td>
                    <div class="row">
                      <div class="col-sm-3 font-weight-bold">Shipping Date: </div>
                      <div class="col-sm-9">
                        <input type="date" min="@php echo date('Y-m-d'); @endphp" class="form-control p-2" required=""
                          name="date">
                      </div>
                    </div>
                  </td>
                </tr>

              </tbody>


            </table>
            <input type="submit" class="btn btn-info m-auto d-block" value="Calculate" />
      </form>



    </div>


  </div>


  <div class="modal-footer">

    <button type="button" class="btn btn-danger p-2 close" data-dismiss="modal" aria-label="Close">
      Cancel
    </button>

  </div>

</div>
</div>
</div>

<!-- Hidden DHL div -->


@endsection

@push('js')
<script>
  function activateShipmentMethod(method){
    var data = {
      method : method
    }
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    $.ajax({
        type: "POST",
        url: "shippings/activate",
        data: data,
        success: function (response) {
          window.location.reload();
        }
    });

  }
</script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/form.js') }}"></script>
@endpush