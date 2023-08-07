<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sub_locations extends Model
{
	protected $fillable = [
		'name', 'location_code', 'location_id', 'nature_business', 'address1', 'address2', 'state', 'country', 'pincode', 'pan', 'gst', 'tan', 'accountNo', 'ifsc', 'agr_valid_from', 'agr_valid_till', 'agreement', 'payment_term', 'payment_remark', 'payroll_cycle_from', 'payroll_cycle_to', 'invoicing_timeline', 'payment_receivable', 'payment_payable', 'scope_revenue', 'service_charges', 'contact_p_1', 'designation_1', 'contact_1', 'email_1', 'contact_p_2', 'designation_2', 'contact_2', 'email_2',
	];
	
	public function location(){
        return $this->hasOne('App\location', 'id', 'location_id');
    }
    
	public function stateS(){
        return $this->hasOne('App\states', 'id', 'state');
    }
    
	public function countryS(){
        return $this->hasOne('App\Country', 'id', 'country');
    }
}
