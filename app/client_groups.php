<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class client_groups extends Model
{
	protected $fillable = [
		'name', 'code', 'head_office', 'remarks'
	];
	
}
