<?php 

namespace App\Core;

/**
 * 
 */
class Session
{
	/**
	 * Set session key/value
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public static function set($key, $value) 
	{
		if (!empty($key) && !empty($value)) {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Unset session key
	 *
	 * @param mixed $key
	 * @return void
	 */
	public static function unset($key)
	{
		if (!empty($key)) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Get value from session using key
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public static function get($key) 
	{
		return (!empty($key) && isset($_SESSION[$key])) ? $_SESSION[$key] : null;
	}

	/**
	 * Check if session key exists
	 * 
	 * @param mixed
	 * @return bool
	 */
	public static function sessionKeyExists($key)
	{
		if (!empty($key)) {
			return isset($_SESSION[$key]);
		}
		return false;
	}

	/**
	 * Set csrf token
	 *
	 * @param string $token
	 * @return void
	 */
	public static function setToken($token)
	{
		if(!empty($token)) {
			$_SESSION['csrf'] = $_SESSION['csrf'] ?? $token;
		}
	}

	/**
	 * Check if csrf token matches the one from session
	 *
	 * @param string $token
	 * @return bool
	 */
	public static function tokenMatches($token)
	{
		return isset($_SESSION['csrf']) && ($_SESSION['csrf'] === $token);
	}

	/**
	 * Delete csrf token
	 *
	 * @return bool
	 */
	public static function deleteToken()
	{
		if(isset($_SESSION['csrf'])) {
			unset($_SESSION['csrf']);
			return true;
		}
		return false;
	}
}
