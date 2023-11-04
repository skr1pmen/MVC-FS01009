<?php

namespace app\core;

class Router
{
    protected $params = [];

    public function match()
    {
        $url = trim($_SERVER["REQUEST_URI"], '/');
        if (!empty($url)) {
            $params = explode('/', $url);
            if (!empty($params[0]) && !empty($params[1])) {
                $this->params = [
                    'controller' => $params[0],
                    'action' => $params[1]
                ];
            } else {
                return false;
            }
        } else {
            $this->params = [
                'controller' => 'main',
                'action' => 'index'
            ];
        }
        return true;
    }

    public function run()
    {
        if ($this->match()) {
            $path_controller = 'app\\controllers\\' . ucfirst($this->params['controller']) . 'Controller';
            if (class_exists($path_controller)) {
                $action = 'action' . ucfirst($this->params['action']);
                if (method_exists($path_controller, $action)) {
                    $controller = new $path_controller($this->params);
                    $controller->$action();
                } else {
                    View::errorCode(404);
                }
            } else {
                View::errorCode(404);
            }
        } else {
            View::errorCode(404);
        }
    }
}