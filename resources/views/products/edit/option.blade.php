@extends('layouts.app')
@section('head')
@include('layouts.partials.headersection',['title'=>'Product Options'])
@endsection
@section('content')

<div class="row">
	<div class="col-lg-12">      
		
		<div class="card">
			<div class="card-body">

				<div class="row">
					<div class="col-sm-3">
						<ul class="nav nav-pills flex-column">
							<li class="nav-item">
								<a class="nav-link" href="{{ route('seller.product.edit',$info->id) }}"><i class="fas fa-cogs"></i> {{ __('Item') }}</a>
                            </li>
                            
							<li class="nav-item">
								<a class="nav-link " href="{{ url('seller/product/'.$info->id.'/price') }}"><i class="fas fa-money-bill-alt"></i> {{ __('Price') }}</a>
                            </li>
                            <li class="nav-item">
								<a class="nav-link active" href="{{ url('seller/product/'.$info->id.'/option') }}"><i class="fas fa-tags"></i> {{ __('Options') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/varient') }}"><i class="fas fa-expand-arrows-alt"></i> {{ __('Variants') }}</a>
							</li>
							
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/image') }}"><i class="far fa-images"></i> {{ __('Images') }}</a>
							</li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('seller/product/'.$info->id.'/inventory') }}"><i class="fa fa-cubes"></i> {{ __('Inventory') }}</a>
                            </li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/files') }}"><i class="fas fa-file"></i> {{ __('Files') }}</a>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/seo') }}"><i class="fas fa-chart-line"></i> {{ __('SEO') }}</a>
							</li>
							<li class="nav-item">
									<a class="nav-link" href="{{ url('seller/product/'.$info->id.'/express-checkout') }}"><i class="fas fa-cart-arrow-down"></i> {{ __('Express checkout') }}</a>
								</li>
						</ul>
					</div>
					<div class="col-sm-9">
                  <form class="basicform" method="post" action="{{ route('seller.product.option_update',$info->id) }}">
                     @csrf
                            <div class="row">
                             
                                <div class="col-12 col-md-12 col-lg-12">
                                    <button type="button" data-toggle="modal" data-target="#add_option" class="btn btn-primary float-right">{{ __('Add New Option') }}</button>	
						
                                </div>   
                                <div class="col-12 col-md-12 col-lg-12 mt-3">
                                    <div id="accordion">
                                      @foreach ($info->options as $key=> $row)
                                          
                                     
                                        <div class="accordion option{{ $row->id }}">
                                          <div class="accordion-header h-50" role="button" data-toggle="collapse" data-target="#panel-body-{{ $key }}">
                                             <div class="float-left">
                                                <h6><span id="option_name{{ $row->id }}">{{ $row->name }}</span> @if($row->is_required == 1) <span class="text-danger">*</span> @endif</h6>
                                             </div>
                                             <div class="float-right">                                               
                                                <a class="btn btn-success btn-sm text-white row_edit" data-toggle="modal" data-target="#editform"  data-selecttype="{{ $row->select_type }}" data-name-translations="{{ json_encode($row->getTranslations('name'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?? '{}' }}" data-required="{{ $row->is_required }}"  data-id="{{ $row->id }}"><i class="fa fa-edit"></i></a>
                                                <a class="btn btn-danger btn-sm text-white option_delete" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></a> 
                                             </div>                                           
                                          </div>
                                          <div class="accordion-body collapse" id="panel-body-{{ $key }}" data-parent="#accordion">
                                            <div class="panel-body">                                              
                                                <div class="option-values clearfix" id="option-2-values">
                                                   <div class="option-select ">
                                                      <div class="table-responsive">
                                                         <table class="options table table-bordered table-striped">
                                                            <thead>
                                                               <tr>
                                                                 
                                                                  <th>Label</th>
                                                                  <th>Price</th>
                                                                  <th>Price Type</th>
                                                                  <th></th>
                                                               </tr>
                                                            </thead>
                                                            <tbody >
                                                              
                                                               @foreach ($row->childrenCategories ?? [] as $item)
                                                               <tr class="option{{ $item->id }}" >
                                                                 
                                                                  <td>                                                                     
                                                                     <input type="text" name="options[{{ $row->id }}][values][{{ $item->id }}][label]"  class="form-control" value="{{ $item->name }}">
                                                                  </td>
                                                                  <td>
                                                                     <input type="number" name="options[{{ $row->id }}][values][{{ $item->id }}][price]" class="form-control" value="{{ $item->amount }}" step="any" min="0">
                                                                  </td>
                                                                  <td>
                                                                     <select name="options[{{ $row->id }}][values][{{ $item->id }}][price_type]" class="form-control custom-select-black">
                                                                        <option value="1" @if($item->amount_type == 1) selected="" @endif>
                                                                           {{ __('Fixed') }}
                                                                        </option>
                                                                        <option value="0" @if($item->amount_type == 0) selected="" @endif>
                                                                           {{ __('Percent') }}
                                                                        </option>
                                                                     </select>
                                                                  </td>
                                                                  <td class="text-center">
                                                                     <button type="button" class="btn btn-white delete-row bg-white option_delete text-danger" data-id="{{ $item->id }}">
                                                                     <i class="fa fa-trash"></i>
                                                                     </button>
                                                                  </td>
                                                               </tr>
                                                               @endforeach
                                                            </tbody>
                                                         </table>
                                                      </div>
                                                   
						
                                                      <button  type="button" data-toggle="modal" data-target="#new_row_modal" class="btn btn-primary add_new_row" data-id="{{ $row->id }}">
                                                          {{ __('Add New Row') }}
                                                      </button>
                                                   </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        @endforeach
                                       
                                      </div>
                                      @if(count($info->options) > 0)
                                      <button type="submit" class="btn btn-primary basicbtn">{{ __('Save Changes') }}</button>	
                                      @endif
                                 </div>
                                
                                
                              
                              </div>                      
                           </form>
						

						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>


