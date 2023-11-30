<?php

namespace app\controllers;

use app\core\InitController;
use app\lib\UserOperation;
use app\models\UserModel;

class UserController extends InitController
{
    public function behaviors()
    {
        return [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['login', 'registration'],
                        'roles' => [UserOperation::RoleGuest],
                        'matchCallback' => function () {
                            $this->redirect('/user/profile');
                        }
                    ],
                    [
                        'actions' => ['profile', 'logout'],
                        'roles' => [UserOperation::RoleUser, UserOperation::RoleAdmin],
                        'matchCallback' => function () {
                            $this->redirect('/user/login');
                        }
                    ],
                    [
                        'actions' => ['users'],
                        'roles' => [UserOperation::RoleAdmin],
                        'matchCallback' => function () {
                            $this->redirect('/user/profile');
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionLogin()
    {
        $this->view->title = "Авторизация";
        $error_message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['btn_login_form'])) {
            $login = !empty($_POST['login']) ? $_POST['login'] : null;
            $password = !empty($_POST['password']) ? $_POST['password'] : null;

            $userModel = new UserModel();
            $result_auth = $userModel->authByLogin($login, $password);
            if ($result_auth['result']) {
                $this->redirect('/user/profile');
            } else {
                $error_message = $result_auth['error_message'];
            }
        }

        $this->render('login', [
            'error_message' => $error_message
        ]);
    }

    public function actionProfile()
    {
        $this->view->title = "Мой профиль";
        $error_message = '';

        if ($_SERVER['REQUEST_METHOD'] === "POST" && !empty($_POST['btn_change_password_form'])) {
            $current_password = !empty($_POST['current_password']) ? $_POST['current_password'] : null;
            $new_password = !empty($_POST['new_password']) ? $_POST['new_password'] : null;
            $confirm_new_password = !empty($_POST['confirm_new_password']) ? $_POST['confirm_new_password'] : null;

            $userModel = new UserModel();
            $result = $userModel->changePassword($current_password, $new_password, $confirm_new_password);
            if ($result['result']) {
                $this->redirect('/user/profile');
            } else {
                $error_message = $result['error_message'];
            }
        }

        $this->render("profile", [
            'sidebar' => UserOperation::getMenuLink(),
            'error_message' => $error_message
        ]);
    }

    public function actionRegistration()
    {
        $this->view->title = "Регистрация";
        $error_message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['btn_registration_form'])) {
            $username = !empty($_POST['username']) ? $_POST['username'] : null;
            $login = !empty($_POST['login']) ? $_POST['login'] : null;
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

            if (empty($username)) {
                $error_message .= "Введите ваше имя!<br>";
            }

            if (empty($login)) {
                $error_message .= "Введите ваш логин!<br>";
            }

            if (empty($password)) {
                $error_message .= "Введите ваш пароль!<br>";
            }

            if (empty($confirm_password)) {
                $error_message .= "Введите повторный пароль!<br>";
            }

            if ($password != $confirm_password) {
                $error_message .= "Пароли не совпадают!<br>";
            }

            if (empty($error_message)) {
                $userModel = new UserModel();
                $user_id = $userModel->addNewUser($username, $login, $password);
                if (!empty($user_id)) {
                    $this->redirect('/user/profile');
                }
            }
        }
        $this->render('registration', [
            "error_message" => $error_message
        ]);
    }

    public function actionLogout() {
        if (isset($_SESSION['user']['id'])) {
            unset($_SESSION['user']);
        }

        $params = require "app/config/params.php";
        $this->redirect('/' . $params['defaultController'] . '/' . $params['defaultAction']);
    }

    public function actionUsers()
    {
        $this->view->title = "Пользователи";

        $userModel = new UserModel();
        $users = $userModel->getListUsers();

        $this->render('users', [
            'sidebar' => UserOperation::getMenuLink(),
            'users' => $users,
            'role' => UserOperation::getRoleUser()
        ]);
    }

    public function actionDelete()
    {
        $this->view->title = "Удаление пользователя";

        $user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : null;
        $users = null;
        $error_message = "";

        if (!empty($user_id)) {
            $userModel = new UserModel();
            $users = $userModel->getUserById($user_id);
            if (!empty($users)) {
                $result_delete = $userModel->deleteById($user_id);
                if ($result_delete['result']) {
                    $this->redirect("/user/users");
                } else {
                    $error_message = $result_delete['error_message'];
                }
            } else {
                $error_message = "Пользователь не найдена!";
            }
        } else {
            $error_message = "Отсутствует id пользователя!";
        }

        $this->render("delete", [
            'sidebar' => UserOperation::getMenuLink(),
            'error_message' => $error_message,
            'users' => $users
        ]);
    }
}