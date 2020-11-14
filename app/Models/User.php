<?php

namespace App\Models;

/**
 * User model
 */
class User extends Model
{
	
	public function __construct($arg = null)
	{
		parent::__construct($arg);
	}

	protected $fillable = ['username', 'email', 'password'];

    protected static $table = 'users';
}