<div class="modal fade" id="add_option" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('seller.product.store_group',$info->id) }}" class="basicform_with_reload" method="post">
           
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Option') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
         <div class="form-group">
            <label for="name">{{ __('Option Name') }}</label>
            <div class="row">
                <div class="col-sm-8 col-md-8">
              <input type="text" class="form-control" id="name">
            </div>
            <div class="col-sm-4 col-md-4">
              <select class="form-control" id="lang_select">
                @foreach($langlist ?? [] as $key => $row)
                    <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                @endforeach
              </select>
            </div>
            <input type="hidden" id="name_translations" name="name_translations" value=""/>
            </div>
            
        </div>
         <div class="form-group">
            <label >{{ __('Select Type') }}</label>
           
            <select name="select_type" class="form-control">
               <option value="1">{{ __('Multiple Select') }}</option>
               <option selected value="0">{{ __('Single Select') }}</option>
            </select>
        </div>
         <label for="is_required"><input type="checkbox" name="is_required" value="1" id="is_required"> {{ __('Required') }}</label>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary basicbtn">{{ __('Save') }}</button>
        </div>
      </div>
    </form>
    </div>
  </div>


<!-- Modal -->
<div class="modal fade" id="new_row_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
     <div class="modal-content">
        <form action="{{ route('seller.product.add_row') }}" class="basicform_with_reload" method="post">
         @csrf
      
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Row') }}</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
       </div>
       <div class="modal-body">
         <div class="form-group">
            <label for="add_row">{{ __('Row Name') }}</label>
            <input type="text" id="add_row" class="form-control" name="name" required>
            <input type="hidden" id="row_id" name="row_id">
         </div>
         
         <div class="form-group">
            <label for="price">{{ __('Price') }}</label>
            <input type="number" step="any"  id="price" class="form-control" name="price" required>
         </div>
         <div class="form-group">
            <label for="price_type">{{ __('Price Type') }}</label>
            <select name="amount_type" id="price_type" class="form-control">
               <option value="1">{{ __('Fixed') }}</option>
               <option value="0">{{ __('Percentage') }}</option>
            </select>
         </div>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">{{ __('Close') }}</button>
         <button type="submit" class="btn btn-primary basicbtn">{{ __('Save') }}</button>
       </div>
      </form>
     </div>
   </div>
 </div>  

 
