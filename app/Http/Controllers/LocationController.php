<?php

namespace App\Http\Controllers;

use App\Employee;
use App\location;
use App\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Spatie\Permission\Models\Role;


class LocationController extends Controller
{
	public function index()
	{
		$countries = \DB::table('countries')->select('id','name')->get();
		$companies = \DB::table('companies')->select('id','company_name')->get();
		$employees = Employee::select('id','first_name','last_name')->where('is_active',1)->where('exit_date',NULL)->get();

		if(request()->ajax())
		{
			return datatables()->of(location::select('locations.*', 'companies.company_name')
                                    ->join('companies', 'companies.id', '=', 'locations.company_id')
                                    ->latest()
                                    ->get())
                ->addColumn('sr_no', function ($data){
				    return $data->id;
				})
                ->addColumn('state', function ($data){
				    return $data->stateS->name;
				})
                ->addColumn('country', function ($data){
				    return $data->countryS->name;
				})
				->addColumn('action', function($data){
					$button = '<div style="display: flex;gap: 10px">';
					if (auth()->user()->can('edit-location'))
					{
					    $button .= '<button type="button" name="view" id="' . $data->id . '" class="view btn btn-success btn-sm"><i class="fa fa-eye"></i></button>';
						$button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
					}
					if (auth()->user()->can('edit-location'))
					{
						$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
					}
					$button .= '</div>';
					return $button;
				})
				->rawColumns(['action'])
				->make(true);
		}
		return view('organization.location.index',compact('countries','employees','companies'));
	}


	public function store(Request $request)
	{

		$logged_user = auth()->user();

		if ($logged_user->can('store-location'))
		{

			$validator = Validator::make($request->only('location_name', 'location_code', 'state_code', 'remarks', 'company', 'country', 'state'),
				[
					'location_name' => 'required|unique:locations,location_name,',
					'location_code' => 'required|regex:/^[A-Za-z0-9]{5}$/|unique:locations,location_code',
					'state_code' => 'required|size:2',
                    'state' => 'required|max:255',
                    'country' => 'required|max:255',
					'remarks'=> 'nullable|max:255',
					'company'=> 'required'
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['location_name'] = $request->location_name;
			$data ['location_code'] = strtoupper(preg_replace('/\s+/', ' ', $request->location_code));
			$data ['state'] = $request->state;
			$data ['country'] = $request->country;
			$data ['state_code'] = $request->state_code;
			$data ['remarks'] = $request->remarks;
			$data ['company_id'] = $request->company;

			location::create($data);

			return response()->json(['success' => __('Data Added successfully.')]);
		}
		return response()->json(['success' => __('You are not authorized')]);
	}


	public function edit($id)
	{

		if(request()->ajax())
		{
			$data = location::findOrFail($id);
			return response()->json(['data' => $data]);
		}
	}


	public function update(Request $request)
	{

		$logged_user = auth()->user();

		if ($logged_user->can('edit-location'))
		{
		    $validator = Validator::make($request->only('location_name', 'location_code', 'state_code', 'remarks', 'company', 'country', 'state'),
				[
					'location_name' => 'required',
					'location_code' => 'required|regex:/^[A-Za-z0-9]{5}$/',
					'state' => 'required|max:255',
                    'country' => 'required|max:255',
					'state_code' => 'required|size:2',
					'remarks'=> 'nullable|max:255',
					'company'=> 'required'
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}
			
            $id = $request->hidden_id;
           
            $data = [];
			$data['location_name'] = $request->location_name;
			$data ['location_code'] = strtoupper(preg_replace('/\s+/', ' ', $request->location_code));
			$data ['state'] = $request->state;
			$data ['country'] = $request->country;
			$data ['state_code'] = $request->state_code;
			$data ['remarks'] = $request->remarks;
			$data ['company_id'] = $request->company;

			location::whereId($id)->update($data);

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

		if ($logged_user->can('delete-location'))
		{
		     location::whereId($id)->delete();
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

		if ($logged_user->can('delete-location'))
		{

			$location_id = $request['locationIdArray'];
			$location = location::whereIntegerInRaw('id', $location_id);
			if ($location->delete())
			{
				return response()->json(['success' => __('Multi Delete',['key'=>trans('file.Location')])]);
			}
			else {
				return response()->json(['error' => 'Error selected Locations can not be deleted']);
			}
		}
		return response()->json(['success' => __('You are not authorized')]);
	}


}
