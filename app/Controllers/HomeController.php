<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Session;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('home/index.html');
    }
    /**
     * Home page
     */
    public function home()
    {
        return $this->render('home/dashboard.html');
    }
}
