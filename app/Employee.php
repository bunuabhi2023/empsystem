<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
	use Notifiable;
	protected $fillable = [
		'id','first_name','last_name','staff_id','email','contact_no','date_of_birth','gender','status_id','office_shift_id','salary_id','location_id', 'sub_location_id', 'designation_id', 'company_id', 'client_grp_id', 'department_id','is_active',
		'role_users_id','permission_role_id','joining_date','exit_date','marital_status','address', 'address2','city','state','country','zip_code','cv','skype_id','fb_id',
		'twitter_id','linkedIn_id','blogger_id','basic_salary','payslip_type','leave_id','attendance_id','performance_id','award_id','transfer_id','resignation_id',
		'travel_id','promotion_id','complain_id','warning_id','termination_id','attendance_type','total_leave','remaining_leave','pension_type','pension_amount', 'blood_grp', 'religion', 'allow_company', 'allow_loc', 'allow_sub_loc', 'fname', 'mname', 'mAnniversary', 'alt_phone', 'disability', 's_disability', 'aadhar', 'pancard', 'form_token', 'wCountry', 'wState', 'employee_type', 'official_email', 'uan', 'pf_no', 'esic_no', 'payable_type'];

	public function getFullNameAttribute() {
		return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
	}

	public function getBirthDateAttribute() {
		return $this->date_of_birth;
	}

	public function department(){
		return $this->hasOne('App\department','id','department_id');
	}
	
	public function countryN(){
		return $this->hasOne('App\Country','id','country');
	}
	
	public function stateN(){
		return $this->hasOne('App\states','id','state');
	}
	
	public function client_group(){
		return $this->hasOne('App\client_groups','id','client_grp_id');
	}
	
	public function location(){
		return $this->hasOne('App\location','id','location_id');
	}
	
	public function sub_location(){
		return $this->hasOne('App\sub_locations','id','sub_location_id');
	}

	public function officeShift(){
		return $this->hasOne('App\office_shift','id','office_shift_id');
	}

	public function company(){
		return $this->hasOne('App\company','id','company_id');
	}

	public function designation(){
		return $this->hasOne('App\designation','id','designation_id');
	}

	public function status(){
		return $this->hasOne('App\status','id','status_id');
	}

	public function user(){
		return $this->hasOne('App\User','id','id');
	}

	public function payroll(){
		return $this->hasOne('App\EmployeeSalary','employee_id','id');
	}

	public function role(){
		return $this->hasOne('Spatie\Permission\Models\Role','id','role_users_id');
	}

	public function bankAccounts(){
		return $this->hasMany(EmployeeBankAccount::class);
	}
	
	public function salaryBasic(){
		return $this->hasMany(SalaryBasic::class);
	}

	public function allowances(){
		return $this->hasMany(SalaryAllowance::class);
	}
	public function deductions(){
		return $this->hasMany(SalaryDeduction::class);
	}
	public function commissions(){
		return $this->hasMany(SalaryCommission::class);
	}
	public function loans(){
		return $this->hasMany(SalaryLoan::class);
	}
	public function otherPayments(){
		return $this->hasMany(SalaryOtherPayment::class);
	}
	public function overtimes(){
		return $this->hasMany(SalaryOvertime::class);
	}
	public function payslips(){
		return $this->hasMany(Payslip::class);
	}

	public function payslipNew(){
		return $this->hasOne(Payslip::class);
	}

	public function employeeAttendance(){
		return $this->hasMany(Attendance::class);
	}

	public function employeeLeave(){
		return $this->hasMany(leave::class)
			->select('id','start_date','end_date','status','employee_id')
			->whereStatus('approved');
	}



	public function setDateOfBirthAttribute($value)
	{
		if (!empty($value)) {
            $this->attributes['date_of_birth'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
        } else {
            $this->attributes['date_of_birth'] = null; // or $this->attributes['date_of_birth'] = '';
        }
	}

	public function getDateOfBirthAttribute($value)
	{
		return Carbon::parse($value)->format(env('Date_Format'));
	}

	public function setJoiningDateAttribute($value)
	{
		$this->attributes['joining_date'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
	}

	public function getJoiningDateAttribute($value)
	{
		if($value === null)
		{
			return '';
		}
		else{
			return Carbon::parse($value)->format(env('Date_Format'));
		}
	}

	public function setExitDateAttribute($value)
	{
		if (!empty($value)) {
		    $this->attributes['exit_date'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
        } else {
		    $this->attributes['exit_date'] = null;
        }
	}

	public function getExitDateAttribute($value)
	{
		if($value === null)
		{
			return '';
		}
		else{
			return Carbon::parse($value)->format(env('Date_Format'));
		}
	}


}
