<?php

namespace App\Http\Controllers;

use App\company;
use App\department;
use App\designation;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
	{

		$companies = company::select('id', 'company_name')->get();

			if (request()->ajax())
			{
				return datatables()->of(designation::with('company','department')->get())
					->setRowId(function ($designation)
					{
						return $designation->id;
					})
					->addColumn('company', function ($row)
					{
						return $row->company->company_name ?? ' ' ;
					})
					->addColumn('department', function ($row)
					{
						return empty($row->department->department_name) ? '' : $row->department->department_name;
					})
					->addColumn('action', function ($data)
					{
						$button= '';
						if (auth()->user()->can('edit-designation'))
						{
							$button = '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
							$button .= '&nbsp;&nbsp;';
						}
						if (auth()->user()->can('delete-designation'))
						{
							$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
						}
							return $button;
					})
					->rawColumns(['action'])
					->make(true);
			}
			return view('organization.designation.index', compact('companies'));
		}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('store-designation'))
		{
			$validator = Validator::make($request->only('designation_name', 'company_id', 'department_id', 'common_des'),
				[
					'designation_name' => 'required|unique:designations,designation_name,NULL,id,department_id,'.$request->department_id,
					'department_id' => 'nullable',
					'common_des' => 'nullable|boolean',
                    'company_id' => $request->common_des ? 'nullable' : 'required'
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['designation_name'] = $request->designation_name;
			if($request->company_id){
			    $data['company_id'] = $request->company_id;
			}
			if($request->common_des){
			    $data['is_common'] = $request->common_des;
			}
			if($request->department_id){
			    $data ['department_id'] = $request->department_id;
			}




			designation::create($data);

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
		if(request()->ajax())
		{
			$data = designation::findOrFail($id);

			$departments = Department::select('id', 'department_name')->where('company_id', $data->company_id)
			                ->orWhere(function ($query) {
                                $query->where('is_common', 1);
                            })
                            ->get();


			return response()->json(['data' => $data,'departments'=>$departments]);
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

		if ($logged_user->can('edit-designation'))
		{
			$id = $request->hidden_id;

			$data = $request->only('designation_name', 'company_id', 'department_id', 'common_des');

			$validator = Validator::make($request->only('designation_name', 'company_id', 'department_id', 'common_des'),
				[
					'designation_name' => 'required|unique:designations,designation_name,'. $id .',id,department_id,'.$request->department_id,
					'common_des' => 'nullable|boolean',
                    'company_id' => $request->common_des ? 'nullable' : 'required'
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['designation_name'] = $request->designation_name;
			if($request->company_id)
			{
				$data['company_id'] = $request->company_id;
			}
			if($request->department_id)
			{
				$data ['department_id'] = $request->department_id;
			}
			if($request->common_des)
			{
				$data['is_common'] = $request->common_des;
				if($request->common_des == 1){
				    $data['company_id'] = NULL;
				}
			}
			


			designation::whereId($id)->update($data);

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

		if ($logged_user->can('delete-designation'))
		{
			designation::whereId($id)->delete();
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

		if ($logged_user->can('delete-designation'))
		{

			$designation_id = $request['designationIdArray'];
			$designation = designation::whereIntegerInRaw('id', $designation_id);
			if ($designation->delete())
			{
				return response()->json(['success' => __('Multi Delete',['key'=>trans('file.Designation')])]);
			}
			else {
				return response()->json(['error' => 'Error selected designation can not be deleted']);
			}
		}
		return response()->json(['success' => __('You are not authorized')]);
	}
}
