<?php

namespace app\controllers;

use app\core\InitController;
use app\lib\UserOperation;
use app\models\NewsModel;

class NewsController extends InitController
{
    public function behaviors()
    {
        return [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['list'],
                        'roles' => [UserOperation::RoleUser, UserOperation::RoleAdmin],
                        'matchCallback' => function () {
                            $this->redirect('/user/login');
                        }
                    ],
                    [
                        'actions' => ['add'],
                        'roles' => [UserOperation::RoleAdmin],
                        'matchCallback' => function () {
                            $this->redirect('/news/list');
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionList()
    {
        $this->view->title = "Новости";

        $newsModel = new NewsModel();
        $news = $newsModel->getListNews();

        $this->render('list', [
            'sidebar' => UserOperation::getMenuLink(),
            'news' => $news,
            'role' => UserOperation::getRoleUser(),
        ]);
    }

    public function actionAdd()
    {
        $this->view->title = "Добавление новости";
        $error_message = '';

        if ($_SERVER['REQUEST_METHOD'] === "POST" && !empty($_POST['btn_news_add_form'])) {
            $news_data = !empty($_POST['news']) ? $_POST['news'] : null;
            if (!empty($news_data)) {
                $newsModel = new NewsModel();
                $result_add = $newsModel->add($news_data);
                if ($result_add['result']) {
                    $this->redirect("/news/list");
                } else {
                    $error_message = $result_add['error_message'];
                }
            }
        }

        $this->render('add', [
            'sidebar' => UserOperation::getMenuLink(),
            'error_message' => $error_message
        ]);
    }
}
