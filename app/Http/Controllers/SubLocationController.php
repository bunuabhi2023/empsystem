<?php

namespace App\Http\Controllers;

use App\Employee;
// use App\location;
use App\sub_locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;

use Spatie\Permission\Models\Role;


class SubLocationController extends Controller
{
	public function index()
	{
		$countries = \DB::table('countries')->select('id','name')->get();
		$locations = \DB::table('locations')->select('id','location_name')->get();
		$employees = Employee::select('id','first_name','last_name')->where('is_active',1)->where('exit_date',NULL)->get();
		
		if(request()->ajax())
		{
			return datatables()->of(sub_locations::select('*'))
			    ->addColumn('sr_no', function ($data){
				    return $data->id;
				})
			    ->addColumn('state', function ($data) {
                    return $data->stateS->name;
                })
				->addColumn('country', function($data){
				    return $data->countryS->name;
				})
				->addColumn('location_id', function($data){
				    return $data->location->location_name;
				})
				->addColumn('location_id_code', function($data){
				    return $data->location->location_code;
				})
				->addColumn('company_name', function($data){
				    return $data->location->company->company_name;
				})
				->addColumn('company_code', function($data){
				    return $data->location->company->company_code;
				})
				->addColumn('client_group', function($data){
				    return $data->location->company->client_groups->name;
				})
				->addColumn('client_group_code', function($data){
				    return $data->location->company->client_groups->code;
				})
				->addColumn('agreement_doc', function($data){
				    return '<a href="'.url($data->agreement).'">View Document</a>';
				})
				->addColumn('action', function($data){
					$button = '<div style="display: flex;gap: 10px">';
					if (auth()->user()->can('view-sub-location'))
					{
						$button .= '<button type="button" name="view" id="' . $data->id . '" class="view btn btn-success btn-sm"><i class="fa fa-eye"></i></button>';
					}
					if (auth()->user()->can('edit-sub-location'))
					{
						$button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
					}
					if (auth()->user()->can('del-sub-location'))
					{
						$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
					}
					$button .= '</div>';
					return $button;
				})
				->rawColumns(['action', 'agreement_doc'])
				->make(true);
		}

		return view('organization.sub-location.index',compact('countries','employees', 'locations'));
	}


