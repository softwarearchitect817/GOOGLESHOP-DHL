<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Domain;
use App\Models\Userplan;
class ReportController extends Controller
{
	public function index(Request $request)
	{
		if (!Auth()->user()->can('report.view')) {
			abort(401);
		}
		if ($request->start) {
			$start = date("Y-m-d",strtotime($request->start));
			$end = date("Y-m-d",strtotime($request->end));

			$order_count=Userplan::whereBetween('created_at',[$start,$end])->count();
			
			$order_expired=Userplan::whereBetween('created_at',[$start,$end])->where('status',3)->count();
			$order_sum=Userplan::whereBetween('created_at',[$start,$end])->whereHas('category',function($q){
				return $q->where('status',1);
			})->sum('amount');
			$order_tax=Userplan::whereBetween('created_at',[$start,$end])->whereHas('category',function($q){
				return $q->where('status',1);
			})->sum('tax');
			$posts=Userplan::whereBetween('created_at',[$start,$end])->with('plan_info','category')->latest()->paginate(40);
		}
		else{
		$order_count=Userplan::count();
		
		$order_expired=Userplan::where('status',3)->count();
		$order_sum=Userplan::whereHas('category',function($q){
			return $q->where('status',1);
		})->sum('amount');
		$order_tax=Userplan::whereHas('category',function($q){
			return $q->where('status',1);
		})->sum('tax');
		$posts=Userplan::with('plan_info','category')->latest()->paginate(40);	
		}
		

		$start = $start ?? '';
		$end = $end ?? '';

		return view('admin.report.index',compact('start','end','order_count','order_sum','order_expired','posts','request','order_tax'));
	}
}
