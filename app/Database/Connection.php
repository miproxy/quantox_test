<?php

namespace App\Database;

use PDO;

class Connection {

    /**
     * Instance of the Connection class
     *
     * @var Connection|null
     */
    private static $instance = null;
    
    /**
     * PDO
     *
     * @var PDO|null
     */
    private $db = null;

    private function __construct() 
    {}

    /**
     * Returns instance of Connection class
     * 
     * @return Connection Instance of Connection class
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Connection();
        } 
        return self::$instance;
    }

    /**
     * Returns PDO (database connection) object
     * 
     * @return PDO PDO connection
     */
    public function getConnection() 
    {
        if (is_null($this->db)) {
        	$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
            $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
        }
        return $this->db;
    }
}
