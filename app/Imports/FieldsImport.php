<?php

namespace App\Imports;

use App\Employee;
use App\EmployeeDocument;
use App\EmployeeSalary;

use App\DocumentType;
use App\EmployeeBankAccount;
use App\User;
use App\company;
use App\location;
use App\sub_locations;
use App\department;
use App\designation;
use App\Country;
use App\states;
use App\office_shift;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FieldsImport implements ToModel,WithHeadingRow, ShouldQueue,WithChunkReading,WithBatchInserts, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

	use Importable;

    public function model(array $row)
	{
	    $sid = sub_locations::where('location_code', $row['sub_location_code'])->first();
	    if($sid){
	        $sid = $sid->id;
	        $emp_id = Employee::where('staff_id', $row['employee_id'])->where('first_name', $row['employee_name'])->where('sub_location_id', $sid)->first();
	        if($emp_id){
	            $emp_id = $emp_id->id;
        	    $employee = EmployeeSalary::where('employee_id', $emp_id)->where('isPaid', 1)->first();
        	    if($employee){
            	    $employee->invoice_no = $row['invoice_number'];
            	    $employee->invoice_date = date("Y-m-d", strtotime($row['invoice_date']));
            	    $employee->utr_no = $row['utr_number'];
            	    $employee->payment_date = $row['payment_date'];
            	    $employee->remarks = $row['remarks'];
    	            $employee->save();
        	    }
	        }
	    }
	}

	public function rules(): array
	{
		return [
		    'sub_location_code' => 'required',
		    'employee_id' => 'required',
		    'employee_name' => 'required',
		    'invoice_number' => 'required',
		    'invoice_date' => 'required',
		    'utr_number' => 'required',
		    'payment_date' => 'required',
	    ];
	}

	public function chunkSize(): int
	{
		return 500;
	}

	public function batchSize(): int
	{
		return 1000;
	}
}
