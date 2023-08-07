<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class company extends Model
{
	protected $fillable = [
		'company_name', 'company_code', 'company_type','trading_name', 'registration_no','contact_no','email','website','date_of_inco','location_id','company_logo', 'client_grp_id', 'remarks', 'add1', 'add2', 'city', 'state', 'country', 'zip'
	];

	public function companyHolidays(){
		return $this->hasMany(Holiday::class)
			->select('id','start_date','end_date','is_publish','company_id')
			->where('is_publish','=',1);
	}

	public function Location(){
		return $this->hasOne('App\location','id','location_id');
	}
	
	public function stateS(){
        return $this->hasOne('App\states', 'id', 'state');
    }
    
	public function countryS(){
        return $this->hasOne('App\Country', 'id', 'country');
    }
	
	public function client_groups(){
		return $this->hasOne('App\client_groups','id','client_grp_id');
	}
}
