<?php

namespace App\Models;

use App\Models\Model;
use App\Database\Connection;
use JsonSerializable;

abstract class Model implements JsonSerializable
{
    /**
     * Fillable fields of model
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Model table name
     *
     * @var string
     */
    protected static $table;

    /**
     * Primary key of model table
     *
     * @var string
     */
    protected static $primary_key = 'id';

    /**
     * Fields of model table
     *
     * @var array
     */
    protected $data = [];

    public function __construct($arg = null)
    {
        if (is_int($arg)) {
            $this->data = static::get($arg);
        }
        else if (is_array($arg)) {
            $this->data = $arg;
            $this->save();
        }
    }

    protected function __clone()
    {}

    /**
     * Persist model to db with current data
     *
     * @return void
     */
    public function save() 
    {
        static::$table = static::$table ?? static::table();
        $masks = [];
        foreach ($this->data as $key => $value) {
            $masks[":" . $key] = $value;
        }
        $query = "INSERT INTO " . static::$table;
        
        $query .= "(" . implode(", ", array_keys($this->data)) . ") VALUES ";
        
        $query .= "(" . implode(", ", array_keys($masks)) . ")";

        static::getConnection()->exec("USE `mvc`;");
        $sql = static::getConnection()->prepare($query);
        if ($sql->execute($masks)) {
            $this->data['id'] = static::getConnection()->lastInsertId();
        }
    }

    /**
     * Persist already existing model to db with current data
     *
     * @return void
     */
    public function update()
    {
        if (isset($this->data[static::$primary_key])) {
            static::$table = static::$table ?? static::table();
            $masks = [];
            foreach ($this->data as $key => $value) {
                $masks[":" . $key] = $value;
            }
            $query = "UPDATE " . static::$table . " SET ";

            $set = [];
            foreach ($this->data as $key => $value) {
                if($key !== static::$primary_key) {
                    $set[] = $key . " = " . ":" . $key;
                }
            }
            $set = implode(", ", $set);

            $query .= $set;
            $query .= " WHERE " . static::$primary_key . " = :" . static::$primary_key;

            $sql = static::getConnection()->prepare($query);
            $sql->execute($masks);
        }
    }

    /**
     * Delete model from db
     *
     * @return void
     */
    public function delete() {
        if (isset($this->data[static::$primary_key])) {
            return static::destroy($this->data[static::$primary_key]);
        }
    }

    protected static function get($id)
    {
        static::$table = static::$table ?? static::table();
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primary_key . " = :" . static::$primary_key . " LIMIT 1";
        $query = static::getConnection()->prepare($sql);
        $parameters = array(':' . static::$primary_key => $id);
        $query->execute($parameters);
        return $query->fetch();
    }

    /**
     * Returns the database connection
     * 
     * @return Connection Database connection
     */
    public static function getConnection()
    {
        return Connection::getInstance()->getConnection();
    }

    /**
     * Returns table name based on model Class name
     * 
     * @return string Model table name
     */
    protected static function table() 
    {
        $path = explode('\\', static::class);
        return lcfirst(array_pop($path));
    }

    /**
     * Returns all table rows
     * 
     * @return array Array of all rows in database table
     */
    public static function all() 
    {
        static::$table = static::$table ?? static::table();
        $sql = "SELECT * FROM " . static::$table;
        $query = static::getConnection()->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Returns a table row with specified id
     * 
     * @param  int      $id Value of the id column
     * @return mixed    Associative array containing all row data for specified id, FALSE on failure
     */
    public static function find($id) 
    {
        return new static(static::get($id));
    }

    /**
     * Returns first row selected by field and value
     * 
     * @param   string  $field
     * @param   string  $value
     * @return  mixed
     */
    public static function findFirstBy($field, $value)
    {
        static::$table = static::$table ?? static::table();
        $sql = "SELECT * FROM " . static::$table . " WHERE `$field`=:$field LIMIT 1";
        $query = static::getConnection()->prepare($sql);
        $parameters = array(':' . $field => $value);
        $query->execute($parameters);
        return $query->fetch();
    }

    /**
     * Deletes row with specified id
     * 
     * @param  int     $id  Row id
     * @return boolean      TRUE on success, FALSE on failure
     */
    public static function destroy($id) 
    {
        static::$table = static::$table ?? static::table();
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primary_key . " = :" . static::$primary_key;
        $query = static::getConnection()->prepare($sql);
        $parameters = array(':id' => $id);
        return $query->execute($parameters);
    }

    /**
     * Counts number of rows in table
     * 
     * @return int Number of rows in table
     */
    public static function count()
    {
        static::$table = static::$table ?? static::table();
        $sql = "SELECT COUNT(*) AS total FROM " . static::$table;
        $query = static::getConnection()->prepare($sql);
        $query->execute();
        return $query->fetch()['total'];
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->fillable)) {
            $this->data[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    public function jsonSerialize() {
        return $this->data;
    }

    public function toArray() {
        return $this->data;
    }
}
