<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
	protected $fillable = ['id', 'employee_id', 'addition', 'deduction', 'overtime_settings', 'adeduction_settings', 'arrear_calculation', 'payable_days', 'total_overtime', 'updated_at', 'created_at', 'invoice_no', 'invoice_date', 'ref_no', 'payslip_id', 'utr_no', 'payment_date', 'remarks', 'period', 'isPaid'];
	protected $table= "employee_salary";
}
