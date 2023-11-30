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
                    $_SESSION['user']['is_admin'] = ($user[0]['is_admin'] == "1");

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

    public function changePassword($current_password, $new_password, $confirm_new_password)
    {
        $result = false;
        $error_message = '';

        if (empty($current_password)) {
            $error_message .= "Введите текущий пароль!<br>";
        }
        if (empty($new_password)) {
            $error_message .= "Введите новый пароль!<br>";
        }
        if (empty($confirm_new_password)) {
            $error_message .= "Повторите новый пароль!<br>";
        }
        if ($new_password != $confirm_new_password) {
            $error_message .= "Пароли не совпадают!<br>";
        }

        if (empty($error_message)) {
            $user = $this->select("SELECT * FROM users WHERE login = :login", ['login' => $_SESSION['user']['login']]);

            if (!empty($user[0])) {
                $passwordCorrect = password_verify($current_password, $user[0]['password']);

                if ($passwordCorrect) {
                    $new_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $updatePassword = $this->update("UPDATE users SET password = :password WHERE login = :login", [
                        'password' => $new_password,
                        'login' => $_SESSION['user']['login']
                    ]);

                    $result = $updatePassword;
                } else {
                    $error_message .= "Неверный пароль!<br>";
                }
            } else {
                $error_message .= "Произошла ошибка при смене пароля!<br>";
            }
        }

        return [
            'result' => $result,
            'error_message' => $error_message
        ];
    }

    public function getListUsers()
    {
        $result = null;

        $users = $this->select("SELECT id, username, login, is_admin FROM users");
        if (!empty($users)) {
            $result = $users;
        }

        return $result;
    }

    public function getUserById($id)
    {
        $result = null;

        $users = $this->select("SELECT * FROM users WHERE id = :id", [
            'id' => $id
        ]);
        if (!empty($users[0])) {
            $result = $users[0];
        }
        return $result;
    }

    public function deleteById($id)
    {
        $result = false;
        $error_message = '';

        if (empty($id)) {
            $error_message .= "Отсутствует id пользователя!<br>";
        }
        if (empty($error_message)) {
            $result = $this->delete("DELETE FROM users WHERE id = :id",['id' => $id]);
        }

        return [
            'result' => $result,
            'error_message' => $error_message
        ];
    }
}
