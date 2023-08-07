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
use App\salary_components;
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

use DB;

class ManualImportPayroll implements ToModel,WithHeadingRow, ShouldQueue,WithChunkReading,WithBatchInserts, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

	use Importable;

    public function model(array $row)
	{
	    $employee = Employee::where('staff_id', $row['employee_id'])->where('first_name', $row['employee_name'])
                    ->whereHas('sub_location', function ($query) use ($row) {
                        $query->where('location_code', $row['sub_location_code']);
                    })->first();
	    if($employee->id){
            foreach ($row as $key => $value) {
                if (preg_match('/^[ad]_compliance_.*_components$/', $key)) {
                    $components_array = json_decode($value, true);
                    if (is_array($components_array)) {
                        $row[$key] = $components_array;
                    }
                }
            }
            $jsonData = json_encode($row, JSON_UNESCAPED_SLASHES);
            
            // Decode the JSON string into an associative array
            $data = json_decode($jsonData, true);
            
            // Separate variables with "a_" prefix and "d_" prefix
            $a_variables = array();
            $d_variables = array();
            
            foreach ($data as $key => $value) {
                if (strpos($key, 'a_') === 0) {
                    $a_variables[$key] = $value;
                } elseif (strpos($key, 'd_') === 0) {
                    $d_variables[$key] = $value;
                }
            }
            
            // Convert the arrays to JSON format
            $a_jsonData = json_encode($a_variables, JSON_UNESCAPED_SLASHES);
            $d_jsonData = json_encode($d_variables, JSON_UNESCAPED_SLASHES);
            
            $days = $row['payable_days'];
            $overtime_total = $row['total_overtime'].'';
            
            $ref_no = $row['reference_no'];
            $period = date("Y-m-d", strtotime($row['from'])).'/'.date("Y-m-d", strtotime($row['to']));
            
            $overtime = ["calculation_type"=>"0","rate"=>"0"];
            
            DB::beginTransaction();
            try {
                EmployeeSalary::create([
                    'employee_id' => $employee->id,
                    'addition' => $a_jsonData, // Use the JSON-encoded strings in the create() method
                    'deduction' => $d_jsonData, // Use the JSON-encoded strings in the create() method
                    'overtime_settings' => json_encode($overtime), // Use the JSON-encoded strings in the create() method
                    'ref_no' => $ref_no,
                    'period' => $period,
                    'payslip_id' => $employee->id.''.rand(00000, 99999),
                    'payable_days' => $days,
                    'total_overtime' => $overtime_total,
                    'isPaid' => 1,
                ]);
            
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
            
                return response()->json(['error' => $e->getMessage()]);
            } catch (Throwable $e) {
                DB::rollback();
            
                return response()->json(['error' => $e->getMessage()]);
            }
	    }else{
	        echo 'Employee Not Found';
	    }
	}

	public function rules(): array
	{
		return [];
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
