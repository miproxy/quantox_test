<?php

namespace App\Controllers;

use App\Core\Auth;

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */
class Controller
{

    /**
     * Twig loader
     *
     * @var \Twig_Loader_Filesystem
     */
    protected $loader;

    /**
     * Twig Environment
     *
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct()
    {
        $folder = __DIR__. '/../../' .'views/';
        $this->loader = new \Twig_Loader_Filesystem($folder);
        $this->twig = new \Twig_Environment($this->loader);
        if(USE_CACHE) {
            $this->twig->setCache($folder . '_cache');
        }
    }

    /**
     * Render html page
     *
     * @param string $file
     * @param array $parameters
     * @return string
     */
    protected function render($file, $parameters = array())
    {
        $parameters['_url_'] = URL;
        $parameters['_user'] = Auth::user();
        $parameters['_csrfField'] = $this->csrf_field();
        $parameters['_csrfToken'] = $this->csrf_token();
        return $this->twig->render($file, $parameters);
    }

    /**
     * Check if string ends with specified substring
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected function endsWith($haystack, $needle) 
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Input field with csrf token as value
     *
     * @return string
     */
    protected function csrf_field() 
    {
        return "<input class='_token' type='hidden' name='_token' value='{$_SESSION['csrf']}'/>";
    }

    /**
     * Returns csrf token
     *
     * @return string
     */
    protected function csrf_token()
    {
        return $_SESSION['csrf'];
    }
}
