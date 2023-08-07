<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdCard extends Model
{
	protected $fillable = [
		'name', 'template_html'
	];

    protected $table = 'id_card';


}