<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
	protected $fillable = [
		'asset_name', 'client_grp_id', 'company_id', 'location_id', 'sub_location_id', 'employee_id', 'asset_code', 'assets_category_id', 'Asset_note', 'manufacturer', 'serial_number', 'invoice_number', 'amount', 'asset_image', 'purchase_date', 'warranty_date', 'status'
	];

    public function client_groups(){
		return $this->hasOne('App\client_groups','id','client_grp_id');
	}

	public function company(){
		return $this->hasOne('App\company','id','company_id');
	}
	
	public function location(){
		return $this->hasOne('App\location','id','location_id');
	}
	
	public function sub_location(){
		return $this->hasOne('App\sub_locations','id','sub_location_id');
	}

	public function employee(){
		return $this->hasOne('App\Employee','id','employee_id');
	}


	public function Category(){
		return $this->hasOne('App\AssetCategory','id','assets_category_id');
	}

	public function setPurchaseDateAttribute($value)
	{
		$this->attributes['purchase_date'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
	}

	public function getPurchaseDateAttribute($value)
	{

		return Carbon::parse($value)->format(env('Date_Format'));
	}

	public function setWarrantyDateAttribute($value)
	{
		$this->attributes['warranty_date'] = Carbon::createFromFormat(env('Date_Format'), $value)->format('Y-m-d');
	}

	public function getWarrantyDateAttribute($value)
	{
		return Carbon::parse($value)->format(env('Date_Format'));
	}
}
