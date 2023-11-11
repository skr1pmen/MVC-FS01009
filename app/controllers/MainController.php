<?php

namespace app\controllers;

use app\core\InitController;

class MainController extends InitController
{
    public function actionIndex(){
        $this->render('index');
    }

    public function actionReg(){
        $this->redirect('/user/profile');
    }
}