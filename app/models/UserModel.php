<?php

namespace app\models;

use app\core\BaseModel;

class UserModel extends BaseModel
{
    public function addNewUser($username, $login, $password)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        return $this->insert(
            "INSERT INTO users (username, login, password) VALUES (:username, :login, :password)",
            [
                'username' => $username,
                'login' => $login,
                'password' => $password
            ]
        );
    }

    public function authByLogin($login, $password) {
        $result = false;
        $error_message = '';

        if (empty($login)) {
            $error_message .= "Введите ваш логин!<br>";
        }
        if (empty($password)) {
            $error_message .= "Введите ваш пароль!<br>";
        }

        if (empty($error_message)) {
            $user = $this->select("SELECT * FROM users WHERE login = :login", ['login' => $login]);

            if (!empty($user[0])) {
                $passwordCorrect = password_verify($password, $user[0]['password']);

                if ($passwordCorrect) {
                    $_SESSION['user']['id'] = $user[0]['id'];
                    $_SESSION['user']['login'] = $user[0]['login'];
                    $_SESSION['user']['username'] = $user[0]['username'];
                    $_SESSION['user']['is_admin'] = ($user[0]['is_admin'] == '1');

                    $result = true;
                } else {
                    $error_message .= "Неверный логин или пароль!<br>";
                }
            } else {
                $error_message .= "Пользователь не найден!<br>";
            }
        }

        return [
            'result' => $result,
            'error_message' => $error_message
        ];
    }
}
