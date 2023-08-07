<?php

namespace App\Http\Controllers;

use App\client_groups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class ClientGroupController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if (request()->ajax())
		{
			return datatables()->of(client_groups::select("*")->latest()->get())
				->setRowId(function ($client_groups)
				{
					return $client_groups->id;
				})
				->addColumn('sr_no', function ($data){
				    return $data->id;
				})
				->addColumn('action', function ($data)
				{
				    $button = "<div style='display: flex;gap: 10px;'>";
					if(auth()->user()->can('edit-client-group'))
					{
					    $button .= '<button type="button" name="view" id="' . $data->id . '" class="view btn btn-success btn-sm"><i class="fa fa-eye"></i></button>';
						$button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
					}
					if(auth()->user()->can('del-client-group'))
					{
						$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
					}
					$button .= '</div>';
					return $button;
				})
				->rawColumns(['action'])
				->make(true);
		}
        $countries = \DB::table('countries')->select('id', 'name')->get();
		return view('organization.client_group.index', compact('countries'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */

	public function store(Request $request)
	{
		if(auth()->user()->can('add-client-group'))
		{
			$validator = Validator::make($request->only('group_name', 'group_code', 'head_office', 'remarks'),
				[
					'group_name' => 'required|unique:client_groups,name',
					'group_code' => 'required|regex:/^[A-Za-z0-9]{5}$/',
					'head_office' => 'required',
					'remarks' => 'nullable',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['name'] = preg_replace('/\s+/', ' ', $request->group_name);
			$data['code'] = strtoupper(preg_replace('/\s+/', ' ', $request->group_code));
			$data ['head_office'] = $request->head_office;
			$data ['remarks'] = $request->remarks;

			client_groups::create($data);

			return response()->json(['success' => __('Data Added successfully.')]);
		}
		return response()->json(['success' => __('You are not authorized')]);
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
			$data = client_groups::findOrFail($id);

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

		if ($logged_user->can('edit-client-group'))
		{
		    $validator = Validator::make($request->only('group_name', 'group_code', 'head_office', 'remarks'),
				[
					'group_name' => 'required',
					'group_code' => 'required|regex:/^[A-Za-z0-9]{5}$/',
					'head_office' => 'required',
					'remarks' => 'nullable',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}
		    
			$id = $request->hidden_id;
			$data = [];

		    $data['name'] = preg_replace('/\s+/', ' ', $request->group_name);
			$data['code'] = strtoupper(preg_replace('/\s+/', ' ', $request->group_code));
			$data ['head_office'] = $request->head_office;
			$data ['remarks'] = $request->remarks;
			
			client_groups::whereId($id)->update($data);

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

		if ($logged_user->can('del-client-group'))
		{
			client_groups::whereId($id)->delete();

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

		if ($logged_user->can('del-client-group'))
		{

			$company_id = $request['companyIdArray'];
			$company = client_groups::whereIntegerInRaw('id', $company_id);

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