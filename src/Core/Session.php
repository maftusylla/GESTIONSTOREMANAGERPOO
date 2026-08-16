<?php

declare(strict_types=1);

final class Session
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::init();

        $_SESSION[$key] = $value;
    }

    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        self::init();

        return $_SESSION[$key] ?? $default;
    }
    public static function setFlash(string $key, mixed $value): void
    {
        self::init();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::init();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function unset(string $key): void
    {
        self::init();

        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::init();

        session_unset();
        session_destroy();
    }
}

