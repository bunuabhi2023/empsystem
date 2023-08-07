<?php

namespace App\Imports;

use App\Employee;
use App\EmployeeDocument;
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
use App\Logs;
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

class UsersImport implements ToModel,WithHeadingRow, ShouldQueue,WithChunkReading,WithBatchInserts, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

	use Importable;

    public function model(array $row)
	{
        // Get State ID based on State Name
        $stateName = $row['state']; // Replace 'State Name' with the column name in your Excel file
        $state = states::where('name', $stateName)->value('id');
        
        // Get Country ID based on Country Name
        $countryName = $row['country']; // Replace 'Country Name' with the column name in your Excel file
        $country = Country::where('name', $countryName)->value('id');
        
        // Get State ID based on State Name
        $wstateName = $row['working_state']; // Replace 'State Name' with the column name in your Excel file
        $wstate = states::where('name', $wstateName)->value('id');
        
        // Get Country ID based on Country Name
        $wcountryName = $row['working_country']; // Replace 'Country Name' with the column name in your Excel file
        $wcountry = Country::where('name', $wcountryName)->value('id');
        
        // Get Company ID based on Company Code
        $companyCode = $row['company_code']; // Replace 'Company Code' with the column name in your Excel file
        $company = company::where('company_code', $companyCode)->first();
        $client_grp = $company->client_grp_id;
        $company = $company->id;
        
        // Get Location ID based on Location Code
        $locationCode = $row['location_code']; // Replace 'Location Code' with the column name in your Excel file
        $location = location::where('location_code', $locationCode)->value('id');
        
        // Get Sub Location ID based on Sub Location Code
        $subLocationCode = $row['sub_location_code']; // Replace 'Sub Location Code' with the column name in your Excel file
        $subLocation = sub_locations::where('location_code', $subLocationCode)->value('id');
        
        // Get Department ID based on Department Name
        $departmentName = $row['department']; // Replace 'Department Name' with the column name in your Excel file
        $department = department::where('company_id', $company)->where("department_name", $departmentName)->orWhere(function ($query) {$query->where('is_common', 1);})->value('id');
        
        // Get Designation ID based on Designation Name
        $designationName = $row['designation']; // Replace 'Designation Name' with the column name in your Excel file
        $designation = designation::where('department_id', $department)->where('designation_name', $designationName)->value('id');
        
        $officeShift = office_shift::where('shift_name', $row['office_shift'])->value('id');

        try{
    		$user = User::create([
    			'first_name' => $row['full_name_as_per_aadhar'] ?? '',
    			'last_name' => "",
    			'username' =>$row['full_name_as_per_aadhar'] ?? '',
    			'email' => isset($row['email']) ? $row['email']:'',
    			'password' => Hash::make($row['full_name_as_per_aadhar'] ?? ''),
    			'contact_no' => isset($row['phone']) ? $row['phone']:'',
    			'role_users_id'=> 2,
    			'is_active'=> 2,
    		]);
        }catch(Exception $e){
            Logs::create([
                'data' => json_encode($row),
            ]);
        
            return $e;
	    }
    
        try{
            if($user){
        		$employee = new Employee([
                    'id' => $user->id,
                    'first_name' => $row['full_name_as_per_aadhar'] ?? '',
                    'fname' => $row['father_name'] ?? '',
                    'mname' => $row['mother_name'] ?? '',
                    'last_name' => "",
                    'email' => $row['email'] ?? '',
                    'official_email' => $row['official_email'] ?? '',
                    'contact_no' => $row['phone'] ?? '',
                    'alt_phone' => $row['alt_phone'] ?? '',
                    'gender' => $row['gender'] ?? '',
                    'marital_status' => $row['marital_status'] ?? '',
                    'mAnniversary' => $row['marriage_anniversary'] ?? null,
                    'address' => $row['address1'] ?? '',
                    'address2' => $row['address2'] ?? '',
                    'city' => $row['city'] ?? '',
                    'state' => $state ?? '',
                    'country' => $country ?? '',
                    'wState' => $wstate ?? '',
                    'wCountry' => $wcountry ?? '',
                    'zip_code' => $row['zip_code'] ?? '',
                    'religion' => $row['religion'] ?? '',
                    'blood_grp' => $row['blood_group'] ?? '',
                    'disability' => $row['disability'] ?? '',
                    's_disability' => $row['disability'] == "No" ? null : $row['specify_disability'] ?? '',
                    'aadhar' => $row['aadhar'] ?? null,
                    'pancard' => $row['pan'] ?? null,
                    'uan' => $row['uan'] ?? null,
                    'pf_no' => $row['pf_number'] ?? null,
                    'esic_no' => $row['esic_number'] ?? null,
                    'payable_type' => $row['payable_type'] ?? null,
                    'employee_type' => $row['attendance_deduction_type'] ?? null,
                    'designation_id' => $designation,
                    'department_id' => $department,
                    'sub_location_id' => $subLocation,
                    'office_shift_id' => $officeShift,
                    'location_id' => $location,
                    'company_id' => $company,
                    'client_grp_id' => $client_grp,
                    'joining_date' => $row['doj'] ?? null,
                    'exit_date' => $row['dol'] ?? null,
                    'date_of_birth' => $row['dob'] ?? null,
                    'attendance_type' => $row['attendance_type'] ?? null,
                    'role_users_id' => 2,
                    'is_active'=> 2
                ]);

        		
                if(isset($row['bank_name'])){
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
            		// Update the Bank details
                    $bank = EmployeeBankAccount::create([
                        'employee_id' => $employee->id,
                        'bank_name' => $row['bank_name'],
                        'bank_code' => $row['ifsc'],
                        'account_number' => $row['account_number'],
                        'account_title' => $row['account_name']
                    ]);
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                }
                
                
                // Update the Document details
                if(isset($row['document_types'])){
                    $documentTypes = explode(',', $row['document_types']);
                    $documentValues = explode(',', $row['document_values']);
            
                    foreach ($documentTypes as $key => $documentType) {
                        $documentTypeId = DocumentType::where('document_type', $documentType)->value('id');
                        
                        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                        $document = EmployeeDocument::create([
                            'employee_id' => $employee->id,
                            'document_type_id' => $documentTypeId,
                            'document_title' => $documentType,
                            'description' => $documentValues[$key] ?? ''
                        ]);
                        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    }
                }
                
    	        return $employee;
            }else{
                Logs::create([
                    'data' => json_encode($row),
                ]);
            }
        }catch(Exception $e){
            Logs::create([
                'data' => json_encode($row),
            ]);
            
		    return $e;
	    }
	}

	public function rules(): array
	{
		return [
			'full_name_as_per_aadhar' => 'required',
			'email' => 'required|email',
			'phone' => 'required|max:10',
			'designation' => 'required',
			'department' => 'required',
			'sub_location_code' => 'required',
			'location_code' => 'required',
			'company_code' => 'required',
		];
	}

	public function chunkSize(): int
	{
		return 20;
	}

// 	public function batchSize(): int
// 	{
// 		return 1500;
// 	}
}
