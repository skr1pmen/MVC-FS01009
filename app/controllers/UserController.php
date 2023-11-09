<?php

namespace app\controllers;

use app\core\InitController;

class UserController extends InitController
{
    public function behaviors()
    {
        return [
            'access' => [
                'roles' => [
                    [
                        'actions' => ['login'],
                        'rules' => ['admin'],
                        'matchCallback' => function () {
                            $this->redirect('/user/profile');
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionProfile()
    {
        $this->render('profile');
    }

    public function actionLogin()
    {
        echo "Это страница Авторизации!";
    }
}