<?php

namespace App\Core;

use Spatie\ArrayToXml\ArrayToXml;

/**
 * Response class
 */
class Response
{
	/**
	 * Redirect data
	 *
	 * @param string $route
	 * @param array $data
	 * @return array
	 */
	public static function redirect($route, $data = []) 
	{
		return ['redirect' => URL . trim($route, "/"), 'data' => $data];
	}

	/**
	 * Response with content type of application/json
	 *
	 * @param mixed $payload
	 * @param integer $httpCode
	 * @param array $headers
	 * @return string
	 */
	public static function json($payload, $httpCode = 200, $headers = [])
	{
		http_response_code($httpCode);
		foreach ($headers as $key => $value) {
			if (is_int($key)) {
				header($value);
			} else {
				header($key . ": " . $value);
			}
		}
		header('Content-Type: application/json');
		return json_encode($payload, JSON_NUMERIC_CHECK);
	}

	/**
	 * Response with content type of application/xml
	 *
	 * @param array $payload
	 * @param integer $httpCode
	 * @param array $headers
	 * @return string
	 */
	public static function xml(array $payload, $httpCode = 200, $headers = [])
	{
		http_response_code($httpCode);
		foreach ($headers as $key => $value) {
			if (is_int($key)) {
				header($value);
			} else {
				header($key . ": " . $value);
			}
		}
		header('Content-Type: application/xml');
		return ArrayToXml::convert($payload);
	}

	/**
	 * Response with content type of text/plain
	 *
	 * @param mixed $payload
	 * @param integer $httpCode
	 * @param array $headers
	 * @return string
	 */
	public static function text($payload, $httpCode = 200, $headers = [])
	{
		http_response_code($httpCode);
		foreach ($headers as $key => $value) {
			if (is_int($key)) {
				header($value);
			} else {
				header($key . ": " . $value);
			}
		}
		header('Content-Type: text/plain');
		return $payload;
	}
}
