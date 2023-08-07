<?php

namespace App\Http\Controllers\FrontEnd;

use App\company;
use App\department;
use App\designation;
use App\DocumentType;
use App\Employee;
use App\EmployeeDocument;
use App\EmployeeImmigration;
use App\Imports\UsersImport;
use App\office_shift;
use App\PaidSalary;
use App\QualificationEducationLevel;
use App\QualificationLanguage;
use App\QualificationSkill;
use App\salary;
use App\status;
use App\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Spatie\Permission\Models\Role;
use Throwable;
use Barryvdh\DomPDF\Facade as PDF;

use App\SalaryBasic;


class EmployeeControllerF {

	public function index($id)
	{
	    $employee = Employee::find($id);
		$companies = Company::select('id', 'company_name')->get();
		$departments = department::select('id', 'department_name')
			->where('company_id', $employee->company_id)
            ->orWhere(function ($query) {
                $query->where('is_common', 1);
            })
            ->get();

		$designations = designation::select('id', 'designation_name')
			->where('department_id', $employee->department_id)
			->get();

		$office_shifts = office_shift::select('id', 'shift_name')
			->where('company_id', $employee->company_id)
			->get();


		$statuses = status::select('id', 'status_title')->get();
		// $roles = Role::select('id', 'name')->get();
		$countries = DB::table('countries')->select('id', 'name')->get();
		$document_types = DocumentType::select('id', 'document_type')->get();

		$education_levels = QualificationEducationLevel::select('id', 'name')->get();
		$language_skills = QualificationLanguage::select('id', 'name')->get();
		$general_skills = QualificationSkill::select('id', 'name')->get();

		$roles = Role::where('id', '!=', 3)->where('is_active',1)->select('id', 'name')->get(); //--new--


		return view('employee2.reg_form', compact('employee', 'countries', 'companies',
			'departments', 'designations', 'statuses', 'office_shifts', 'document_types', 'education_levels', 'language_skills', 'general_skills','roles'));
	}


	public function infoUpdate(Request $request, $employee)
	{
	    $validator = Validator::make($request->only('alt_phone', 'contact_no', 'zip_code'),
			[
				'alt_phone' => 'nullable|max:10',
				'contact_no' => 'required|max:10',
				'zip_code' => 'required|digits:6'
			]
		);

		if ($validator->fails())
		{
			return response()->json(['errors' => $validator->errors()->all()]);
		}else{
    		$data = [];
    		
    		$data['first_name'] = $request->first_name;
    		$data['last_name'] = $request->last_name;
    		$data['fname'] = $request->fname;
    		$data['mname'] = $request->mname;
    		$data['date_of_birth'] = $request->date_of_birth;
    		$data['gender'] = $request->gender;
    		$data ['marital_status'] = $request->marital_status;
    		$data['mAnniversary'] = $request->mAnniversary;
    		$data['contact_no'] = $request->contact_no;
    		$data['alt_phone'] = $request->alt_phone;
    		$data['email'] = $request->email;
    
    		$data ['address'] = $request->address;
    		$data ['address2'] = $request->address2;
    		$data ['city'] = $request->city;
    		$data['state'] = $request->state;
    		$data ['country'] = $request->country;
    		$data ['zip_code'] = $request->zip_code;
    		
    		$data['religion'] = $request->religion;
    		$data['blood_grp'] = $request->blood_grp;
    		$data['disability'] = $request->disability;
    		$data['s_disability'] = $request->s_disability;
    
    		$user = [];
    		$user['first_name'] = $request->first_name;
    		$user['last_name'] = $request->last_name;
    		$user['username'] = strtolower(trim($request->username));
    		$user['email'] = strtolower(trim($request->email));
    		//$user['password'] = bcrypt($request->password);
    		$user['contact_no'] = $request->contact_no;
    		$user['is_active'] = 0;
    
    		DB::beginTransaction();
    		try
    		{
    			employee::find($employee)->update($data);
    			User::whereId($employee)->update($user);
    
    			DB::commit();
    		} catch (Exception $e)
    		{
    			DB::rollback();
    
    			return response()->json(['error' => $e->getMessage()]);
    		} catch (Throwable $e)
    		{
    			DB::rollback();
    
    			return response()->json(['error' => $e->getMessage()]);
    		}
    
    		return response()->json(['success' => __('Details Updated Successfully.')]);
		}
	}

	public function socialProfileShow(Employee $employee)
	{
		return view('employee.social_profile.index', compact('employee'));
	}

	public function storeSocialInfo(Request $request, $employee)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee') || $logged_user->id == $employee)
		{
			$data = [];
			$data['fb_id'] = $request->fb_id;
			$data['twitter_id'] = $request->twitter_id;
			$data['linkedIn_id'] = $request->linkedIn_id;
			$data['whatsapp_id'] = $request->whatsapp_id;
			$data ['skype_id'] = $request->skype_id;

			Employee::whereId($employee)->update($data);

			return response()->json(['success' => __('Data is successfully updated')]);

		}

		return response()->json(['success' => __('You are not authorized')]);

	}

	public function indexProfilePicture(Employee $employee)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
			return view('employee.profile_picture.index', compact('employee'));
		}

		return response()->json(['success' => __('You are not authorized')]);
	}

	public function storeProfilePicture(Request $request, $employee)
	{
		$data = [];
		$photo = $request->profile_photo;
		$file_name = null;

		if (isset($photo))
		{
			$new_user = $request->employee_username;
			if ($photo->isValid())
			{
				$file_name = preg_replace('/\s+/', '', $new_user) . '_' . time() . '.' . $photo->getClientOriginalExtension();
				$photo->storeAs('profile_photos', $file_name);
				$data['profile_photo'] = $file_name;
			}
		}

		$this->unlink($employee);

		User::whereId($employee)->update($data);

		return response()->json(['success' => 'Data is successfully updated', 'profile_picture' => $file_name]);
	}
	
	public function submitFormMain($employee){
	    $data = [];
		$data['is_active'] = 1;
		$data['form_token'] = NULL;

		DB::beginTransaction();
		try
		{
			employee::find($employee)->update($data);
			DB::commit();
		} catch (Exception $e)
		{
			DB::rollback();

			return response()->json(['error' => $e->getMessage()]);
		} catch (Throwable $e)
		{
			DB::rollback();

			return response()->json(['error' => $e->getMessage()]);
		}

		return response()->json(['success' => __('Registration Successfully.')]);
	}
	
	public function unlink($employee)
	{

		$user = User::findOrFail($employee);
		$file_path = $user->profile_photo;

		if ($file_path)
		{
			$file_path = public_path('uploads/profile_photos/' . $file_path);
			if (file_exists($file_path))
			{
				unlink($file_path);
			}
		}
	}
}