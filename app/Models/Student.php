<?php

namespace App\Models;

/**
 * User model
 */
class Student extends Model
{
	
	public function __construct($arg = null)
	{
		parent::__construct($arg);
	}

	protected $fillable = ['id_school_board', 'name'];

	protected static $table = 'students';
	
	public static function getGrades($id)
	{
		$sql = "SELECT
				grades.`value`
				FROM
				students
				INNER JOIN grades ON grades.id_student = students.id
				WHERE
				students.id = :id
				ORDER BY
				grades.`value` ASC";
		$query = static::getConnection()->prepare($sql);
		$mask = [':id' => $id];
        $query->execute($mask);
        return $query->fetchAll();
	}

	public static function gradeCount($id)
	{
		$sql = "SELECT
				Count(grades.id) as count
				FROM
				grades
				WHERE
				id_student = :id";
		$query = static::getConnection()->prepare($sql);
		$mask = [':id' => $id];
        $query->execute($mask);
        return $query->fetch();
	}

	public static function findOne($id)
	{
		$sql = "SELECT
				*
				FROM
				students
				WHERE
				id = :id";
		$query = static::getConnection()->prepare($sql);
		$mask = [':id' => $id];
        $query->execute($mask);
        return $query->fetch();
	}

	public static function getStudentWithBoardAndAverage($id)
	{
		$sql = "SELECT
				students.id,
				students.`name`,
				school_boards.`name` AS school_board,
				school_boards.type AS school_board_type,
				Avg(grades.`value`) AS average
				FROM
				students
				INNER JOIN school_boards ON students.id_school_board = school_boards.id
				INNER JOIN grades ON grades.id_student = students.id
				WHERE
				students.id = :id 
				GROUP BY
				students.id,
				students.`name`,
				school_boards.`name`,
				school_boards.type";
		$query = static::getConnection()->prepare($sql);
		$mask = [':id' => $id];
        $query->execute($mask);
        return $query->fetch();
	}

	public static function passesCSM($average)
	{
		return floatval($average) >= 7;
	}

	public static function passesCSMB($grades)
	{
		if (count($grades) === 1) {
			return floatval($grades[0]['value']) > 8 ? "true" : "false";
		} else if (count($grades) > 1) {
			// Grades are sorted ASCENDING in db query
			$max = array_pop($grades);
			return floatval($max['value']) > 8 ? "true" : "false";
		}
		return "false";
	}

	public static function getAllStudents()
	{
		$sql = "SELECT
			students.id,
			students.`name`,
			school_boards.`name` AS school_board_name,
			school_boards.type AS school_board_type
			FROM
			students
			INNER JOIN school_boards ON students.id_school_board = school_boards.id
			GROUP BY
			students.id,
			students.`name`,
			school_boards.`name`,
			school_boards.type";
		$query = static::getConnection()->prepare($sql);
        $query->execute();
        return $query->fetchAll();
	}

	public static function studentWithBoard($id)
	{
		$sql = "SELECT
				students.id,
				students.`name`,
				school_boards.`name` AS school_board_name,
				school_boards.type AS school_board_type
				FROM
				students
				INNER JOIN school_boards ON students.id_school_board = school_boards.id
				WHERE
				students.id = :id";
		$query = static::getConnection()->prepare($sql);
		$mask = [':id' => $id];
		$query->execute($mask);
		return $query->fetch();
	}
}
