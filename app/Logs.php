<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
	protected $fillable = ['log_id', 'data'];
	protected $table= "csv_logs";
}
