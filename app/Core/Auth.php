<?php

namespace App\Core;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;

class Auth
{
    /**
     * Check if user is logged in
     *
     * @return array
     */
    public static function check()
    {
        if (!Session::sessionKeyExists('user')) {
            if (Request::isAjax()) {
                return ['authenticated' => false, 'response' => Response::json(['message' => 'Unauthenticated', 401]), 'redirect' => false];
            } else {
                return ['authenticated' => false, 'response' => Response::redirect('/login', "Unauthenticated"), 'redirect' => true];
            }
        }
        return ['authenticated' => true, 'response' => Response::redirect('/students', "Authenticated"), 'redirect' => true];
    }

    /**
     * Attempts to login user
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function login($email, $password)
    {
        $user = User::findFirstBy('email', $email);
        if (!$user) {
            return false;
        }
        $verified = password_verify($password, $user['password']);
        if (!$verified) {
            return false;
        }
        Session::set('user', [
            'username' => $user['username'],
            'email' => $user['email']
        ]);
        return true;
    }

    /**
     * Logout user
     *
     * @return void
     */
    public static function logout()
    {
        Session::unset('user');
    }

    /**
     * Get logged in user as array or null if not logged in
     *
     * @return array|null
     */
    public static function user()
    {
        if (Session::sessionKeyExists('user')) {
            return Session::get('user');
        }
        return null;
    }
}
