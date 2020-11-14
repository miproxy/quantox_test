<?php

namespace App\Models;

/**
 * User model
 */
class SchoolBoard extends Model
{
	
	public function __construct($arg = null)
	{
		parent::__construct($arg);
	}

	protected $fillable = ['name', 'type', 'response_format'];

	protected static $table = 'school_boards';
}
