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
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\traits\MonthlyWorkedHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Carbon;

class PayslipController extends Controller {

	use MonthlyWorkedHours;

	public function index(Request $request)
	{
		$logged_user = auth()->user();

        $client_groups = client_groups::select('id', 'name')->get();
        $components = salary_components::select('*')->get();
        
        // ADMIN
		$logged_user = auth()->user();

		$selected_date = empty($request->filter_month_year) ? now()->format('F-Y') : $request->filter_month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $selected_date));
		$last_date = date('Y-m-d', strtotime('last day of ' . $selected_date));

		if ($logged_user->can('view-payslip'))
		{
			return view('salary.payslip.index', compact('client_groups', 'components'));
		}

		return abort('403', __('You are not authorized'));
	}
	
	public function getEmployees(Request $request){
	    $client_groups = client_groups::select('id', 'name')->get();
	    $components = salary_components::select('*')->get();
        
        
        $from = $request->salary_from;
        $to = $request->salary_to;
        
        if ($request->client_grp_id) {
            $employees = Employee::where('client_grp_id', $request->client_grp_id)
                ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 1);
                    if ($request->salary_from) {
                        $query->where('period', $request->salary_from."/".$request->salary_to);
                    }
                })
                ->with('payroll')
                ->get();
        } else if ($request->company_id) {
            $employees = Employee::where('company_id', $request->company_id)
                ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 1);
                    if ($request->salary_from) {
                        $query->where('period', $request->salary_from."/".$request->salary_to);
                    }
                })
                ->with('payroll')
                ->get();
        } else if ($request->location_id) {
            $employees = Employee::where('location_id', $request->location_id)
                ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 1);
                    if ($request->salary_from) {
                        $query->where('period', $request->salary_from."/".$request->salary_to);
                    }
                })
                ->with('payroll')
                ->get();
        } else if ($request->sub_location_id) {
            $employees = Employee::where('sub_location_id', $request->sub_location_id)
                ->whereHas('payroll', function ($query) use ($request) {
                    $query->where('isPaid', 1);
                    if ($request->salary_from) {
                        $query->where('period', $request->salary_from."/".$request->salary_to);
                    }
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
			return view('salary.payslip.index', compact('client_groups', 'employees', 'client_groupS', 'company', 'location_id', 'sub_location_id', 'from', 'to', 'components'));
		}

		return abort('403', __('You are not authorized'));
	}

	public function show(Employee $employee, $payroll_id)
	{
	    $components = salary_components::select('*')->get();
	    
	    $salary = EmployeeSalary::where('payslip_id', $payroll_id)->first();
	    
	    // Separate the start and end dates
        list($startDate, $endDate) = explode("/", $salary->period);
        
        // Trim any extra spaces
        $from = trim($startDate);
        $to = trim($endDate);
        
        // Create Carbon instances for the start and end dates
        $carbonStartDate = Carbon::parse($startDate);
        $carbonEndDate = Carbon::parse($endDate);
        
        // Calculate the total number of days between the dates
        $totalDays = $carbonStartDate->diffInDays($carbonEndDate);

	    $workingDays = DB::table('attendances')
            ->select('attendance_date')
            ->where('employee_id', $employee->id)
            ->where('attendance_status', 'present')
            ->whereRaw('DAYOFWEEK(attendance_date) != 1') // Exclude Sundays (where 1 represents Sunday)
            ->whereBetween('attendance_date', [$from, $to]) // Add the date range condition
            ->distinct()
            ->get();
            
        $workingDays = count($workingDays);
        
		return view('salary.payslip.show', compact('employee', 'salary', 'workingDays', 'components', 'totalDays'));
	}

	public function delete(Payslip $payslip){
		if ($payslip->exists)
		{
			$payslip->delete();

			return response()->json(['success' => __('Payslip Deleted successfully')]);
		}
		return response()->json(['error' => 'Operation Unsuccessful']);
	}


	public function printPdf(Payslip $payslip)
	{
		$month_year = $payslip->month_year;
		$first_date = date('Y-m-d', strtotime('first day of ' . $month_year));
		$last_date  = date('Y-m-d', strtotime('last day of ' . $month_year));

		$employee = Employee::with(['user:id,username','company.Location.country',
			'department:id,department_name','designation:id,designation_name',
			'employeeAttendance' => function ($query) use ($first_date, $last_date){
				$query->whereBetween('attendance_date', [$first_date, $last_date]);
			}])
			->select('id','first_name','last_name','joining_date','contact_no','company_id','department_id','designation_id','payslip_type','pension_amount')
			->where('id',$payslip->employee_id)->first()->toArray();


		// return $payslip->pension_amount;

		$total_minutes = 0 ;
		$total_hours = $payslip->hours_worked; //correction
		sscanf($total_hours, '%d:%d', $hour, $min);
		//converting in minute
		$total_minutes += $hour * 60 + $min;
		$amount_hours = ($payslip->basic_salary / 60 ) * $total_minutes;
		$employee['hours_amount'] = $amount_hours;
        $employee['pension_amount'] = $payslip->pension_amount;

		//return view('salary.payslip.pdf',compact('payslip','employee'));

		PDF::setOptions(['dpi' => 10, 'defaultFont' => 'sans-serif','tempDir'=>storage_path('temp')]);
        $pdf = PDF::loadView('salary.payslip.pdf', $payslip, $employee);
        return $pdf->stream();
	}
}

