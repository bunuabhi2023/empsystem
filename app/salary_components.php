<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class salary_components extends Model
{
	protected $fillable = [
		'title', 'category', 'type', 'remarks'
	];
	
}