	public function store(Request $request)
	{

		$logged_user = auth()->user();

		if ($logged_user->can('add-sub-location'))
		{

			$validator = Validator::make($request->only('sub_location_name', 'location_code', 'location_id', 'nature_business', 'address1', 'address2', 'city',
				'state', 'country', 'pincode', 'pan', 'tan', 'gstin', 'acc_no', 'ifsc', 'agr_valid_from', 'agr_valid_till', 'agreement', 'payment_term', 'special_remark', 'payroll_cycle_from', 'payroll_cycle_to', 'invoicing_timeline', 'payment_receivable', 'payment_payable', 'scope_revenue', 'service_charges', 'contact_p_1', 'designation_1', 'contact_no_1', 'contact_email_1'),
				[
					'sub_location_name' => 'required|max:255',
                    'location_code' => 'required|regex:/^[A-Za-z0-9]{5}$/',
                    'location_id' => 'required',
                    'nature_business' => 'required|max:255',
                    'address1' => 'required|max:255',
                    'address2' => 'nullable|max:255',
                    'city' => 'required|max:255',
                    'pincode' => 'required|digits:6',
                    'state' => 'required|max:255',
                    'country' => 'required|max:255',
                    'pan' => 'required|max:10',
                    'tan' => 'required|max:10',
                    'gstin' => 'required|max:15',
                    'acc_no' => 'required',
                    'ifsc' => 'required|regex:/^[A-Z]{4}[0][A-Z0-9]{2,6}$/|max:11',
                    'agr_valid_from' => 'required|date',
                    'agr_valid_till' => 'required|date|after:agr_valid_from',
                    'agreement' => 'required|file',
                    'payment_term' => 'required',
                    'special_remark' => 'required',
                    'payroll_cycle_from' => 'required',
                    'payroll_cycle_to' => 'required',
                    'invoicing_timeline' => 'required',
                    'payment_receivable' => 'required',
                    'payment_payable' => 'required',
                    'scope_revenue' => 'required',
                    'service_charges' => 'required',
                    'contact_p_1' => 'required',
                    'designation_1' => 'required',
                    'contact_no_1' => 'required|digits:10',
                    'contact_email_1' => 'required|email',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

            $data['name'] = $request->sub_location_name;
            $data['location_code'] = strtoupper(preg_replace('/\s+/', ' ', $request->location_code));
            $data['location_id'] = $request->location_id;
            $data['nature_business'] = $request->nature_business;
            $data['address1'] = $request->address1;
            $data['address2'] = $request->address2;
            $data['state'] = $request->state;
            $data['city'] = $request->city;
            $data['country'] = $request->country;
            $data['pincode'] = $request->pincode;
            $data['pan'] = $request->pan;
            $data['gst'] = $request->gstin;
            $data['tan'] = $request->tan;
            $data['accountNo'] = $request->acc_no;
            $data['ifsc'] = $request->ifsc;
            $data['agr_valid_from'] = $request->agr_valid_from;
            $data['agr_valid_till'] = $request->agr_valid_till;
            
            $file = $request->file('agreement');
            $filename = $file->getClientOriginalName();
            $path = public_path('uploads/agreements' . $filename);
            $file->move(public_path('uploads/agreements'), $filename);
            
            $data['agreement'] = 'uploads/agreements/' . $filename;
            
            $data['payment_term'] = $request->payment_term;
            $data['payment_remark'] = $request->special_remark;
            
            $data['payroll_cycle_from'] = $request->payroll_cycle_from;
            $data['payroll_cycle_to'] = $request->payroll_cycle_to;
            
            $data['invoicing_timeline'] = $request->invoicing_timeline;
            $data['payment_receivable'] = $request->payment_receivable;
            $data['payment_payable'] = $request->payment_payable;
            $data['scope_revenue'] = $request->scope_revenue;
            $data['service_charges'] = $request->service_charges;
            
            $data['contact_p_1'] = $request->contact_p_1;
            $data['designation_1'] = $request->designation_1;
            $data['contact_1'] = $request->contact_no_1;
            $data['email_1'] = $request->contact_email_1;
            $data['contact_p_2'] = $request->contact_p_2;
            $data['designation_2'] = $request->designation_2;
            $data['contact_2'] = $request->contact_no_2;
            $data['email_2'] = $request->contact_email_2;


			sub_locations::create($data);

			return response()->json(['success' => __('Data Added successfully.')]);
		}
		return response()->json(['success' => __('You are not authorized')]);
	}


	public function edit($id)
	{

		if(request()->ajax())
		{
			$data = sub_locations::findOrFail($id);
			return response()->json(['data' => $data]);
		}
	}
	
	public function view($id)
	{

		if(request()->ajax())
		{
			$data = DB::table('sub_locations')
			->select('sub_locations.*', 'locations.location_name')
            ->join('locations', 'sub_locations.location_id', '=', 'locations.id')
            ->where('sub_locations.id', '=', $id)
            ->get();;

			return response()->json(['data' => $data]);
		}
	}

	public function update(Request $request)
	{

		$logged_user = auth()->user();

		if ($logged_user->can('edit-location'))
		{
    		    
    	    $validator = Validator::make($request->only('sub_location_name', 'location_code', 'location_id', 'nature_business', 'address1', 'address2', 'city',
    			'state', 'country', 'pincode', 'pan', 'tan', 'gstin', 'acc_no', 'ifsc', 'agr_valid_from', 'agr_valid_till', 'agreement', 'payment_term', 'special_remark', 'payroll_cycle_from', 'payroll_cycle_to', 'invoicing_timeline', 'payment_receivable', 'payment_payable', 'scope_revenue', 'service_charges', 'contact_p_1', 'designation_1', 'contact_no_1', 'contact_email_1'),
    			[
    				'sub_location_name' => 'required|regex:/^(?!\s)(?!.*\s$)[\w\s]+$/|max:255',
                    'location_code' => 'required|regex:/^[A-Za-z0-9]{5}$/',
                    'location_id' => 'required',
                    'nature_business' => 'required|max:255',
                    'address1' => 'required|max:255',
                    'address2' => 'required|max:255',
                    'city' => 'required|max:255',
                    'pincode' => 'required|digits:6',
                    'state' => 'required|max:255',
                    'country' => 'required|max:255',
                    'pan' => 'required|max:10',
                    'tan' => 'required|max:10',
                    'gstin' => 'required|max:15',
                    'acc_no' => 'required',
                    'ifsc' => 'required|regex:/^[A-Z]{4}[0][A-Z0-9]{2,6}$/|max:11',
                    'agr_valid_from' => 'required|date',
                    'agr_valid_till' => 'required|date|after:agr_valid_from',
                    'payment_term' => 'required',
                    'special_remark' => 'required',
                    'payroll_cycle_from' => 'required',
                    'payroll_cycle_to' => 'required',
                    'invoicing_timeline' => 'required',
                    'payment_receivable' => 'required',
                    'payment_payable' => 'required',
                    'scope_revenue' => 'required',
                    'service_charges' => 'required',
                    'contact_p_1' => 'required',
                    'designation_1' => 'required',
                    'contact_no_1' => 'required|digits:10',
                    'contact_email_1' => 'required|email',
    			]
    		);
    
    
    		if ($validator->fails())
    		{
    			return response()->json(['errors' => $validator->errors()->all()]);
    		}
    		    
           $id = $request->hidden_id;

			$data = [];

            $data['name'] = $request->sub_location_name;
            $data['location_code'] = strtoupper(preg_replace('/\s+/', ' ', $request->location_code));
            $data['location_id'] = $request->location_id;
            $data['nature_business'] = $request->nature_business;
            $data['address1'] = $request->address1;
            $data['address2'] = $request->address2;
            $data['state'] = $request->state;
            $data['city'] = $request->city;
            $data['country'] = $request->country;
            $data['pincode'] = $request->pincode;
            $data['pan'] = $request->pan;
            $data['gst'] = $request->gstin;
            $data['tan'] = $request->tan;
            $data['accountNo'] = $request->acc_no;
            $data['ifsc'] = $request->ifsc;
            $data['agr_valid_from'] = $request->agr_valid_from;
            $data['agr_valid_till'] = $request->agr_valid_till;
            
            if($request->file('agreement')){
                $file = $request->file('agreement');
                $filename = $file->getClientOriginalName();
                $path = public_path('uploads/agreements' . $filename);
                $file->move(public_path('uploads/agreements'), $filename);
                
                $data['agreement'] = 'uploads/agreements/' . $filename;
            }
            
            $data['payment_term'] = $request->payment_term;
            $data['payment_remark'] = $request->special_remark;
            
            $data['payroll_cycle_from'] = $request->payroll_cycle_from;
            $data['payroll_cycle_to'] = $request->payroll_cycle_to;
            
            $data['invoicing_timeline'] = $request->invoicing_timeline;
            $data['payment_receivable'] = $request->payment_receivable;
            $data['payment_payable'] = $request->payment_payable;
            $data['scope_revenue'] = $request->scope_revenue;
            $data['service_charges'] = $request->service_charges;
            
            $data['contact_p_1'] = $request->contact_p_1;
            $data['designation_1'] = $request->designation_1;
            $data['contact_1'] = $request->contact_no_1;
            $data['email_1'] = $request->contact_email_1;
            $data['contact_p_2'] = $request->contact_p_2;
            $data['designation_2'] = $request->designation_2;
            $data['contact_2'] = $request->contact_no_2;
            $data['email_2'] = $request->contact_email_2;


			sub_locations::whereId($id)->update($data);

			return response()->json(['success' => __('Data is successfully updated')]);

		}
		return response()->json(['success' => __('You are not authorized')]);
	}


	public function delete($id)
	{

		if(!env('USER_VERIFIED'))
		{
			return response()->json(['success' => 'This feature is disabled for demo!']);
		}
		$logged_user = auth()->user();

		if ($logged_user->can('del-sub-location'))
		{
		     sub_locations::whereId($id)->delete();
		     return "success";

		}
		return response()->json(['success' => __('You are not authorized')]);
	}


	public function delete_by_selection(Request $request)
	{
		if(!env('USER_VERIFIED'))
		{
			return response()->json(['error' => 'This feature is disabled for demo!']);
		}
		$logged_user = auth()->user();

		if ($logged_user->can('del-sub-location'))
		{

			$location_id = $request['locationIdArray'];
			$location = sub_locations::whereIntegerInRaw('id', $location_id);
			if ($location->delete())
			{
				return response()->json(['success' => __('Multi Delete',['key'=>trans('Sub Location')])]);
			}
			else {
				return response()->json(['error' => 'Error selected Locations can not be deleted']);
			}
		}
		return response()->json(['success' => __('You are not authorized')]);
	}


}
