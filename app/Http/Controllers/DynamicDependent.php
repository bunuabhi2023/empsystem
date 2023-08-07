<?php

namespace App\Http\Controllers;

use App\Country;
use App\states;
use App\company;
use App\location;
use App\sub_locations;
use App\department;
use App\designation;
use App\Employee;
use App\FinanceBankCash;
use App\JobCandidate;
use App\office_shift;
use App\SupportTicket;
use App\TaxType;
use Illuminate\Http\Request;

class DynamicDependent extends Controller {

	public function fetchStates(Request $request)
	{
		$value = $request->get('value');
		$data = [];
        $data = states::where('country_id', $value)->get();

		$output = '';
		for($i=0;$i<count($data);$i++){
			$output .= '<option value=' . $data[$i]['id'] . '>' . $data[$i]['name'] . '</option>';
		}

		return $output;
	}
	
	public function fetchCompany(Request $request)
	{
		$value = $request->get('value');
		
		$data = [];
        
        if(is_array($value) == 1){
		    foreach ($value as $row){
                $data = array_merge($data, $data = company::where('client_grp_id', $row)->get()->toArray());
		    }
		}else{
            $data = company::where('client_grp_id', $value)->get();
		}

		$output = '';
		for($i=0;$i<count($data);$i++){
			$output .= '<option value=' . $data[$i]['id'] . '>' . $data[$i]['company_name'] ." - ". $data[$i]['company_code'] . '</option>';
		}

		return $output;
	}
	
	public function fetchLocation(Request $request)
	{
		$value = $request->get('value');
		
		$data = [];
		
		if(is_array($value) == 1){
		    foreach ($value as $row){
                $data = array_merge($data, location::where('company_id', $row)->get()->toArray());
		    }
		}else{
            $data = location::where('company_id', $value)->get();
		}


		$output = '';
		for($i=0;$i<count($data);$i++){
			$output .= '<option value=' . $data[$i]['id'] . '>' . $data[$i]['location_name'] ." - ". $data[$i]['location_code'] . '</option>';
		}

		return $output;
	}
	
	public function fetchSubLocation(Request $request)
	{
		$value = $request->get('value');
		
		$data = [];
		
		if(is_array($value) == 1){
		    foreach ($value as $row){
                $data = array_merge($data, sub_locations::where('location_id', $row)->get()->toArray());
		    }
		}else{
            $data = sub_locations::where('location_id', $value)->get();
		}


		$output = '';
		for($i=0;$i<count($data);$i++){
			$output .= '<option value=' . $data[$i]['id'] . '>' . $data[$i]['name'] ." - ". $data[$i]['location_code'] . '</option>';
		}
		
		return $output;
	}
	
	public function getPayrollCycle(Request $request)
	{
		$value = $request->get('value');
		
        $data = sub_locations::where('id', $value)->first();

		$output = date("d-m-Y", strtotime($data->payroll_cycle_from)). " - ". date("d-m-Y", strtotime($data->payroll_cycle_to));
		return $output;
	}
	
	public function fetchDepartment(Request $request)
	{
		$value = $request->get('value');
		$dependent = $request->get('dependent');
		
	    $data = department::where('company_id', $value)
                ->orWhere(function ($query) {
                    $query->where('is_common', 1);
                })
                ->distinct()
                ->get();
	   // $data .= department::whereIs_common("1")->groupBy('department_name')->get();
	    
	    
		$output = '';
		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->$dependent . '</option>';
		}

		return $output;
	}

	public function fetchOfficeShifts(Request $request)
	{
		$value = $request->get('value');
		$dependent = $request->get('dependent');
		$data = office_shift::select("*")->groupBy('shift_name')->get();
		$output = '';
		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->$dependent . '</option>';
		}

		return $output;
	}

	public function fetchEmployee(Request $request)
	{
		$value = $request->get('value');
		$first_name = $request->get('first_name');
		$last_name = $request->get('last_name');
		$data = Employee::where('sub_location_id', $value)
                            ->where('is_active',1)
                            ->where('exit_date',NULL)
                            ->get();
		$output = '';
		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->staff_id . ' - ' . $row->$first_name . ' ' . $row->$last_name . '</option>';
		}

		return $output;
	}

	public function fetchEmployeeDepartment(Request $request)
	{
		$value = $request->get('value');
		$first_name = $request->get('first_name');
		$last_name = $request->get('last_name');
		$data = Employee::wheredepartment_id($value)
                    ->where('is_active',1)
                    ->where('exit_date',NULL)
                    ->get();
		$output = '';
		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->$first_name . ' ' . $row->$last_name . '</option>';
		}

		return $output;
	}

	public function fetchDesignationDepartment(Request $request)
	{
		$value = $request->get('value');
		$designation_name = $request->get('designation_name');
		$data = designation::select("*")->groupBy('designation_name')->get();
		$output = '';

		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->$designation_name . '</option>';
		}

		return $output;
	}

	public function fetchBalance(Request $request)
	{
		$value = $request->get('value');
		$dependent = $request->get('dependent');
		$data = FinanceBankCash::whereId($value)->pluck('account_balance')->first();
		$output = '';
		$output .= '<p> (Available Balance ' . $data  .  ' )</p>';
		return $output;
	}

	public function companyEmployee(SupportTicket $ticket){
		$value = $ticket->company_id;
		$data = Employee::whereCompany_id($value)
                ->where('is_active',1)
                ->where('exit_date',NULL)
                ->get();
		$output = '';
		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->first_name . ' ' . $row->last_name . '</option>';
		}

		return $output;
	}


	public function getTaxRate(Request $request)
	{
		$value = $request->get('value');
		$qty = $request->get('qty');
		$unit_price = $request->get('unit_price');

		$data = TaxType::findorFail($value);
		$total_cost = $qty * $unit_price;
		if($data->type=='fixed')
		{
			$tax = $data->rate;
			$sub_total = $total_cost + $tax;
		}
		else {
			$tax = (($total_cost)*($data->rate/100));
			$sub_total = $total_cost + $tax;
		}

		return response()->json(['data'=>$data,'sub_total'=>$sub_total,'tax'=>$tax,'total_cost'=>$total_cost]);

	}


	public function fetchCandidate(Request $request)
	{
		$value = $request->get('value');

		$data = JobCandidate::whereJob_id($value)->groupBy('full_name')->get();
		$output = '';
		foreach ($data as $row)
		{
			$output .= '<option value=' . $row->id . '>' . $row->full_name . '</option>';
		}

		return $output;
	}

}
