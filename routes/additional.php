<?php
use Illuminate\Support\Facades\Route;

//Route::group(['as' =>'seller.','prefix'=>'seller','namespace'=>'Seller','middleware'=>['web','auth','seller']],function(){ 
  
	Route::get('seller/setting/domain','Seller\DomainController@index')->middleware(['web','auth','seller'])->name('seller.domain.index');
	Route::post('seller/domain/store','Seller\DomainController@store')->middleware(['web','auth','seller'])->name('seller.customdomain.store');
	Route::put('seller/domain/update/{id}','Seller\DomainController@update')->middleware(['web','auth','seller'])->name('seller.customdomain.update');
	

	Route::get('admin/custom-domain','Admin\CustomdomainController@index')->middleware(['web','auth','admin'])->name('admin.customdomain.index');
	Route::get('admin/custom-domain/{id}','Admin\CustomdomainController@show')->middleware(['web','auth','admin'])->name('admin.customdomain.show');
	Route::get('admin/custom-domain/edit/{id}','Admin\CustomdomainController@edit')->middleware(['web','auth','admin'])->name('admin.customdomain.edit');
	Route::post('admin/custom-domain-delete','Admin\CustomdomainController@destroy')->middleware(['web','auth','admin'])->name('admin.customdomain.destroy');
	Route::put('admin/custom-domain-update/{id}','Admin\CustomdomainController@update')->middleware(['web','auth','admin'])->name('admin.customdomain.update');

//});




?>