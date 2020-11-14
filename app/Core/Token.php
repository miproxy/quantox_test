<?php

namespace App\Core;

use App\Core\Session;

/**
 * CSRF Token
 */
class Token
{
	/**
	 * Create session csrf token
	 *
	 * @return void
	 */
	public static function create() {
		Session::setToken(bin2hex(openssl_random_pseudo_bytes(32)));
	}

	/**
	 * Check if token matches the one from session
	 *
	 * @param string|null $token
	 * @return bool
	 */
	public static function check($token = null) {
		return Session::tokenMatches($token);
	}
}
