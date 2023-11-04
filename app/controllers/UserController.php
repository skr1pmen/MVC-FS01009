<?php

namespace app\controllers;

use app\core\InitController;

class UserController extends InitController
{
    public function actionProfile()
    {
        $this->render('profile');
    }
}