<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	protected $fillable = [
		'id', 'client_grp_id', 'company_id', 'location_id', 'sub_location_id', 'department_id', 'event_title', 'event_note', 'event_date', 'event_time', 'status', 'is_notify'
	];

    public function client_group(){
		return $this->hasOne('App\client_groups','id','client_grp_id');
	}
	
	public function location(){
		return $this->hasOne('App\location','id','location_id');
	}
	
	public function sub_location(){
		return $this->hasOne('App\sub_locations','id','sub_location_id');
	}

	public function company(){
		return $this->hasOne('App\company','id','company_id');
	}

	public function department(){
		return $this->hasOne('App\department','id','department_id');
	}

	public function setEventDateAttribute($value)
	{
		$this->attributes['event_date'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
	}

	public function getEventDateAttribute($value)
	{
		return Carbon::parse($value)->format(env('Date_Format'));
	}

}
