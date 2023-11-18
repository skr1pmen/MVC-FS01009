<?php

namespace app\lib;

class UserOperation
{
    const RoleGuest = "guest";
    const RoleAdmin = "admin";
    const RoleUser = "user";

    public static function getRoleUser()
    {
        $result = self::RoleGuest;
        if (isset($_SESSION['user']['id']) && $_SESSION['user']['is_admin']) {
            $result = self::RoleAdmin;
        } elseif (isset($_SESSION['user']['id'])) {
            $result = self::RoleUser;
        }
        return $result;
    }

    public static function getMenuLink()
    {
        $role = self::getRoleUser();
        $list[] = [
            'title' => "Мой профиль",
            'link' => "/user/profile"
        ];
        $list[] = [
            'title' => "Новости",
            'link' => "/news/list"
        ];

        if ($role === self::RoleAdmin) {
            $list[] = [
                'title' => "Пользователи",
                'link' => "/user/users"
            ];
        }

        $list[] = [
            'title' => "Выход",
            'link' => "/user/logout"
        ];

        return $list;
    }
}