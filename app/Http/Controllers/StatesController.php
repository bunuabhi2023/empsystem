<?php

namespace App\Http\Controllers;

use App\states;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class StatesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	 
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(states::select("*")->latest()->get())
                ->setRowId(function ($state) {
                    return $state->id;
                })
                ->addColumn('country_name', function ($data) {
                    return $data->country->name;
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    if (auth()->user()->can('edit-client-group')) {
                        $button .= '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm"><i class="dripicons-pencil"></i></button>';
                        $button .= '&nbsp;&nbsp;';
                    }
                    if (auth()->user()->can('del-client-group')) {
                        $button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="dripicons-trash"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    
        $countries = DB::table('countries')->select('id', 'name')->get();
        return view('settings.states.index', compact('countries'));
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
			$validator = Validator::make($request->only('state_name', 'country'),
				[
					'state_name' => 'required',
					'country' => 'required',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}


			$data = [];

			$data['name'] = preg_replace('/\s+/', ' ', $request->state_name);
			$data['country_id'] = preg_replace('/\s+/', ' ', $request->country);

			states::create($data);

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
			$data = states::findOrFail($id);

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
		    $validator = Validator::make($request->only('state_name', 'country'),
				[
					'state_name' => 'required',
					'country' => 'required',
				]
			);


			if ($validator->fails())
			{
				return response()->json(['errors' => $validator->errors()->all()]);
			}
		    
			$id = $request->hidden_id;
			$data = [];

		    $data['name'] = preg_replace('/\s+/', ' ', $request->state_name);
			$data['country_id'] = preg_replace('/\s+/', ' ', $request->country);
			
			states::whereId($id)->update($data);

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
			states::whereId($id)->delete();

			return response()->json(['success' => __('Data is successfully deleted')]);

		}

		return response()->json(['success' => __('You are not authorized')]);

	}
}