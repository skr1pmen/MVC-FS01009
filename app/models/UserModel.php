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
}
