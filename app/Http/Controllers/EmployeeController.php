<?php

namespace App\Http\Controllers;

use App\client_groups;
use App\salary_components;
use App\location;
use App\sub_locations;
use App\company;
use App\department;
use App\designation;
use App\DocumentType;
use App\Employee;
use App\EmployeeBankAccount;
use App\EmployeeDocument;
use App\EmployeeSalary;
use App\EmployeeImmigration;
use App\Imports\UsersImport;
use App\Imports\UsersBulkUpdate;
use App\office_shift;
use App\PaidSalary;
use App\QualificationEducationLevel;
use App\QualificationLanguage;
use App\QualificationSkill;
use App\salary;
use App\Logs;
use App\status;
use App\User;
use App\PaymentMethod;
use App\LeaveType;

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

use App\Rules\Above18YearsOldRule;

use Carbon\Carbon;
use App\SalaryBasic;


class EmployeeController extends Controller {   

	public function getEmployees(Request $request){
	    $logged_user = auth()->user();
		
        if ($logged_user->can('view-details-employee'))
        {
            $client_groups = client_groups::select('id', 'name')->get();
            $roles = Role::where('id', '!=', 3)->where('is_active',1)->select('id', 'name')->get();

            if (request()->ajax())
            {
                if ($request->company_id && $request->department_id && $request->designation_id && $request->office_shift_id){
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                ->where('company_id','=',$request->company_id)
                                ->where('department_id','=',$request->department_id)
                                ->where('designation_id','=',$request->designation_id)
                                ->where('office_shift_id','=',$request->office_shift_id)
                                ->where('is_active',1)
                                ->where('exit_date',NULL)
                                ->orWhere('exit_date','0000-00-00')
                                ->get();
                }elseif ($request->company_id && $request->department_id && $request->designation_id) {
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                ->where('company_id','=',$request->company_id)
                                ->where('department_id','=',$request->department_id)
                                ->where('designation_id','=',$request->designation_id)
                                ->where('is_active',1)
                                ->where('exit_date',NULL)
                                ->orWhere('exit_date','0000-00-00')
                                ->get();
                }elseif ($request->company_id && $request->department_id) {
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                ->where('company_id','=',$request->company_id)
                                ->where('department_id','=',$request->department_id)
                                ->where('is_active',1)
                                ->where('exit_date',NULL)
                                ->orWhere('exit_date','0000-00-00')
                                ->get();
                }elseif ($request->company_id && $request->office_shift_id) {
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                ->where('company_id','=',$request->company_id)
                                ->where('office_shift_id','=',$request->office_shift_id)
                                ->where('is_active',1)
                                ->where('exit_date',NULL)
                                ->orWhere('exit_date','0000-00-00')
                                ->get();
                }elseif ($request->company_id) {
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                ->where('company_id','=',$request->company_id)
                                ->where('is_active',1)
                                ->where('exit_date',NULL)
                                ->orWhere('exit_date','0000-00-00')
                                ->get();
                }else if($request->pending_employees == "1"){
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                ->orderBy('company_id')
                                ->where(function ($query) {
                                    $query->whereNotNull('form_token');
                                })
                                ->where('exit_date', null)
                                ->orWhere('exit_date', '0000-00-00')
                                ->get();
                }else if($request->inactive_employees == "1"){
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                    ->orderBy('company_id')
                                    ->where(function ($query) {
                                        $query->where('is_active', 0)
                                            ->orWhere('is_active', null);
                                    })
                                    ->where('form_token', null)
                                    ->orWhere('form_token', "")
                                    ->get();
                }else if($request->unapprove_employees == "1"){
                    $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                    ->orderBy('company_id')
                                    ->where('is_active', 2)
                                    ->where('form_token', null)
                                    ->orWhere('form_token', "")
                                    ->get();
                }else {
		            if($logged_user->role_users_id != 1){
		                $allowedData = Employee::find($logged_user->id);
		                
		                if(is_array(json_decode($allowedData->allow_sub_loc, true))){
                            $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                        ->whereIn('sub_location_id', json_decode($allowedData->allow_sub_loc, true))
                                        ->where('is_active',1)
                                        ->where('exit_date',NULL)
                                        ->orWhere('exit_date','0000-00-00')
                                        ->get();
		                }else{
                            $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                        ->where('sub_location_id', $allowedData->allow_sub_loc)
                                        ->where('is_active',1)
                                        ->where('exit_date',NULL)
                                        ->orWhere('exit_date','0000-00-00')
                                        ->get();
		                }
		            }else{
                        $employees = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name')
                                    ->orderBy('company_id')
                                    ->where('is_active',1)
                                    ->where('exit_date',NULL)
                                    ->orWhere('exit_date','0000-00-00')
                                    ->get();
		            }
                }
                return datatables()->of($employees)
                    ->setRowId(function ($row)
                    {
                        return $row->id;
                    })
                    ->addColumn('staff_id', function ($row)
                    {
                        return isset($row->staff_id) ? $row->staff_id:'N/A';
                    })
                    ->addColumn('full_name', function ($row)
                    {
                        return isset($row->first_name) ? $row->first_name:'N/A';
                    })
                    ->addColumn('fname', function ($row)
                    {
                        return isset($row->fname) ? $row->fname:'N/A';
                    })
                    ->addColumn('mname', function ($row)
                    {
                        return isset($row->mname) ? $row->mname:'N/A';
                    })
                    ->addColumn('email', function ($row)
                    {
                        return isset($row->email) ? $row->email:'N/A';
                    })
                    ->addColumn('phone', function ($row)
                    {
                        return isset($row->contact_no) ? $row->contact_no:'N/A';
                    })
                    ->addColumn('alt_phone', function ($row)
                    {
                        return isset($row->alt_phone) ? $row->alt_phone:'N/A';
                    })
                    ->addColumn('address', function ($row)
                    {
                        return isset($row->address) ? $row->address:'N/A';
                    })
                    ->addColumn('city', function ($row)
                    {
                        return isset($row->city) ? $row->city:'N/A';
                    })
                    ->addColumn('zip', function ($row)
                    {
                        return isset($row->zip_code) ? $row->zip_code:'N/A';
                    })
                    ->addColumn('state', function ($row)
                    {
                        return isset($row->stateN->name) ? $row->stateN->name:'N/A';
                    })
                    ->addColumn('country', function ($row)
                    {
                        return isset($row->countryN->name) ? $row->countryN->name:'N/A';
                    })
                    ->addColumn('dob', function ($row)
                    {
                        return isset($row->date_of_birth) ? $row->date_of_birth:'N/A';
                    })
                    ->addColumn('gender', function ($row)
                    {
                        return isset($row->gender) ? $row->gender:'N/A';
                    })
                    ->addColumn('marital_status', function ($row)
                    {
                        return isset($row->marital_status) ? $row->marital_status:'N/A';
                    })
                    ->addColumn('manniversary', function ($row)
                    {
                        return isset($row->mAnniversary) ? $row->mAnniversary:'N/A';
                    })
                    ->addColumn('religion', function ($row)
                    {
                        return isset($row->religion) ? $row->religion:'N/A';
                    })
                    ->addColumn('blood_group', function ($row)
                    {
                        return isset($row->blood_grp) ? $row->blood_grp:'N/A';
                    })
                    ->addColumn('disability', function ($row)
                    {
                        return isset($row->disability) ? $row->disability == 0 ? 'No':'Yes':'N/A';
                    })
                    ->addColumn('sdisability', function ($row)
                    {
                        return isset($row->s_disability) ? $row->s_disability == "" ?  "N/A":$row->s_disability :  "N/A";
                    })
                    ->addColumn('client_group', function ($row)
                    {
                        return isset($row->client_group->name) ? $row->client_group->name:'N/A';
                    })
                    ->addColumn('company', function ($row)
                    {
                        return isset($row->company->company_name) ? $row->company->company_name:'N/A';
                    })
                    ->addColumn('location', function ($row)
                    {
                        return isset($row->location->location_name) ? $row->location->location_name:'N/A';
                    })
                    ->addColumn('sub_location', function ($row)
                    {
                        return isset($row->sub_location->name) ? $row->sub_location->name:'N/A';
                    })
                    ->addColumn('department', function ($row)
                    {
                        return isset($row->department->department_name) ? $row->department->department_name:'N/A';
                    })
                    ->addColumn('designation', function ($row)
                    {
                        return isset($row->designation->designation_name) ? $row->designation->designation_name:'N/A';
                    })
                    ->addColumn('status', function ($row)
                    {
                        return isset($row->status->status_title) ? $row->status->status_title:'N/A';
                    })
                    ->addColumn('office_shift', function ($row)
                    {
                        return isset($row->officeShift->shift_name) ? $row->officeShift->shift_name:'N/A';
                    })
                    ->addColumn('doj', function ($row)
                    {
                        return isset($row->joining_date) ? $row->joining_date:'N/A';
                    })
                    ->addColumn('dol', function ($row)
                    {
                        return isset($row->exit_date) ? $row->exit_date:'N/A';
                    })
                    ->addColumn('attendance_type', function ($row)
                    {
                        return isset($row->attendance_type) ? $row->attendance_type:'N/A';
                    })
                    ->addColumn('rem_leave', function ($row)
                    {
                        return isset($row->remaining_leave) ? $row->remaining_leave:'N/A';
                    })
                    ->addColumn('account_no', function ($row)
                    {
                        $accountDetails = EmployeeBankAccount::where('employee_id', $row->id)->latest()->first();
                        return isset($accountDetails->account_number) ? $accountDetails->account_number:'N/A';
                    })
                    ->addColumn('bank_name', function ($row)
                    {
                        $accountDetails = EmployeeBankAccount::where('employee_id', $row->id)->latest()->first();
                        return isset($accountDetails->bank_name) ? $accountDetails->bank_name:'N/A';
                    })
                    ->addColumn('bank_branch', function ($row)
                    {
                        $accountDetails = EmployeeBankAccount::where('employee_id', $row->id)->latest()->first();
                        return isset($accountDetails->bank_branch) ? $accountDetails->bank_branch:'N/A';
                    })
                    ->addColumn('account_name', function ($row)
                    {
                        $accountDetails = EmployeeBankAccount::where('employee_id', $row->id)->latest()->first();
                        return isset($accountDetails->account_title) ? $accountDetails->account_title:'N/A';
                    })
                    ->addColumn('ifsc_code', function ($row)
                    {
                        $accountDetails = EmployeeBankAccount::where('employee_id', $row->id)->latest()->first();
                        return isset($accountDetails->bank_code) ? $accountDetails->bank_code:'N/A';
                    })
                    ->addColumn('aadhar', function ($row)
                    {
                        return isset($row->aadhar) ? $row->aadhar:'N/A';
                    })
                    ->addColumn('pancard', function ($row)
                    {
                        return isset($row->pancard) ? $row->pancard:'N/A';
                    })
                    ->addColumn('action', function ($data)
                    {
                        $button = '<div style="display: flex;align-items: center;gap:10px">';
                        if($data->form_token != ""){
                            $button .= '<a class="btn-sm btn btn-info" href="' . route('employees.resendEmail', $data->id ) . '"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                        }
                        
                        if (auth()->user()->can('view-details-employee'))
                        {
                            $button .= '<a href="employees/' . $data->id . '"  class="edit btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="View Details"><i class="dripicons-preview"></i></button></a>';
                        }
                        if (auth()->user()->can('modify-details-employee'))
                        {
                            if ($data->role_users_id!=1) {
                                $button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Delete"><i class="dripicons-trash"></i></button>';
                            }
                        }
                        $button .= '</div>';

                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('employee2.index', compact('client_groups','roles'));
        }
        else
		{
			return response()->json(['success' => __('You are not authorized')]);
		}
	}
	
	public function index(Request $request)
	{
		$logged_user = auth()->user();
		
        if ($logged_user->can('view-details-employee'))
        {
            $client_groups = client_groups::select('id', 'name')->get();
            $roles = Role::where('id', '!=', 3)->where('is_active',1)->select('id', 'name')->get();

            return view('employee2.index', compact('client_groups','roles'));
        }
        else
		{
			return response()->json(['success' => __('You are not authorized')]);
		}
	}


	public function store(Request $request)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('store-details-employee'))
		{
			if (request()->ajax())
			{
				$validator = Validator::make($request->only('first_name', 'last_name', 'email', 'contact_no', 'role_users_id', 'company_id', 'department_id', 'designation_id','office_shift_id', 'client_grp_id'),
					[
						'first_name' => 'required',
						'last_name' => 'nullable',
                        'email'      => 'nullable|email|unique:users',
						'contact_no' => 'required|numeric|unique:users',
                        'client_grp_id' => 'required',
                        'company_id' => 'required',
						'department_id' => 'required',
						'designation_id' => 'required',
						'office_shift_id' => 'required',
						'role_users_id' => 'required',
					]
				);

				if ($validator->fails())
				{
					return response()->json(['errors' => $validator->errors()->all()]);
				}

				$data = [];
				$data['first_name'] = $request->first_name;
				$data['last_name'] = $request->last_name;
				$data['department_id'] = $request->department_id;
				$data ['designation_id'] = $request->designation_id;
				
			    $data['client_grp_id'] = $request->client_grp_id;
			    $data['company_id'] = $request->company_id;
			    $data['location_id'] = $request->location_id;
			    $data['sub_location_id'] = $request->sub_location_id;
			    
				if($request->role_users_id != 1 && $request->role_users_id != 2){
				    if(count($request->a_company_id) > 1){
				        $data['allow_company'] = json_encode($request->a_company_id);
				    }else{
				        $data['allow_company'] = $request->a_company_id[0];
				    }
				    
				    if(count($request->a_location_id) > 1){
				        $data['allow_loc'] = json_encode($request->a_location_id);
				    }else{
				        $data['allow_loc'] = $request->a_location_id[0];
				    }
				    
				    if(count($request->a_sub_location_id) > 1){
				        $data['allow_sub_loc'] = json_encode($request->a_sub_location_id);
				    }else{
				        $data['allow_sub_loc'] = $request->a_sub_location_id[0];
				    }
				}
				
				$data ['office_shift_id'] = $request->office_shift_id;

				$data['email'] = strtolower(trim($request->email));
				$data ['role_users_id'] = $request->role_users_id;
				$data['contact_no'] = $request->contact_no;
				$data['form_token'] = uniqid();
				$data['is_active'] = 0;

				$user = [];
				$user['first_name'] = $request->first_name;
				$user['last_name'] = $request->last_name;
				$user['username'] = strtolower($request->first_name);
				$user['email'] = strtolower(trim($request->email));
				$user['password'] = bcrypt($request->email);
				$user ['role_users_id'] = $request->role_users_id;
				$user['contact_no'] = $request->contact_no;
				$user['is_active'] = 0;

				// $photo = $request->profile_photo;
				// $file_name = null;

				// if (isset($photo))
				// {
				// 	$new_user = $request->username;
				// 	if ($photo->isValid())
				// 	{
				// 		$file_name = preg_replace('/\s+/', '', $new_user) . '_' . time() . '.' . $photo->getClientOriginalExtension();
				// 		$photo->storeAs('profile_photos', $file_name);
				// 		$user['profile_photo'] = $file_name;
				// 	}
				// }

				DB::beginTransaction();
				try
				{
					$created_user = User::create($user);
					$created_user->syncRoles($request->role_users_id); //new

					$data['id'] = $created_user->id;
					
					$created_emp = employee::create($data);
					$uid = $created_emp->id;
					
					$to = $user['email'];
					$subject = "Your Registration In Our Company";
					$header = "From: no-reply@yashritik.co.in";
				    $formLink = "https://yashritik.co.in/crm/employee/form/" . $uid;
					$msg = "Please click on the link below to fill your registration form.\n";
                    $msg .= $formLink;
                    
                    mail($to, $subject, $msg, $header);
                    
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

				return response()->json(['success' => __('Data Added successfully.')]);
			}
		}

		return response()->json(['success' => __('You are not authorized')]);
	}

	public function show(Employee $employee)
	{
		if (auth()->user()->can('view-details-employee'))
		{
			$client_groups = client_groups::select('id', 'name')->get();
			$companies = Company::select('id', 'company_name')
			    ->where('client_grp_id', $employee->client_grp_id)
				->get();
			$locations = location::select('id', 'location_name')
			    ->where('company_id', $employee->company_id)
				->get();
			$sub_locations = sub_locations::select('id', 'name')
			    ->where('location_id', $employee->location_id)
				->get();
			$departments = department::select('id', 'department_name')
				->where('company_id', $employee->company_id)
				->orWhere(function ($query) {
                    $query->where('is_common', 1);
                })
				->get();

			$designations = designation::select('id', 'designation_name')->get();
    		$office_shifts = office_shift::select('id', 'shift_name')->get();
    			
    		$components = salary_components::select('*')->get();
    		$salary = EmployeeSalary::select('*')->where('employee_id', $employee->id)->where('isPaid', 0)->orWhere('isPaid', null)->latest()->get();

			$statuses = status::select('id', 'status_title')->get();
			// $roles = Role::select('id', 'name')->get();
			$countries = DB::table('countries')->select('id', 'name')->get();
			$document_types = DocumentType::select('id', 'document_type')->get();

			$education_levels = QualificationEducationLevel::select('id', 'name')->get();
			$language_skills = QualificationLanguage::select('id', 'name')->get();
			$general_skills = QualificationSkill::select('id', 'name')->get();
			
			$paymentMethods = PaymentMethod::select("*")->get();
			
			$leave_types = LeaveType::select('id', 'leave_type')->get();

			$roles = Role::where('id', '!=', 3)->where('is_active',1)->select('id', 'name')->get(); //--new--
            
			return view('employee2.dashboard', compact('employee', 'countries', 'client_groups', 'companies', 'locations', 'sub_locations',
				'departments', 'designations', 'statuses', 'office_shifts', 'document_types', 'education_levels', 'language_skills', 'general_skills','roles', 'components', 'salary', 'paymentMethods', 'leave_types'));
		}else
		{
			return response()->json(['success' => __('You are not authorized')]);
		}
	}


	public function destroy($id)
	{
		if (!env('USER_VERIFIED'))
		{
			return response()->json(['error' => 'This feature is disabled for demo!']);
		}
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
			DB::beginTransaction();
			try
			{
				Employee::whereId($id)->delete();
				$this->unlink($id);
				User::whereId($id)->delete();

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

			return response()->json(['success' => __('Data is successfully deleted')]);
		}

		return response()->json(['success' => __('You are not authorized')]);
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

	public function delete_by_selection(Request $request)
	{
		if (!env('USER_VERIFIED'))
		{
			return response()->json(['error' => 'This feature is disabled for demo!']);
		}
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
			$employee_id = $request['employeeIdArray'];

			$user = User::whereIntegerInRaw('id', $employee_id)->where('role_users_id','!=',1);

			if ($user->delete())
			{
				return response()->json(['success' => __('Data is successfully deleted')]);
			}
		}

		return response()->json(['success' => __('You are not authorized')]);
	}
	
	public function infoUpdate(Request $request, $employee)
	{
		// return response()->json($request->attendance_type);

		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
			if (request()->ajax())
			{
				$validator = Validator::make($request->only('first_name', 'staff_id', 'email', 'contact_no', 'date_of_birth', 'gender', 'alt_phone', 'client_grp_id', 'sub_location_id',
					'username', 'role_users_id', 'company_id', 'department_id', 'designation_id', 'office_shift_id', 'location_id', 'status_id',
					'marital_status', 'joining_date', 'permission_role_id', 'address', 'city', 'state', 'country', 'zip_code','attendance_type','total_leave', 'pancard', 'aadhar'
				),
					[
						'first_name' => 'required',
                        'username' => 'required',
                        'email'      => 'nullable|email|unique:users,email,' . $employee,
						'contact_no' => 'required|numeric|unique:users,contact_no,' . $employee,
						'date_of_birth' => ['required', new Above18YearsOldRule],
                        'client_grp_id' => 'required',
                        'company_id' => 'required',
                        'location_id' => 'required',
                        'sub_location_id' => 'required',
                        'department_id' => 'required',
                        'designation_id' => 'required',
                        'office_shift_id' => 'required',
						'role_users_id' => 'required',
						'attendance_type' => 'required',
						'total_leave' => 'numeric|min:0',
						'joining_date' => 'required',
						'exit_date' => 'nullable',
						'pancard' => 'required|min:10|max:10|regex:/^[A-Za-z]{5}\d{4}[A-Za-z]{1}$/',
						'aadhar' => 'required|digits:12',
					]
				);

				if ($validator->fails())
				{
					return response()->json(['errors' => $validator->errors()->all()]);
				}

				$data = [];
				$data['first_name'] = $request->first_name;
				$data['last_name'] = $request->last_name;
        		$data['fname'] = $request->fname;
        		$data['mname'] = $request->mname;
				$data['date_of_birth'] = $request->date_of_birth;
				$data['gender'] = $request->gender;
				$data['department_id'] = $request->department_id;
				$data['client_grp_id'] = $request->client_grp_id;
				$data['company_id'] = $request->company_id;
				$data['location_id'] = $request->location_id;
				$data['sub_location_id'] = $request->sub_location_id;
				$data['wCountry'] = $request->wcountry;
				$data['wState'] = $request->wstate;
				$data ['designation_id'] = $request->designation_id;
				$data ['office_shift_id'] = $request->office_shift_id;
				$data['status_id'] = $request->status_id;
				$data ['marital_status'] = $request->marital_status;
        		$data['mAnniversary'] = date("Y-m-d", strtotime($request->mAnniversary));
				$data ['religion'] = $request->religion;
				$data ['blood_grp'] = $request->blood_grp;
        		$data['disability'] = $request->disability;
        		$data['s_disability'] = $request->s_disability;
        		
        		$data['employee_type'] = $request->employee_type;
        		
        		$data['aadhar'] = $request->aadhar;
        		$data['pancard'] = $request->pancard;
				
				if ($request->joining_date)
				{
					$data ['joining_date'] = $request->joining_date;
				}

				if ($request->exit_date){
					$data['exit_date'] = $request->exit_date;
				}
                // else {
                //     $data['exit_date'] = NULL;
                // }

				$data ['alt_phone'] = $request->alt_phone;
				
				$data ['address'] = $request->address;
				$data ['address2'] = $request->address2;
				$data ['city'] = $request->city;
				$data['state'] = $request->state;
				$data ['country'] = $request->country;
				$data ['zip_code'] = $request->zip_code;


				$data['email'] = strtolower(trim($request->email));
				$data['official_email'] = strtolower(trim($request->off_email));
				
				$data['uan'] = $request->uan;
				$data['pf_no'] = $request->pf_no;
				$data['esic_no'] = $request->esic_no;
				$data['payable_type'] = $request->payable_type;
				
				$data ['role_users_id'] = $request->role_users_id;
				$data['contact_no'] = $request->contact_no;
				$data['attendance_type'] = $request->attendance_type;

				//Leave Calculation
				$employee_leave_info = Employee::find($employee);
				if ($employee_leave_info->total_leave==0) {
					$data['total_leave'] = $request->total_leave;
					$data['remaining_leave'] = $request->total_leave;
				}
				elseif ($request->total_leave > $employee_leave_info->total_leave) {
					$data['total_leave'] = $request->total_leave;
					$data['remaining_leave'] = $request->remaining_leave + ($request->total_leave - $employee_leave_info->total_leave);
				}
				elseif ($request->total_leave < $employee_leave_info->total_leave) {
					$data['total_leave'] = $request->total_leave;
					$data['remaining_leave'] = $request->remaining_leave - ($employee_leave_info->total_leave - $request->total_leave);
				}else {
					$data['total_leave'] = $request->total_leave;
					$data['remaining_leave'] = $employee_leave_info->remaining_leave;
				}

				$user = [];
				$user['first_name'] = $request->first_name;
				$user['last_name'] = $request->last_name;
				$user['email'] = strtolower(trim($request->email));
				//$user['password'] = bcrypt($request->password);
				$user ['role_users_id'] = $request->role_users_id;
				$user['contact_no'] = $request->contact_no;

                if($request->approval_stat){
    				if($request->approval_stat == 1){
    				    $to = $user['email'];
    					$subject = "Congratulations You're Approved In Your Company. Please set your password!";
    					$header = "From: no-reply@yashritik.co.in";
    				    $formLink = "https://yashritik.co.in/crm/password/reset";
    					$msg = "Please click on the link below to set your password.\n";
                        $msg .= $formLink;
                        
                        mail($to, $subject, $msg, $header); 
			            $data['is_active'] = 1;
			            $data['form_token'] = null;
    				    $user['is_active'] = 1;
    				    
    				    $last_employee = employee::latest()->skip(1)->first();
    				    if($last_employee){
                            $staff_id = $last_employee->staff_id + 1;
    				    }else{
                            $staff_id = 10001;
    				    }
    				    $data['staff_id'] = $staff_id;
    				    
				        $user['username'] = $staff_id;
    				    
    				}else if($request->approval_stat == 0){
    				    $to = $user['email'];
    					$subject = "Your Application Has Been Rejected From Our Company";
    					$header = "From: no-reply@yashritik.co.in";
    					$msg = "Your Job Application Has Been Rejected.\n";
    
                        mail($to, $subject, $msg, $header); 
                        
			            $data['is_active'] = 0;
    				    $user['is_active'] = 0;
    				}
                }
                
                if ($request->exit_date){
			        $data['is_active'] = 0;
			        $user['is_active'] = 0;
                }

				DB::beginTransaction();
				try
				{
					User::whereId($employee)->update($user);
					employee::find($employee)->update($data);
                    
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    // Update or create Aadhar Card document
                    $document = EmployeeDocument::updateOrCreate(
                        [
                            'employee_id' => $employee,
                            'document_type_id' => 1,
                            'document_title' => 'Aadhar Card'
                        ],
                        [
                            'description' => $data['aadhar']
                        ]
                    );
                    
                    // Update or create Pan Card document
                    $document = EmployeeDocument::updateOrCreate(
                        [
                            'employee_id' => $employee,
                            'document_type_id' => 2,
                            'document_title' => 'Pan Card'
                        ],
                        [
                            'description' => strtoupper($data['pancard'])
                        ]
                    );

                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');

					$usertest = User::find($employee); //--new--
					$usertest->syncRoles($data['role_users_id']); //--new--

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

				return response()->json(['success' => __('Data Added successfully.'), 'remaining_leave' => $data['remaining_leave']]);
			}
		}

		return response()->json(['success' => __('You are not authorized')]);
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
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee') || $logged_user->id == $employee)
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

		return response()->json(['success' => __('You are not authorized')]);
	}

	public function setSalary(Employee $employee)
	{
		$logged_user = auth()->user();
		if ($logged_user->can('modify-details-employee'))
		{
			return view('employee.salary.index', compact('employee'));
		}

		return response()->json(['success' => __('You are not authorized')]);
	}

	public function storeSalary(Request $request, $employee)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
		    $request = $request->except(['_token', 'submit_salary']);
		    // Filter keys with "d" prefix
            $deduction = array_filter($request, function($key) {
                return strpos($key, 'd_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            // Filter keys with "a" prefix
            $addition = array_filter($request, function($key) {
                return strpos($key, 'a_') === 0;
            }, ARRAY_FILTER_USE_KEY);

            $addition = json_encode($addition);
            $deduction = json_encode($deduction);

			DB::beginTransaction();
			try
			{
				// Find the existing record based on the employee_id
                $employeeSalary = EmployeeSalary::where('employee_id', $employee)->first();
                
                // Check if the existing record exists and its isPaid value is 0 or null
                if ($employeeSalary && ($employeeSalary->isPaid === 0 || $employeeSalary->isPaid === null)) {
                    // Update the existing record
                    $employeeSalary->update(['addition' => $addition, 'deduction' => $deduction]);
                } else {
                    // Create a new record
                    EmployeeSalary::create([
                        'employee_id' => $employee,
                        'addition' => $addition,
                        'deduction' => $deduction,
                        'isPaid' => 0 // Assuming you want to set isPaid to 0 for new records
                    ]);
                }


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

			return redirect()->back();
		}

		return response()->json(['error' => __('You are not authorized')]);
	}
	
	public function attendanceD(Request $request, $employee)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
		    $request = $request->except(['_token']);
		    
		    $punchInData = array();
            $punchOutData = array();
        
            // Loop through the punch-in data
            if (isset($request["punch_in_name"])) {
                $punchInNames = $_POST["punch_in_name"];
                $punchInMinValues = $_POST["punch_in_min_value"];
                $punchInMaxValues = $_POST["punch_in_max_value"];
                $punchInAlloweds = $_POST["punch_in_allowed"];
                $punchInPayables = $_POST["punch_in_payable"];
        
                for ($i = 0; $i < count($punchInNames); $i++) {
                    $punchInData[] = array(
                        "name" => $punchInNames[$i],
                        "min_value" => $punchInMinValues[$i],
                        "max_value" => $punchInMaxValues[$i],
                        "allowed" => $punchInAlloweds[$i],
                        "payable" => $punchInPayables[$i]
                    );
                }
            }
        
            // Loop through the punch-out data
            if (isset($_POST["punch_out_min_value"])) {
                $punchOutMinValues = $_POST["punch_out_min_value"];
                $punchOutMaxValues = $_POST["punch_out_max_value"];
                $punchOutAlloweds = $_POST["punch_out_allowed"];
                $punchOutPayables = $_POST["punch_out_payable"];
        
                for ($i = 0; $i < count($punchOutMinValues); $i++) {
                    $punchOutData[] = array(
                        "name" => $punchInNames[$i],
                        "min_value" => $punchOutMinValues[$i],
                        "max_value" => $punchOutMaxValues[$i],
                        "allowed" => $punchOutAlloweds[$i],
                        "payable" => $punchOutPayables[$i]
                    );
                }
            }
        
            // Combine punch-in and punch-out data into a single array
            $punchData = array("punch_in" => $punchInData, "punch_out" => $punchOutData);
        
            // Convert the array to JSON for further use
            $data = json_encode($punchData);
        
			DB::beginTransaction();
			try
			{
				EmployeeSalary::updateOrCreate(['employee_id' => $employee], ['adeduction_settings' => $data]);
				
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

			return redirect()->back();
		}

		return response()->json(['error' => __('You are not authorized')]);
	}
	
	public function overtimeStore(Request $request, $employee)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee'))
		{
		    $request = $request->except(['_token']);
		    
		    $data = json_encode($request);
		    
			DB::beginTransaction();
			try
			{
				EmployeeSalary::updateOrCreate(['employee_id' => $employee], ['overtime_settings' => $data]);
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

			return redirect()->back();
		}

		return response()->json(['error' => __('You are not authorized')]);
	}

    public function employeesPensionUpdate(Request $request, $employee)
    {
        //return response()->json('ok');
        $logged_user = auth()->user();

		if ($logged_user->can('modify-details-employee')){

            $validator = Validator::make($request->only('pension_type', 'pension_amount'),[
					'pension_type'  => 'required',
					'pension_amount'=> 'required|numeric',
				]
			);


			if ($validator->fails()){
				return response()->json(['errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			try
			{
				Employee::updateOrCreate(['id' => $employee], [
					'pension_type' => $request->pension_type,
					'pension_amount' => $request->pension_amount]);
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

			return response()->json(['success' => __('Data Added successfully.')]);
        }
        return response()->json(['success' => __('You are not authorized')]);

    }

	public function import()
	{

		if (auth()->user()->can('import-employee'))
		{
			return view('employee2.import');
		}

		return abort(404, __('You are not authorized'));
	}

	public function importPost(Request $request)
	{
	    if($request->update_data == 0){
            try
    		{
    			Excel::queueImport(new UsersImport(), request()->file('file'));
    			
    			$todayLogs = Logs::whereDate('created_at', Carbon::today())->get();
    			if ($todayLogs->isEmpty()) {
        			$csvData = implode(',', array_keys(json_decode($todayLogs->first()->data, true))) . "\n";

                    foreach ($todayLogs as $log) {
                        $rowData = json_decode($log->data, true);
                        $csvData .= implode(',', $rowData) . "\n";
                    }
                    
                    $to = 'yash190068@gmail.com'; // Replace with actual recipient email
                    
                    $subject = 'Today\'s Log Data Report';
                    $headers = 'From: info@investation.team' . "\r\n" .
                               'Reply-To: info@investation.team' . "\r\n" .
                               'X-Mailer: PHP/' . phpversion();
                    
                    // Attach the CSV data to the email
                    $attachmentName = 'log_data.csv';
                    $attachmentContent = $csvData;
                    $attachmentHeaders = "Content-type: text/csv";
                    
                    // Combine attachment with other headers and content
                    $attachment = $attachmentHeaders . "\r\n" .
                                  "Content-Disposition: attachment; filename=\"" . $attachmentName . "\"\r\n" .
                                  $attachmentContent;
                    
                    // Sending the email using mail() function
                    mail($to, $subject, "", $headers, "-f info@investation.team", $attachment);
    			}
    		} catch (ValidationException $e)
    		{
    			$failures = $e->failures();
    
    			return view('employee2.importError', compact('failures'));
    		}
    		
		    $this->setSuccessMessage(__('Imported Successfully'));
        }else{
            try
    		{
    			Excel::queueImport(new UsersBulkUpdate(), request()->file('file'));
    		} catch (ValidationException $e)
    		{
    			$failures = $e->failures();
    
    			return view('employee2.importError', compact('failures'));
    		}
    		
		    $this->setSuccessMessage(__('Data Updated Successfully'));
        }
		if (!env('USER_VERIFIED'))
		{
            $this->setErrorMessage('This feature is disabled for demo!');
            return redirect()->back();
		}

		return back();
	}

	public function employeePDF($id)
	{
		$employee = Employee::with('user:id,profile_photo,username','company:id,company_name','department:id,department_name', 'designation:id,designation_name','officeShift:id,shift_name','role:id,name')
							->where('id',$id)
							->first()
							->toArray();

		PDF::setOptions(['dpi' => 10, 'defaultFont' => 'sans-serif','tempDir'=>storage_path('temp')]);
        $pdf = PDF::loadView('employee.pdf',$employee);
        return $pdf->stream();
	}
	
	public function employeeResend($id)
	{
	    $employee = Employee::select('email')->where('id', $id)->first();
	    
	    $to = $employee->email;
		$subject = "Your Registration In Our Company";
		$header = "From: no-reply@yashritik.co.in";
	    $formLink = "https://yashritik.co.in/crm/employee/form/" . $id;
		$msg = "Please click on the link below to fill your registration form.\n";
        $msg .= $formLink;
        
        if(mail($to, $subject, $msg, $header)){
            $this->setSuccessMessage(__('Mail Sent Successfully'));
        }else{
            $this->setErrorMessage('Error while sending mail!');
        }
        
        return redirect()->back();
	}

}
