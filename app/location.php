<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class location extends Model
{
	protected $fillable = [
		'location_name', 'location_head', 'address1','address2','city','state','country','zip','location_code', 'state_code', 'company_id', 'remarks'
	];

	public function company(){
		return $this->hasOne('App\company','id','company_id');
	}
	
	public function stateS(){
		return $this->hasOne('App\states','id','state');
	}
	
	public function countryS(){
		return $this->hasOne('App\Country','id','country');
	}

	public function LocationHead(){
		return $this->hasOne('App\Employee','id','location_head');
	}


}
