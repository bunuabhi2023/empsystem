<?php

namespace App\Http\Controllers;

use App\company;
use App\client_groups;
use App\location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
	    $countries = \DB::table('countries')->select('id','name')->get();
		$locations = location::all('id','location_name');
		$client_groups = client_groups::all('id','name');
		if (request()->ajax())
		{
			return datatables()->of(company::with('Location.Country')->latest()->get())
				->setRowId(function ($company)
				{
					return $company->id;
				})
				->addColumn('client_group', function ($data){
				    return $data->client_groups->name;
				})
				->addColumn('sr_no', function ($data){
				    return $data->id;
				})
				->addColumn('company_name', function ($data){
				    return $data->company_name;
				})
				->addColumn('company_code', function ($data){
				    return $data->company_code;
				})
				->addColumn('company_type', function ($data){
				    return $data->company_type;
				})
				->addColumn('trading_name', function ($data){
				    return $data->trading_name;
				})
				->addColumn('registration_no', function ($data){
				    return $data->registration_no;
				})
				->addColumn('contact_no', function ($data){
				    return $data->contact_no;
				})
				->addColumn('email', function ($data){
				    return $data->email;
				})
				->addColumn('website', function ($data){
				    return $data->website;
				})
				->addColumn('date_of_inco', function ($data){
				    return $data->date_of_inco;
				})
				->addColumn('add1', function ($data){
				    return $data->add1;
				})
				->addColumn('add2', function ($data){
				    return $data->add2;
				})
				->addColumn('city', function ($data){
				    return $data->city;
				})
				->addColumn('state', function ($data){
				    return $data->stateS->name;
				})
				->addColumn('country', function ($data){
				    return $data->countryS->name;
				})
				->addColumn('zip', function ($data){
				    return $data->zip;
				})
				->addColumn('remarks', function ($data){
				    return $data->remarks;
				})
				->addColumn('action', function ($data)
				{
					$button = '<div style="display: flex;gap: 10px">';
					if(auth()->user()->can('edit-company'))
					{
					    $button .= '<button type="button" name="view" id="' . $data->id . '" class="view btn btn-success btn-sm"><i class="fa fa-eye"></i></button>';
						$button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
					}
					if(auth()->user()->can('delete-company'))
					{
						$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
					}
					$button .= '</div>';
					return $button;
				})
				->rawColumns(['action'])
				->make(true);
		}

		return view('organization.company.index',compact('locations','client_groups', 'countries'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */

	public function store(Request $request)
	{
		if(auth()->user()->can('store-company'))
		{
			$validator = Validator::make($request->only('client_group_m', 'company_name', 'company_code', 'company_type', 'trading_name', 'registration_no', 'contact_no', 'email', 'website', 'date_of_inco', 'company_logo', 'address1', 'address2', 'city', 'state', 'country', 'pincode'),
				[
					'company_name' => 'required|unique:companies,company_name',
					'company_code' => 'required|regex:/^[A-Za-z0-9]{5}$/|unique:companies,company_code',
					'client_group_m' => 'required',
					'company_type' => 'required',
					'email' => 'email',
					'address1' => 'required|max:255',
                    'address2' => 'nullable|max:255',
                    'city' => 'required|max:255',
                    'pincode' => 'required|digits:6',
                    'state' => 'required|max:255',
                    'country' => 'required|max:255',
					'registration_no' => 'required',
					'trading_name' => 'required',
					'contact_no' => 'nullable|digits:10',
				// 	'location_id' => 'required',
					'company_logo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif'
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['client_grp_id'] = $request->client_group_m;
			$data['company_name'] = $request->company_name;
			$data['company_code'] = strtoupper(preg_replace('/\s+/', ' ', $request->company_code));
			$data['company_type'] = $request->company_type;
			$data ['trading_name'] = $request->trading_name;
			$data ['registration_no'] = $request->registration_no;
			$data ['contact_no'] = $request->contact_no;
			$data ['email'] = $request->email;
			$data ['website'] = $request->website;
			$data ['date_of_inco'] = $request->date_of_inco;
		    $data['add1'] = $request->address1;
            $data['add2'] = $request->address2;
            $data['state'] = $request->state;
            $data['city'] = $request->city;
            $data['country'] = $request->country;
            $data['zip'] = $request->pincode;
			$data ['remarks'] = $request->remarks;
// 			$data ['location_id'] = $request->location_id;

			$company_logo = $request->company_logo;

			if (isset($company_logo))
			{

				if ($company_logo->isValid())
				{
					$file_name = preg_replace('/\s+/', '', rand()) . '_' . time() . '.' . $company_logo->getClientOriginalExtension();
					$company_logo->storeAs('company_logo', $file_name);
					$data['company_logo'] = $file_name;
				}
			}


			company::create($data);


			return response()->json(['success' => __('Data Added successfully.')]);
		}
		return response()->json(['success' => __('You are not authorized')]);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if (request()->ajax())
		{
			$data = company::with('location.country')->with('client_groups')->findOrFail($id);

			return response()->json(['data' => $data]);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{

		if (request()->ajax())
		{
			$data = company::findOrFail($id);

			return response()->json(['data' => $data]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{

		$logged_user = auth()->user();

		if ($logged_user->can('edit-company'))
		{
			$id = $request->hidden_id;

			$validator = Validator::make($request->only('client_group_m', 'company_name', 'company_code', 'company_type', 'trading_name', 'registration_no', 'contact_no', 'email', 'website', 'date_of_inco', 'address1', 'address2', 'city', 'state', 'country', 'pincode',
				'company_logo'),
				[
				    'client_group_m' => 'required',
					'company_name' => 'required',
					'company_code' => 'required|regex:/^[A-Za-z0-9]{5}$/',
					'email' => 'email',
					'contact_no' => 'nullable|digits:10',
					'address1' => 'required|max:255',
                    'address2' => 'required|max:255',
                    'city' => 'required|max:255',
                    'pincode' => 'required|digits:6',
                    'state' => 'required|max:255',
                    'country' => 'required|max:255',
				// 	'location_id' => 'required',
					'company_logo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif'
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];
            
            $data['client_grp_id'] = $request->client_group_m;
			$data['company_name'] = $request->company_name;
			$data['company_code'] = strtoupper(preg_replace('/\s+/', ' ', $request->company_code));
			$data ['trading_name'] = $request->trading_name;
			$data ['registration_no'] = $request->registration_no;
			$data ['contact_no'] = $request->contact_no;
			$data ['email'] = $request->email;
			$data ['website'] = $request->website;
			$data ['date_of_inco'] = $request->date_of_inco;
			$data['add1'] = $request->address1;
            $data['add2'] = $request->address2;
            $data['state'] = $request->state;
            $data['city'] = $request->city;
            $data['country'] = $request->country;
            $data['zip'] = $request->pincode;
			$data ['remarks'] = $request->remarks;
// 			$data ['location_id'] = $request->location_id;

			if ($request->company_type)
			{
				$data ['company_type'] = $request->company_type;
			}


			$company_logo = $request->company_logo;

			if (isset($company_logo))
			{

				if ($company_logo->isValid())
				{
					$file_name = preg_replace('/\s+/', '', rand()) . '_' . time() . '.' . $company_logo->getClientOriginalExtension();
					$company_logo->storeAs('company_logo', $file_name);
					$data['company_logo'] = $file_name;
				}
			}
			company::whereId($id)->update($data);

			return response()->json(['success' => __('Data is successfully updated')]);

		} else
		{
			return response()->json(['success' => __('You are not authorized')]);
		}


	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(!env('USER_VERIFIED'))
		{
			return response()->json(['error' => 'This feature is disabled for demo!']);
		}
		$logged_user = auth()->user();

		if ($logged_user->can('delete-company'))
		{
			company::whereId($id)->delete();

			return response()->json(['success' => __('Data is successfully deleted')]);

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

		if ($logged_user->can('delete-company'))
		{

			$company_id = $request['companyIdArray'];
			$company = company::whereIntegerInRaw('id', $company_id);

			if ($company->delete())
			{
				return response()->json(['success' => __('Multi Delete',['key'=>trans('file.Company')])]);
			} else
			{
				return response()->json(['error' => 'Error,selected users can not be deleted']);
			}
		}
		return response()->json(['success' => __('You are not authorized')]);
	}

}


