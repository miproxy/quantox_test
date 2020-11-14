<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolBoard;
use App\Core\Response;
use App\Core\Request;

class StudentController extends Controller
{
    /**
     * Display all students
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $students = Student::getAllStudents();
        return $this->render('students/index.html', ['students' => $students]);
    }

    /**
     * Show new student form
     */
    public function create()
    {
        $boards = SchoolBoard::all();
        return $this->render('students/create.html', ['boards' => $boards]);
    }

    public function store(Request $request)
    {
        $student = new Student([
            'name' => $request->get('name'),
            'id_school_board' => $request->get('id_school_board')
        ]);
        return Response::redirect('students');
    }

    /**
     * Displays Student info
     * 
     * @param Request $request
     */
    public function show(Request $request)
    {
        $stu = Student::studentWithBoard($request->get('id'));
        if (!$stu) {
            return Response::json([
                'message' => 'Student Not Found', 
                'status' => 'error',
                'code' => 404
            ], 404);
        }

        $student = Student::getStudentWithBoardAndAverage($request->get('id'));

        if ($stu && !$student) {
            $stu['grades'] = [];
            $stu['average'] = 'N/A';
            $stu['final_result'] = 'N/A';
            return $stu['school_board_type'] === 'CSM' 
                ? Response::json($stu) 
                : Response::xml($stu);
        }
        
        $student['grades'] = Student::getGrades($request->get('id'));
        switch($student['school_board_type']) {
            case 'CSM':
                $student['final_result'] = Student::passesCSM($student['average']);
                return Response::json($student);
                break;
            case 'CSMB':
                $student['final_result'] = Student::passesCSMB($student['grades']);
                return Response::xml($student);
                break;
        }
    }
}
