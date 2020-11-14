<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Traits\AuthTrait;

class LoginController extends Controller
{
    use AuthTrait;

    /**
     * Displays login form
     *
     * @param Request $request
     */
    public function form(Request $request)
    {
        $data = [];
        $data['message'] = $request->get('message') ?? null;
        return $this->render('auth/login.html', $data);
    }

    /**
     * Handles login
     *
     * @param Request $request
     */
    public function login(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        if (Auth::login($email, $password)) {
            return Response::redirect($this->redirectPath);
        } else {
            return Response::redirect($this->loginPath, ['message' => 'Invalid user credentials.']);
        }
    }

    /**
     * Handles logout
     *
     */
    public function logout()
    {
        Auth::logout();
        return Response::redirect($this->loginPath);
    }
}
