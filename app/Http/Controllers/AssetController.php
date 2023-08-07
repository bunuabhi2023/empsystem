<?php

namespace App\Http\Controllers;

use App\Asset;
use App\client_groups;
use App\AssetCategory;
use App\company;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$client_groups = client_groups::select('id', 'name')->get();
		$asset_categories = AssetCategory::select('id', 'category_name')->get();

		if (request()->ajax())
		{
			if(!(auth()->user()->can('view-assets')))
			{
				$assets = Asset::with('company', 'employee', 'Category')->where('employee_id',auth()->user()->id)->get();
			}else{
				$assets = Asset::with('company', 'employee', 'Category')->get();
			}

			return datatables()->of($assets)
				->setRowId(function ($row)
				{
					return $row->id;
				})
				->addColumn('client_grp', function ($row)
				{
					return $row->client_groups->name ?? ' ';
				})
				->addColumn('company', function ($row)
				{
					return $row->company->company_name ?? ' ';
				})
				->addColumn('location', function ($row)
				{
					return $row->location->location_name ?? ' ';
				})
				->addColumn('sub_location', function ($row)
				{
					return $row->sub_location->name ?? ' ';
				})
				->addColumn('employee', function ($row)
				{
					return $row->employee->full_name ?? '';
				})
				->addColumn('category', function ($row)
				{
					return $row->Category->category_name ?? '';
				})
				->addColumn('action', function ($data)
				{
					$button = '<div style="display: flex;gap: 10px;">';
					if ((Auth::user()->can('edit-assets')) && (Auth::user()->can('delete-assets'))){
						$button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
						$button .= '<button type="button" name="show" id="' . $data->id . '" class="show_new btn btn-success btn-sm"><i class="dripicons-preview"></i></button>';
						$button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
					}
					$button .= '</div>';
					return $button;

				})
				->rawColumns(['action'])
				->make(true);
		}

		return view('assets.index', compact('client_groups', 'asset_categories'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{

		$validator = Validator::make($request->only('client_grp_id', 'company_id', 'location_id', 'sub_location_id', 'employee_id', 'asset_name', 'assets_category_id', 'asset_note', 'manufacturer', 'asset_code',
			'invoice_number', 'purchase_date', 'serial_number', 'asset_image', 'warranty_date', 'status'),
			[
				'client_grp_id' => 'required',
				'company_id' => 'required',
				'location_id' => 'required',
				'sub_location_id' => 'required',
				'employee_id' => 'required',
				'assets_category_id' => 'required',
				'asset_name' => 'required',
				'amount' => 'required',
				'warranty_date' => 'required',
				'purchase_date' => 'required',
				'status' => 'required',
				'asset_image' => 'required|image|max:5048|mimes:jpeg,png,jpg,gif'
			]);


		if ($validator->fails())
		{
			return response()->json(['errors' => $validator->errors()->all()]);
		}


		$data = [];

		$data['employee_id'] = $request->employee_id;
		$data['client_grp_id'] = $request->client_grp_id;
		$data['company_id'] = $request->company_id;
		$data['location_id'] = $request->location_id;
		$data['sub_location_id'] = $request->sub_location_id;
		$data['assets_category_id'] = $request->assets_category_id;
		$data ['Asset_note'] = $request->asset_note;
		$data['asset_code'] = $request->asset_code;
		$data['asset_name'] = $request->asset_name;
		$data['amount'] = $request->amount;
		$data['manufacturer'] = $request->manufacturer;
		$data['invoice_number'] = $request->invoice_number;
		$data ['serial_number'] = $request->serial_number;
		$data ['status'] = $request->status;
		$data ['purchase_date'] = $request->purchase_date;
		$data ['warranty_date'] = $request->warranty_date;

		$file = $request->asset_image;
		$file_name = null;


		if (isset($file))
		{
			$file_name = $request->asset_name;
			if ($file->isValid())
			{
				$file_name = preg_replace('/\s+/', '', $file_name) . '_' . time() . '.' . $file->getClientOriginalExtension();
				$file->storeAs('asset_file', $file_name);
				$data['asset_image'] = $file_name;
			}
		}


		Asset::create($data);

		return response()->json(['success' => __('Data Added successfully.')]);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function show($id)
	{
		if (request()->ajax())
		{
			$data = Asset::findOrFail($id);
			$company_name = $data->company->company_name ?? '';

			$employee_name = $data->employee->full_name ?? '';
			$assets_category_name = $data->Category->category_name ?? '';

			return response()->json(['data' => $data, 'employee_name' => $employee_name, 'company_name' => $company_name, 'assets_category_name' => $assets_category_name]);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (request()->ajax())
		{
			$data = Asset::findOrFail($id);

			$employees = Employee::select('id', 'first_name', 'last_name')
                        ->where('company_id', $data->company_id)
                        ->where('is_active',1)
                        ->orWhere('exit_date',NULL)
                        ->get();

			return response()->json(['data' => $data, 'employees' => $employees]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		$id = $request->hidden_id;

		$validator = Validator::make($request->only('client_grp_id', 'company_id', 'location_id', 'sub_location_id', 'employee_id', 'asset_name', 'assets_category_id', 'asset_note', 'manufacturer', 'asset_code',
			'invoice_number', 'purchase_date', 'serial_number', 'asset_image', 'warranty_date', 'status'),
			[

				'client_grp_id' => 'required',
				'company_id' => 'required',
				'location_id' => 'required',
				'sub_location_id' => 'required',
				'employee_id' => 'required',
				'assets_category_id' => 'required',
				'asset_name' => 'required',
				'warranty_date' => 'required',
				'purchase_date' => 'required',
				'status' => 'required',
				'asset_image' => 'nullable|image|max:5048|mimes:jpeg,png,jpg,gif'
			]
//				,
//				[
//					'asset_name.required' => 'Asset Name can not be empty',
//					'purchase_date.date' => 'Please input a valid Purchase date',
//					'purchase_date.required' => 'Please select the Purchase date',
//					'warranty_date.date' => 'Please input a valid Warranty date',
//					'warranty_date.required' => 'Please select the Warranty date',
//					'asset_image.image' => 'Must be a image (jpeg,png,jpg,gif)',
//					'asset_image.max' => 'Image size should be less than 5 mb',
//				]
		);


		if ($validator->fails())
		{
			return response()->json(['errors' => $validator->errors()->all()]);
		}


		$data = [];

		$data ['Asset_note'] = $request->asset_note;
		$data['asset_code'] = $request->asset_code;
		$data['asset_name'] = $request->asset_name;
		$data['manufacturer'] = $request->manufacturer;
		$data['invoice_number'] = $request->invoice_number;
		$data ['serial_number'] = $request->serial_number;
		$data ['purchase_date'] = $request->purchase_date;
		$data ['warranty_date'] = $request->warranty_date;

		$data['employee_id'] = $request->employee_id;
        $data['amount'] = $request->amount;
		$data['client_grp_id'] = $request->client_grp_id;
		$data['company_id'] = $request->company_id;
		$data['location_id'] = $request->location_id;
		$data['sub_location_id'] = $request->sub_location_id;

		$data['status'] = $request->status;

		$data['assets_category_id'] = $request->assets_category_id;

		$file = $request->asset_image;
		$file_name = null;


		if (isset($file))
		{
			$file_name = $request->asset_name;
			if ($file->isValid())
			{
				$file_name = preg_replace('/\s+/', '', $file_name) . '_' . time() . '.' . $file->getClientOriginalExtension();
				$file->storeAs('asset_file', $file_name);
				$data['asset_image'] = $file_name;
			}
		}

		Asset::find($id)->update($data);

		return response()->json(['success' => __('Data is successfully updated')]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if(!env('USER_VERIFIED'))
		{
			return response()->json(['error' => 'This feature is disabled for demo!']);
		}
		$asset = Asset::findOrFail($id);
		$file_path = $asset->asset_image;

		if ($file_path)
		{
			$file_path = public_path('uploads/asset_file/' . $file_path);
			if (file_exists($file_path))
			{
				unlink($file_path);
			}
		}

		$asset->delete();

		return response()->json(['success' => __('Data is successfully deleted')]);
	}


	public function delete_by_selection(Request $request)
	{
		if(!env('USER_VERIFIED'))
		{
			return response()->json(['error' => 'This feature is disabled for demo!']);
		}

		$asset_id = $request['assetIdArray'];
		$assets = Asset::whereIntegerInRaw('id', $asset_id)->get();

		foreach ($assets as $asset)
		{
			$file_path = $asset->asset_image;

			if ($file_path)
			{
				$file_path = public_path('uploads/asset_file/' . $file_path);
				if (file_exists($file_path))
				{
					unlink($file_path);
				}
			}
			$asset->delete();
		}

		return response()->json(['success' => __('Multi Delete', ['key' => trans('file.Travel')])]);
	}


	public function download($id)
	{
		$asset = Asset::findOrFail($id);
		$file_path = $asset->asset_image;
		$file_path = public_path('uploads/asset_file/' . $file_path);

		return response()->download($file_path);
	}
}
