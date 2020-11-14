<?php

namespace App\Controllers;

use App\Controllers\Controller;

class ErrorController extends Controller
{
    /**
     * Displays 404 page
     */
    public function error404()
    {
    	http_response_code(404);
        return $this->render('error/404.html');
    }
}
