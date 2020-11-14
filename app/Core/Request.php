<?php

namespace App\Core;

/**
 * Request class
 */
class Request
{
	/**
	 * URL parts
	 *
	 * @var mixed
	 */
	protected $url;

	/**
	 * Request method
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Request params
	 *
	 * @var array
	 */
	protected $params = [];
	
	function __construct()
	{
		$this->url = parse_url(trim($_SERVER['REQUEST_URI'], "/"), PHP_URL_PATH) ?: "/";
		$this->method = $_SERVER['REQUEST_METHOD'];
	}


	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Get request method
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Get one or all params or false if one does not exist
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function get($key = null) 
	{
		if (is_null($key)) {
			return $this->params;
		} 
		else if (!empty($key) && isset($this->params[$key])) {
			return $this->params[$key];
		}
		else {
			return false;
		}
	}

	/**
	 * Add param to request
	 *
	 * @param array $inputs
	 * @return Request
	 */
	public function set($inputs = [])
	{
		$this->params = $inputs;
		return $this;
	}

	/**
	 * Request method
	 *
	 * @return string
	 */
	public static function requestMethod() 
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
     * Check if request is Ajax request
     * 
     * @return boolean TRUE if the http request is ajax, FALSE otherwise
     */
    public static function isAjax() 
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
	
	/**
	 * Accept header
	 *
	 * @return string
	 */
	public static function acceptType()
	{
		return $_SERVER['HTTP_ACCEPT'];
	}

	/**
	 * Check if client expects json content type
	 *
	 * @return bool
	 */
	public static function expectsJson()
	{
		return $_SERVER['HTTP_ACCEPT'] === 'application/json';
	}

	/**
	 * Request URI
	 *
	 * @return string
	 */
    public static function url() 
    {
        return $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * All query params
	 *
	 * @return array
	 */
	public static function getQueryParams()
	{
		$queries = [];
		parse_str($_SERVER['QUERY_STRING'], $queries);
		return $queries;
	}

	/**
	 * Single query param
	 *
	 * @param string $key
	 * @return string|null
	 */
	public static function getQueryParam($key)
	{
		$queries = [];
		parse_str($_SERVER['QUERY_STRING'], $queries);
		return $queries[$key] ?? null;
	}
}
