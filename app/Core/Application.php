<?php

namespace App\Core;

use App\Controllers;
use App\Core\Request;
use App\Core\Response;
use App\Core\Token;
use App\Core\Auth;
use App\Core\Logger;

class Application
{
    /**
     * Response
     *
     * @var string|null
     */
    private $response = null;

    /**
     * URL pattern
     *
     * @var string|mixed
     */
    private $pattern = null;

    /**
     * Application class constructor
     * "Start" the application:
     * Analyses the URL elements and calls the according controller/method or the fallback
     */
    public function __construct()
    {
        $url = $this->getUrlRoute();
        $request = new Request();
        $requestMethod = $request->getMethod();

        if ($requestMethod == 'POST') {
            if (!isset($_POST['_token']) || !Token::check($_POST['_token'])) {
                $controller = new \App\Controllers\ErrorController;
                $this->response = $controller->error404();
                echo $this->response;
                return;
            }
            if (isset($_POST['_method']) && ($_POST['_method'] == 'PUT' || $_POST['_method'] == 'PATCH' || $_POST['_method'] == 'DELETE')) {
                $requestMethod = strtoupper($_POST['_method']);
            }
            else {
                $requestMethod = 'POST';
            }
        }

        foreach ($this->routes[$requestMethod] as $route => $controllerMethod) {
            if (preg_match($this->toPattern($route), $url, $matches)) {

                $matches = array_filter($matches, function($k) {
                    return !is_numeric($k);
                }, ARRAY_FILTER_USE_KEY);

                $request->set(array_merge($matches, $_GET, $_POST));

                if ($controllerMethod['auth'] === true) {
                    $auth = Auth::check();
                    if (!$auth['authenticated']) {
                        if ($auth['redirect']) {
                            header('location: ' . $auth['response']['redirect']);
                        } else {
                            echo $auth['response'];
                        }
                        return;
                    }
                }

                if ($controllerMethod['guest'] === true) {
                    $auth = Auth::check();
                    if ($auth['authenticated']) {
                        if ($auth['redirect']) {
                            header('location: ' . $auth['response']['redirect']);
                        } else {
                            echo $auth['response'];
                        }
                        return;
                    }
                }

                $controllerAndMethod = explode('@', $controllerMethod['action']);
                list($controllerName, $method) = $controllerAndMethod;

                $class = "App\\Controllers\\{$controllerName}";
                $controller = new $class();
                $this->response = $controller->$method($request);
                break;
            }
        }

        if (is_null($this->response)) {
            $controller = new \App\Controllers\ErrorController;
            $this->response = $controller->error404();
            echo $this->response;
        }
        else if (is_array($this->response) && isset($this->response['redirect'])) {
            header('location: ' . $this->response['redirect']);
        }
        else {
            echo $this->response;
        }

    }

    /**
     * Filter request URI
     *
     * @return string
     */
    private function getUrlRoute()
    {
        $url = urldecode($_SERVER['REQUEST_URI']);
        $url = (stripos($url, "?") === false) ? $url : substr($url, 0, stripos($url, "?"));
        $url = explode('/', $url);
        $url = array_diff($url, array('', 'index.php'));
        $url = array_values($url);
        return implode("/", $url);
    }

    /**
     * Get URI pattern
     *
     * @param string $route
     * @return string
     */
    private function toPattern($route)
    {
        if ($route !== "/") {
            $route = trim($route, "/");
            $pattern = '/^';
            $params = explode("/", $route);
            foreach ($params as $param) {
                if (strpos($param, "{") === false) {
                    $pattern .= '(' . $param . ')\/';
                }
                else {
                    $pattern .= '(?P<' . trim($param, "{}") . '>\w+)\/';
                }
            }
            return substr($pattern, 0, -2) . '$/';
        }
        else {
            return '/^(?![\s\S])/';
        }
    }

    /**
     * Defined routes
     *
     * @var array
     */
    private $routes = [
        'GET' => [
            "/" => ['action' => "HomeController@index", "auth" => false, 'guest' => false ],
            'login' => ['action' => 'LoginController@form', 'auth' => false, 'guest' => true ],
            'logout' => ['action' => 'LoginController@logout', 'auth' => true, 'guest' => false ],
            'register' => ['action' => 'RegisterController@form', 'auth' => false, 'guest' => true ],
            'boards' => ['action' => 'BoardController@index', 'auth' => true, 'guest' => false ],
            'students' => ['action' => 'StudentController@index', 'auth' => true, 'guest' => false ],
            'students/create' => ['action' => 'StudentController@create', 'auth' => true, 'guest' => false ],
            'students/{id}/grades' => ['action' => 'GradeController@index', 'auth' => true, 'guest' => false ],
            'students/{id}' => ['action' => 'StudentController@show', 'auth' => true, 'guest' => false ],
        ],
        'POST' => [
            '/register' => ['action' => 'RegisterController@register', 'auth' => false, 'guest' => true ],
            '/login' => ['action' => 'LoginController@login', 'auth' => false, 'guest' => true ],
            '/boards' => ['action' => 'BoardController@store', 'auth' => true, 'guest' => false ],
            '/students' => ['action' => 'StudentController@store', 'auth' => true, 'guest' => false ],
            '/grades' => ['action' => 'GradeController@store', 'auth' => true, 'guest' => false ],
        ],
        'PUT' => [
        ],
        'PATCH' => [
        ],
        'DELETE' => [
        ]
    ];
}
