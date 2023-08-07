<?php


namespace App\Imports;

use App\Employee;
use App\EmployeeDocument;
use App\EmployeeBankAccount;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection,WithHeadingRow, ShouldQueue,WithChunkReading
{
	public function collection(Collection $rows)
	{

		foreach ($rows as $row)
		{
			$user = User::create([
				'username' => $row['Full Name'],
				'email' => $row['Email'],
				'password' => Hash::make($row['Full Name']),
				'contact_no' => $row['Phone'],
				'role_users_id'=> 2
			]);
			
			// Get State ID based on State Name
            $stateName = $row['State']; // Replace 'State Name' with the column name in your Excel file
            $state = State::where('name', $stateName)->value('id');
            
            // Get Country ID based on Country Name
            $countryName = $row['Country']; // Replace 'Country Name' with the column name in your Excel file
            $country = Country::where('name', $countryName)->value('id');
            
            // Get Company ID based on Company Code
            $companyCode = $row['Company Code']; // Replace 'Company Code' with the column name in your Excel file
            $company = Company::where('company_code', $companyCode)->value('id');
            
            // Get Location ID based on Location Code
            $locationCode = $row['Location Code']; // Replace 'Location Code' with the column name in your Excel file
            $location = Location::where('location_code', $locationCode)->value('id');
            
            // Get Sub Location ID based on Sub Location Code
            $subLocationCode = $row['Sub Location Code']; // Replace 'Sub Location Code' with the column name in your Excel file
            $subLocation = SubLocation::where('location_code', $subLocationCode)->value('id');
            
            // Get Department ID based on Department Name
            $departmentName = $row['Department Name']; // Replace 'Department Name' with the column name in your Excel file
            $department = Department::where('company_id', $company)->where('department_name', $departmentName)->value('id');
            
            // Get Designation ID based on Designation Name
            $designationName = $row['Designation Name']; // Replace 'Designation Name' with the column name in your Excel file
            $designation = Designation::where('department_id', $department)->where('designation_name', $designationName)->value('id');
			
			$employee = Employee::create([
                'id' => $user->id,
                'first_name' => $row['Full Name'],
                'fname' => $row['Father Name'],
                'mname' => $row['Mother Name'],
                'last_name' => "",
                'email' => $row['Email'],
                'contact_no' => $row['Phone'],
                'alt_phone' => $row['Alt Phone'],
                'joining_date' => isset($row['DOJ']) ? date("Y-m-d", strtotime($row['DOJ'])):null,
                'exit_date' => $row['DOL'],
                'date_of_birth' => $row['DOB'],
                'gender' => $row['Gender'],
                'marital_status' => $row['Marital Status'],
                'mAnniversary' => isset($row['Marital Anniversary']) ? date("Y-m-d", strtotime($row['Marital Anniversary'])):null,
                'address' => $row['Full Address'],
                'city' => $row['City'],
                'state_id' => $state,
                'country_id' => $country,
                'zip_code' => $row['Zip Code'],
                'religion' => $row['Religion'],
                'blood_group' => $row['Blood Group'],
                'disability' => $row['Disability'],
                's_disability' => $row['Disability'] == "No" ? null:$row['Specify Disability'],
                'designation_id' => $designation,
                'department_id' => $department,
                'sub_location_id' => $subLocation,
                'location_id' => $location,
                'company_id' => $company,
                'role_users_id' => 2
            ]);

            // Update the Bank details
            $bank = new EmployeeBankAccount();
            $bank->employee_id = $employee->id;
            $bank->bank_name = $row['Bank Name'];
            $bank->bank_code = $row['IFSC Code'];
            $bank->account_number = $row['Account Number'];
            $bank->account_title = $row['Account Name'];
            $bank->save();
            
            
            // Update the Document details
            $documentTypes = explode(',', $row['Document Types']);
            $documentValues = explode(',', $row['Document Values']);
    
            foreach ($documentTypes as $key => $documentType) {
                $documentTypeId = DocumentType::where('document_type', $documentType)->value('id');
                
                $document = new EmployeeDocument();
                $document->employee_id = $employee->id;
                $document->document_type = $documentTypeId;
                $document->document_title = $documentType;
                $document->description = $documentValues[$key] ?? '';
                $document->save();
            }
		}

	}
	public function chunkSize(): int
	{
		return 500;
	}


}
