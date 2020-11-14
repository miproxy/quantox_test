<?php

namespace App\Models;

/**
 * User model
 */
class Grade extends Model
{
	
	public function __construct($arg = null)
	{
		parent::__construct($arg);
	}

	protected $fillable = ['id_student', 'value'];

    protected static $table = 'grades';
}
