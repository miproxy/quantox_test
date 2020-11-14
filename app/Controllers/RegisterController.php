<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Models\User;
use App\Traits\AuthTrait;
use App\Core\Logger;

class RegisterController extends Controller
{
    use AuthTrait;

    /**
     * Displays register form
     *
     * @param Request $request
     */
    public function form(Request $request)
    {
        $data = [];
        $data['message'] = $request->get('message') ?? null;
        return $this->render('auth/register.html', $data);
    }

    /**
     * Handles user registration
     *
     * @param Request $request
     */
    public function register(Request $request)
    {
        list($username, $email, $password) = $this->getCredentials($request);
        $this->createUser($username, $email, $password);
        if (Auth::login($email, $password)) {
            return Response::redirect($this->redirectPath);
        } else {
            return Response::redirect($this->loginPath, ['message' => 'Invalid user credentials.']);
        }
    }

    /**
     * Creates new user
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @return User
     */
    private function createUser($username, $email, $password)
    {
        return new User([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Creates array of user credentials from request
     *
     * @param Request $request
     * @return array
     */
    private function getCredentials(Request $request)
    {
        return [
            $request->get('username'), 
            $request->get('email'), 
            $request->get('password')
        ];
    }
}
