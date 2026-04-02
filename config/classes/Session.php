<?php

abstract class Session 
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    public static function setUser($user)
    {
        self::start();
        $_SESSION['user'] = $user;
    }

    public static function getUser()
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function logout()
    {
        self::start();
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function isLogged()
    {
        return !empty(self::getUser());
    }

    public static function requiredLogin()
    {
        if (!self::isLogged()){
            header('Location: http://localhost/controle-contas/?aviso=sessao');
        }
    }
}