<!-- Modal -->
<div class="modal fade" id="editform" tabindex="-1" aria-labelledby="editform" aria-hidden="true">
   <div class="modal-dialog">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="editform">{{ __('Edit Option') }}</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <form action="{{ route('seller.product.row_update') }}" class="basicform row_update_form" >
         @csrf
       <div class="modal-body">
         <div class="form-group">
            <label for="name">{{ __('Option Name') }}</label>
            <div class="row">
                <div class="col-sm-8 col-md-8">
              <input type="text" class="form-control" id="edit_name">
            </div>
            <div class="col-sm-4 col-md-4">
              <select class="form-control" id="edit_lang_select">
                @foreach($langlist ?? [] as $key => $row)
                    <option value="{{ $row }}" @if($key==$local) selected="" @endif>{{ $key }}</option>
                @endforeach
              </select>
            </div>
            <input type="hidden" id="edit_name_translations" name="edit_name_translations" value=""/>
            </div>
        </div>
         
         <div class="form-group">
             <label for="name">{{ __('Select Type') }}</label>
           
             <select id="edit_select" name="select_type" class="form-control">
                <option value="1">{{ __('Multiple Select') }}</option>
                <option value="0">{{ __('Single Select') }}</option>
             </select>
         </div>
         <input type="hidden" id="edit_id" name="id">
         <label for="edit_required"><input id="edit_required" type="checkbox" name="is_required" value="1" > {{ __('Required') }}</label>
          
        </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">{{ __('Close') }}</button>
         <button type="submit" class="btn btn-primary basicbtn" >{{ __('Save') }}</button>
       </div>
      </form>
     </div>
   </div>
 </div>

<form class="basicform delete_from" action="{{ route('seller.product.option_delete') }}"  method="post">
@csrf
<input type="hidden" name="id" id="option_id">
</form> 
@endsection
@push('js')
<script src="{{ asset('assets/js/form.js') }}"></script>
<script src="{{ asset('assets/js/product_option.js') }}"></script>
<script>
var name_translations = {};
var local='{{ $local }}';

$('#name').on('change', function(e) {
    name_translations[$('#lang_select').val()] = $(this).val();
    $('#name_translations').val(JSON.stringify(name_translations));
});

$('#lang_select').on('change', function(e) {
    var name = name_translations[$('#lang_select').val()] || "";
    $('#name').val(name);
});

var edit_name_translations = "";

 $('.row_edit').on('click',function(){
    edit_name_translations=$(this).data('name-translations');
    $('#edit_name_translations').val(JSON.stringify(edit_name_translations));

    var is_required=$(this).data('required');
    var id=$(this).data('id');
    var select_type=$(this).data('selecttype');

    
    var local='{{ $local }}';
    
    $('#edit_name').val(edit_name_translations[$('#edit_lang_select').val()] || "");
    
    if(is_required == 1){
       $('#edit_required').attr('checked','');
    }
    else{
       $('#edit_required').removeAttr('checked')
    }
    $('#edit_id').val(id);
    $('#edit_select').val(select_type);
    
    $('#edit_name').on('change', function(e) {
        edit_name_translations[$('#edit_lang_select').val()] = $(this).val();
        $('#edit_name_translations').val(JSON.stringify(edit_name_translations));
    });

    $('#edit_lang_select').on('change', function(e) {
        var name = edit_name_translations[$('#edit_lang_select').val()] || "";
        $('#edit_name').val(name);
    });
 });
</script>
@endpush