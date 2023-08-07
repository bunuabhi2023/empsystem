<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
	protected $fillable = [
	    'client_grp_id', 'company_id', 'location_id', 'sub_location_id', 'meeting_title', 'meeting_note', 'meeting_date', 'meeting_time', 'status', 'is_notify'
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

	public function employees(){
		return $this->belongsToMany(Employee::class);
	}

	public function setMeetingDateAttribute($value)
	{
		$this->attributes['meeting_date'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
	}

	public function getMeetingDateAttribute($value)
	{
		return Carbon::parse($value)->format(env('Date_Format'));
	}

}
