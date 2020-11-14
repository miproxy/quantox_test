<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\SchoolBoard;
use App\Core\Response;
use App\Core\Request;

class BoardController extends Controller
{
    public function index(Request $request)
    {
        $boards = SchoolBoard::all();
        return $this->render('boards/index.html', ['boards' => $boards]);
    }
    
    public function store(Request $request)
    {
        $board = new SchoolBoard([
            'name' => $request->get('name'),
            'type' => $request->get('type')
        ]);
        return Response::redirect('boards');
    }
}
