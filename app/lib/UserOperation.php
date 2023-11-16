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
        if (isset($_SESSION['user']['id']) && isset($_SESSION['user']['is_admin'])) {
            $result = self::RoleAdmin;
        } elseif (isset($_SESSION['user']['id'])) {
            $result = self::RoleUser;
        }
        return $result;

    }
}