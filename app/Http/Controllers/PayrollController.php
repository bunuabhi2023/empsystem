<?php

namespace App\Http\Controllers;

use App\company;
use App\client_groups;
use App\salary_components;
use App\Employee;
use App\EmployeeSalary;
use App\FinanceBankCash;
use App\FinanceExpense;
use App\FinanceTransaction;
use App\Http\traits\TotalSalaryTrait;
use App\Payslip;
use App\SalaryLoan;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

use App\Imports\ManualImportPayroll;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use App\Http\traits\MonthlyWorkedHours;
use App\SalaryBasic;

class PayrollController extends Controller {

	use TotalSalaryTrait;
	use MonthlyWorkedHours;

	public function index(Request $request)
	{
        $client_groups = client_groups::select('id', 'name')->get();
        $components = salary_components::select('*')->get();
        
        // ADMIN
		$logged_user = auth()->user();

		$selected_date = empty($request->filter_month_year) ? now()->format('F-Y') : $request->filter_month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $selected_date));
		$last_date = date('Y-m-d', strtotime('last day of ' . $selected_date));

		if ($logged_user->can('view-paylist'))
		{
			return view('salary.pay_list.index', compact('client_groups', 'components'));
		}

		return abort('403', __('You are not authorized'));
	}
	
	public function getEmployees(Request $request){
	    $client_groups = client_groups::select('id', 'name')->get();
	    $components = salary_components::select('*')->get();
        
        $from = $request->salary_from;
        $to = $request->salary_to;
        
        if($request->client_grp_id){
	        $employees = Employee::where('client_grp_id', $request->client_grp_id)
	            ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 0);
                    $query->orWhere('isPaid', null);
                })
                ->with('payroll')
                ->get();
                
        }else if($request->client_grp_id && $request->company_id){
	        $employees = Employee::where('company_id', $request->company_id)
	        ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 0);
                    $query->orWhere('isPaid', null);
                })
                ->with('payroll')
                ->get();
        }else if($request->client_grp_id && $request->company_id && $request->location_id){
	        $employees = Employee::where('location_id', $request->location_id)
	        ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 0);
                    $query->orWhere('isPaid', null);
                })
                ->with('payroll')
                ->get();
        }else if($request->client_grp_id && $request->company_id && $request->location_id && $request->sub_location_id){
	        $employees = Employee::where('sub_location_id', $request->sub_location_id)
	        ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 0);
                    $query->orWhere('isPaid', null);
                })
                ->with('payroll')
                ->get();
        }
        
        $client_groupS = $request->client_grp_id;
        $company = $request->company_id;
        $location_id = $request->location_id;
        $sub_location_id = $request->sub_location_id;
        
        
        // ADMIN
		$logged_user = auth()->user();
		

		if ($logged_user->can('view-paylist'))
		{
			return view('salary.pay_list.index', compact('client_groups', 'employees', 'client_groupS', 'company', 'location_id', 'sub_location_id', 'from', 'to', 'components'));
		}

		return abort('403', __('You are not authorized'));
	}
	
	public function manual()
	{

		if (auth()->user()->can('view-payslip'))
		{
			return view('salary.pay_list.manual');
		}

		return abort(404, __('You are not authorized'));
	}

	public function manualPost(Request $request)
	{
        try
		{
			Excel::queueImport(new ManualImportPayroll(), request()->file('file'));
		} catch (ValidationException $e)
		{
			$failures = $e->failures();

			return view('employee2.importError', compact('failures'));
		}
		
	    $this->setSuccessMessage(__('Imported Successfully'));
		return back();
	}

	public function paySlip(Request $request)
	{
		$month_year = $request->filter_month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date = date('Y-m-d', strtotime('last day of ' . $month_year));

		$employee = Employee::with(['salaryBasic' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'allowances' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'commissions'=> function ($query) use ($first_date)
            {
                $query->where('first_date', $first_date);
            },
			'loans'=> function ($query) use ($first_date)
            {
                $query->where('first_date','<=', $first_date)
                ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
            },
			'deductions'=> function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'otherPayments'=> function ($query) use ($first_date)
			{
				$query->where('first_date', $first_date);
			},
			'overtimes'=> function ($query) use ($month_year)
			{
				$query->where('month_year', $month_year);
			},
			'designation', 'department', 'user',
			'employeeAttendance' => function ($query) use ($first_date, $last_date){
				$query->whereBetween('attendance_date', [$first_date, $last_date]);
			}])
			->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount', 'designation_id', 'department_id', 'joining_date')
			->findOrFail($request->id);

		//payslip_type && salary_basic
		foreach ($employee->salaryBasic as $salaryBasic) {
			if($salaryBasic->first_date <= $first_date){
				$basic_salary = $salaryBasic->basic_salary;
				$payslip_type = $salaryBasic->payslip_type;
			}
		}

        //Pension Amount
        if ($employee->pension_type=="percentage") {
            $pension_amount =  ($basic_salary * $employee->pension_amount) /100.00;
        } else {
            $pension_amount = $employee->pension_amount;
        }


        $type          = "getArray";
        $allowances    = $this->allowances($employee, $first_date, $type);
        $deductions    = $this->deductions($employee, $first_date, $type);
		$data = [];
		$data['basic_salary'] = $basic_salary;
		$data['basic_total']  = $basic_salary;
		$data['allowances']   = $allowances;
		$data['commissions']  = $employee->commissions;
		$data['loans']        = $employee->loans;
		$data['deductions']   = $deductions;
		$data['overtimes']    = $employee->overtimes;
		$data['other_payments'] = $employee->otherPayments;
		$data['pension_type']   = $employee->pension_type;
        $data['pension_amount'] = $pension_amount;

		$data['employee_id']          = $employee->id;
		$data['employee_full_name']   = $employee->full_name;
		$data['employee_designation'] = $employee->designation->designation_name ?? '';
		$data['employee_department']  = $employee->department->department_name ?? '';
		$data['employee_join_date']   = $employee->joining_date;
		$data['employee_username']    = $employee->user->username;
		$data['employee_pp']          = $employee->user->profile_photo ?? '';

		$data['payslip_type'] = $payslip_type;

		if ($payslip_type == 'Hourly')
		{
			$total = 0;
			$total_hours_worked = $this->totalWorkedHours($employee);
			$data['monthly_worked_hours'] = $total_hours_worked;
			//formatting in hour:min and separating them
			sscanf($total_hours_worked, '%d:%d', $hour, $min);
			//converting in minute
			$total += $hour * 60 + $min;

			$data['monthly_worked_amount'] = ($basic_salary / 60) * $total;

			$data['basic_total'] = $data['monthly_worked_amount'];
		}

		return response()->json(['data' => $data]);
	}

	public function paySlipGenerate(Request $request)
	{

		$month_year = $request->filter_month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date = date('Y-m-d', strtotime('last day of ' . $month_year));

		$employee = Employee::with(['salaryBasic' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'allowances' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'commissions'=> function ($query) use ($first_date)
            {
                $query->where('first_date', $first_date);
            },
			'loans'=> function ($query) use ($first_date)
            {
                $query->where('first_date','<=', $first_date)
                ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
            },
			'deductions' => function ($query)
			{
				$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
			},
			'otherPayments'=> function ($query) use ($first_date)
			{
				$query->where('first_date', $first_date);
			},
			'overtimes'=> function ($query) use ($month_year)
			{
				$query->where('month_year', $month_year);
			},
			'designation', 'department', 'user',
			'employeeAttendance' => function ($query) use ($first_date, $last_date){
				$query->whereBetween('attendance_date', [$first_date, $last_date]);
			}])
			->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type', 'designation_id', 'department_id', 'joining_date','pension_type','pension_amount')
			->findOrFail($request->id);


		//payslip_type & basic_salary
		foreach ($employee->salaryBasic as $salaryBasic) {
			if($salaryBasic->first_date <= $first_date)
			{
				$basic_salary = $salaryBasic->basic_salary;
				$payslip_type = $salaryBasic->payslip_type;
			}
		}

        //Pension Amount
        if ($employee->pension_type=="percentage") {
            $pension_amount =  ($basic_salary * $employee->pension_amount) /100;
        } else {
            $pension_amount = $employee->pension_amount;
        }


		$type              = "getAmount";
        $allowance_amount  = $this->allowances($employee, $first_date, $type);
        $deduction_amount  = $this->deductions($employee, $first_date, $type);

		$data = [];
		$data['employee']         = $employee->id;
		$data['basic_salary']     = $basic_salary;
		$data['total_allowance']  = $allowance_amount;
		$data['total_commission'] = $employee->commissions->sum('commission_amount');
		$data['monthly_payable']  = $employee->loans->sum('monthly_payable');
		$data['amount_remaining'] = $employee->loans->sum('amount_remaining');
		$data['total_deduction']  = $deduction_amount;
		$data['total_overtime']   = $employee->overtimes->sum('overtime_amount');
		$data['total_other_payment'] = $employee->otherPayments->sum('other_payment_amount');
		$data['payslip_type']     = $payslip_type;
		$data['pension_amount']   = $pension_amount;

		if ($payslip_type == 'Monthly')
		{
			// $data['total_salary'] = $this->totalSalary($employee); //will be deleted----
// 			$data['total_salary'] = $this->totalSalary($employee, $payslip_type, $basic_salary, $allowance_amount, $deduction_amount, $pension_amount);
			
			$totalWorkDays = DB::table('attendances')
            ->select(DB::raw('COUNT(DISTINCT DATE(attendance_date)) as total_work_days'))
            ->where('employee_id', $employee->id)
            ->whereNotNull('clock_in')
            ->whereNotNull('clock_out')
            ->where('attendance_status', 'present')
            ->value('total_work_days');
            
            $data['worked_days'] = $totalWorkDays;
            
            
            $currentMonth = date('m');
            $currentYear = date('Y');
            $totalDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
            
            $data['total_salary'] = number_format(($basic_salary/$totalDays*$totalWorkDays)+($allowance_amount/$totalDays*$totalWorkDays)+($employee->otherPayments->sum('other_payment_amount')/$totalDays*$totalWorkDays), 2);
		} else
		{
			$total = 0;
			$total_hours = $this->totalWorkedHours($employee);
			sscanf($total_hours, '%d:%d', $hour, $min);
			//converting in minute
			$total += $hour * 60 + $min;
			$data['total_hours'] = $total_hours;
			$data['worked_amount'] = ($data['basic_salary'] / 60) * $total;
			$data['total_salary'] = $this->totalSalary($employee, $payslip_type, $basic_salary, $allowance_amount, $deduction_amount, $pension_amount, $total);
		}
		return response()->json(['data' => $data]);
	}


	public function payEmployee($id, Request $request)
	{
		$logged_user = auth()->user();

		if ($logged_user->can('make-payment'))
		{
			$first_date = date('Y-m-d', strtotime('first day of ' . $request->month_year));

			DB::beginTransaction();
				try
				{
					$employee = Employee::with(['allowances' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'commissions'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
                        'loans'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date','<=', $first_date)
                            ->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
                        },
                        'deductions' => function ($query)
						{
							$query->orderByRaw('DATE_FORMAT(first_date, "%y-%m")');
						},
						'otherPayments'=> function ($query) use ($first_date)
                        {
                            $query->where('first_date', $first_date);
                        },
						'overtimes'=> function ($query) use ($first_date)
						{
							$query->where('first_date', $first_date);
						}])
						->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type','pension_type','pension_amount','company_id')
						->findOrFail($id);


                    $type          = "getArray";
                    $allowances    = $this->allowances($employee, $first_date, $type); //getArray
                    $deductions    = $this->deductions($employee, $first_date, $type);

					$data = [];
					$data['payslip_key']    = Str::random('20');
					$data['payslip_number'] = mt_rand(1000000000,9999999999);
					$data['payment_type']   = $request->payslip_type;
					$data['basic_salary']   = $request->basic_salary;
					$data['allowances']     = $allowances;
					$data['commissions']    = $employee->commissions;
					$data['loans']          = $employee->loans;
					$data['deductions']     = $deductions;
					$data['overtimes']      = $employee->overtimes;
					$data['other_payments'] = $employee->otherPayments;
					$data['month_year']     = $request->month_year;
					$data['net_salary']     = $request->net_salary;
					$data['status']         = 1;
					$data['employee_id']    = $employee->id;
					$data['hours_worked']   = $request->worked_hours;
					$data['pension_type']   = $employee->pension_type;
					$data['pension_amount'] = $request->pension_amount;
					$data['company_id']     = $employee->company_id;

					if ($data['payment_type'] == NULL) { //No Need This Line
						return response()->json(['payment_type_error' => __('Please select a payslip-type for this employee.')]);
					}

					$account_balance = DB::table('finance_bank_cashes')->where('id', config('variable.account_id'))->pluck('account_balance')->first();

					if ((int)$account_balance < (int)$request->net_salary)
					{
						return response()->json(['error' => 'requested balance is less then available balance']);
					}

					$new_balance = (int)$account_balance - (int)$request->net_salary;

					$finance_data = [];

					$finance_data['account_id'] = config('variable.account_id');
					$finance_data['amount'] = $request->net_salary;
					$finance_data ['expense_date'] = now()->format(env('Date_Format'));
					$finance_data ['expense_reference'] = trans('file.Payroll');


					FinanceBankCash::whereId($finance_data['account_id'])->update(['account_balance' => $new_balance]);

					$Expense = FinanceTransaction::create($finance_data);

					$finance_data['id'] = $Expense->id;

					FinanceExpense::create($finance_data);

					if ($employee->loans)
					{
						foreach ($employee->loans as $loan)
						{
							if($loan->time_remaining == '0')
							{
								$amount_remaining = 0;
								$time_remaining   = 0;
								$monthly_payable  = 0;
							}
							else
							{
								$amount_remaining = (int) $loan->amount_remaining - (int) $loan->monthly_payable;
								$time_remaining   = (int) $loan->time_remaining - 1;
								$monthly_payable  = $amount_remaining !=0 ? $loan->monthly_payable : 0;
							}
							SalaryLoan::whereId($loan->id)->update(['amount_remaining' => $amount_remaining, 'time_remaining' => $time_remaining,
								'monthly_payable' => $monthly_payable]);
						}
						$employee_loan = Employee::with('loans:id,employee_id,loan_title,loan_amount,time_remaining,amount_remaining,monthly_payable')
							->select('id', 'first_name', 'last_name', 'basic_salary', 'payslip_type')
							->findOrFail($id);
						$data['loans'] = $employee_loan->loans;
					}
					Payslip::create($data);

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


	//--- Updated ----
	public function payBulk(Request $request)
	{
		$logged_user = auth()->user();
		if ($logged_user->can('make-bulk_payment'))
		{
			$ref = $request->ref_no;
			$period = $request->period;
			$employee_ids = $request->employee_ids;
			
			$employee_ids_array = explode(',', $employee_ids);
            $employees = EmployeeSalary::whereIn('employee_id', $employee_ids_array)->get();
        
            // Update the employees
            foreach ($employees as $employee) {
                // Perform the required updates here
                // Example: Update the employee's name
                $employee->ref_no = $ref;
                $employee->period = $period;
                $employee->isPaid = 1;
                // Update other fields as needed
        
                // Save the changes
                if($employee->save()){
                    return redirect()->route('payment_history.index');
                }else{
                    return redirect()->back();
                }
            }
		}

		return response()->json(['error' => __('Error')]);
	}

    protected function allowances($employee, $first_date, $type)
    {
        if ($type=="getArray") {
            if (!$employee->allowances->isEmpty()) {
                foreach($employee->allowances as $item) {
                    if($item->first_date <= $first_date){
                        $allowances = array();
                        foreach($employee->allowances as $key => $value) {
                            if($value->first_date <= $first_date){
                                //$allowances = array();
                                if ($item->first_date == $value->first_date) {
                                    $allowances[] =  $employee->allowances[$key];
                                }
                            }
                        }

                    }
                }
            }else {
                $allowances = [];
            }
            return $allowances;
        }
        elseif ($type=="getAmount") {
            $allowance_amount = 0;
            if (!$employee->allowances->isEmpty()) {
                foreach($employee->allowances as $item) {
                    if($item->first_date <= $first_date){
                        // $allowance_amount = SalaryAllowance::where('month_year',$item->month_year)->where('employee_id',$item->employee_id)->sum('allowance_amount');
                        $allowance_amount = 0;
                        foreach($employee->allowances as $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $allowance_amount += $value->allowance_amount;
                                }
                            }
                        }
                    }
                }
            }

            return $allowance_amount;
        }

    }

    protected function deductions($employee, $first_date ,$type)
    {
        if ($type=="getAmount") {
            $deduction_amount = 0;
            if (!$employee->deductions->isEmpty()) {
                foreach($employee->deductions as $item) {
                    if($item->first_date <= $first_date){
                        $deduction_amount = 0;
                        foreach($employee->deductions as $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $deduction_amount += $value->deduction_amount;
                                }
                            }
                        }
                    }
                }
            }
            return $deduction_amount;
        }
        elseif($type=="getArray") {
            if (!$employee->deductions->isEmpty()) {
                foreach($employee->deductions as $item) {
                    if($item->first_date <= $first_date){
                        $deductions = array();
                        foreach($employee->deductions as $key => $value) {
                            if($value->first_date <= $first_date){
                                if ($item->first_date == $value->first_date) {
                                    $deductions[] =  $employee->deductions[$key];
                                }
                            }
                        }
                    }
                }
            }else {
                $deductions = [];
            }
            return $deductions;
        }
    }
}


