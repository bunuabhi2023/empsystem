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

class UsersBulkUpdate implements ToModel,WithHeadingRow, ShouldQueue,WithChunkReading,WithBatchInserts, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

	use Importable;

    public function model(array $row)
	{
	    if(isset($row['state'])){
            // Get State ID based on State Name
            $stateName = $row['state']; // Replace 'State Name' with the column name in your Excel file
            $state = states::where('name', $stateName)->value('id');
	    }
	    if(isset($row['country'])){
            // Get Country ID based on Country Name
            $countryName = $row['country']; // Replace 'Country Name' with the column name in your Excel file
            $country = Country::where('name', $countryName)->value('id');
	    }
		
		if(isset($row['staff_id'])){
		    $user = [];
            if(isset($row['full_name'])){
                $user['first_name'] = $row['full_name'];
            }
            if(isset($row['email'])){
                $user['email'] = $row['email'];
            }
            if(isset($row['phone'])){
                $user['contact_no'] = $row['phone'];
            }
            
    		$user = User::where('username', $row['staff_id'])->update($user);
            
            $data = [];
    		if(isset($row['full_name'])){
    		    $data['first_name'] = $row['full_name'];
    		}
    		if(isset($row['father_name'])){
    		    $data['fname'] = $row['father_name'];
    		}
    		if(isset($row['mother_name'])){
    		    $data['mname'] = $row['mother_name'];
    		}
    		if(isset($row['email'])){
    		    $data['email'] = $row['email'];
    		}
    		if(isset($row['phone'])){
    		    $data['contact_no'] = $row['phone'];
    		}
    		if(isset($row['alt_phone'])){
    		    $data['alt_phone'] = $row['alt_phone'];
    		}
    		if(isset($row['gender'])){
    		    $data['gender'] = $row['gender'];
    		}
    		if(isset($row['marital_status'])){
    		    $data['marital_status'] = $row['marital_status'];
    		}
    		if(isset($row['mAnniversary'])){
    		    $data['mAnniversary'] = $row['mAnniversary'];
    		}
    		if(isset($row['address'])){
    		    $data['address'] = $row['address'];
    		}
    		if(isset($row['city'])){
    		    $data['city'] = $row['city'];
    		}
    		if(isset($row['state'])){
    		    $data['state'] = $state;
    		}
    		if(isset($row['country'])){
    		    $data['country'] = $country;
    		}
    		if(isset($row['zip_code'])){
    		    $data['zip_code'] = $row['zip_code'];
    		}
    		if(isset($row['religion'])){
    		    $data['religion'] = $row['father_name'];
    		}
    		if(isset($row['blood_group'])){
    		    $data['blood_group'] = $row['blood_group'];
    		}
    		if(isset($row['disability'])){
    		    $data['disability'] = $row['disability'];
    		}
    		if(isset($row['disability']) && $row['disability'] == "No"){
    		    $data['s_disability'] = $row['specify_disability'];
    		}
    		if(isset($row['aadhar'])){
    		    $data['aadhar'] = $row['aadhar'];
    		}
    		if(isset($row['pancard'])){
    		    $data['pancard'] = $row['pancard'];
    		}
    		
    		Employee::where('staff_id', $row['staff_id'])->update($data);
    		
            // Update the Document details
            if(isset($row['document_types'])){
                $documentTypes = explode(',', $row['document_types']);
                $documentValues = explode(',', $row['document_values']);
        
                foreach ($documentTypes as $key => $documentType) {
                    $documentTypeId = DocumentType::where('document_type', $documentType)->value('id');
                    
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    $document = EmployeeDocument::updateOrCreate([
                        'employee_id' => $employee->id,
                        'document_type_id' => $documentTypeId,
                        'document_title' => $documentType,
                        'description' => $documentValues[$key] ?? ''
                    ]);
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                }
            }
		}
	}

	public function rules(): array
	{
		return [
			'staff_id' => 'required'
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
