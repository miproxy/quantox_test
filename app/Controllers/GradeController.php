<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolBoard;
use App\Models\Grade;
use App\Core\Response;
use App\Core\Request;

class GradeController extends Controller
{
    /**
     * Display all students
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $student = Student::findOne($request->get('id'));
        $grades = Student::getGrades($request->get('id'));
        return $this->render('grades/index.html', ['student' => $student, 'grades' => $grades]);
    }

    /**
     * Display all students
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $studentId = $request->get('id_student');
        $gradeCount = Student::gradeCount($studentId);
        if (intval($gradeCount['count']) < 4) {
            $grade = new Grade([
                'value' => intval($request->get('value')),
                'id_student' => intval($studentId)
            ]);
        }
        return Response::redirect("students/{$studentId}/grades");
    }
}
