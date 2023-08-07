<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaves extends Model
{
	protected $fillable = ['id', 'employee_id', 'leave_type_id', 'probation_days', 'allowed_leave', 'leave_credited', 'manual_adjustment', 'advance_leave', 'leave_taken', 'leave_balance', 'carry_forward_limit', 'leave_refreshed_on', 'leave_deduction_method', 'leave_credit_method'];
	protected $table= "employee_leaves";
}
