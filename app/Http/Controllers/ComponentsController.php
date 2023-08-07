<?php

namespace App\Http\Controllers;

use App\salary_components;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class ComponentsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if (request()->ajax())
		{
			return datatables()->of(salary_components::select("*")->latest()->get())
				->setRowId(function ($components)
				{
					return $components->id;
				})
				->addColumn('type', function ($data)
				{
				    $type = "";
				    switch($data->type){
				        case 0:
				            $type = "Variable";
				            break;
				        case 1:
				            $type = "Compliances";
				            break;
				        case 2:
				            $type = "Fixed";
				            break;
				        case 3:
				            $type = "One Time";
				            break;
				    }
				    return $type;
				})
				->addColumn('category', function ($data)
				{
					return $data->category == 0 ? "Addition":"Deduction";
				})
				->addColumn('action', function ($data)
				{
				    $button = "";
					if(auth()->user()->can('edit-client-group'))
					{
						$button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
						$button .= '&nbsp;&nbsp;';
					}
					if(auth()->user()->can('del-client-group'))
					{
						$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
					}
					return $button;
				})
				->rawColumns(['action'])
				->make(true);
		}

		return view('organization.components.index');
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
			$validator = Validator::make($request->only('component_title', 'component_type', 'component_cat', 'remarks'),
				[
					'component_title' => 'required',
					'component_type' => 'required',
					'component_cat' => 'required',
					'remarks' => 'nullable',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['title'] = preg_replace('/\s+/', ' ', $request->component_title);
			$data['type'] = $request->component_type;
			$data ['category'] = $request->component_cat;
			$data ['remarks'] = $request->remarks;

			salary_components::create($data);

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
			$data = salary_components::findOrFail($id);

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
		    $validator = Validator::make($request->only('component_title', 'component_type', 'component_cat', 'remarks'),
				[
					'component_title' => 'required',
					'component_type' => 'required',
					'component_cat' => 'required',
					'remarks' => 'nullable',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}
		    
			$id = $request->hidden_id;
			$data = [];

		    $data['title'] = preg_replace('/\s+/', ' ', $request->component_title);
			$data['type'] = $request->component_type;
			$data ['category'] = $request->component_cat;
			$data ['remarks'] = $request->remarks;
			
			salary_components::whereId($id)->update($data);

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
			salary_components::whereId($id)->delete();

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
			$company = salary_components::whereIntegerInRaw('id', $company_id);

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