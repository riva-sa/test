<?php

namespace App\Helpers;

class DatabaseHelper
{
    /**
     * Get database driver name
     */
    public static function getDriver()
    {
        return config('database.default');
    }

    /**
     * Check if using PostgreSQL
     */
    public static function isPostgreSQL()
    {
        return self::getDriver() === 'pgsql';
    }

    /**
     * Check if using MySQL
     */
    public static function isMySQL()
    {
        return in_array(self::getDriver(), ['mysql', 'mariadb']);
    }

    /**
     * Get JSON extract syntax for the current database
     */
    public static function jsonExtract($column, $path)
    {
        if (self::isPostgreSQL()) {
            return "{$column}->>{$path}";
        } elseif (self::isMySQL()) {
            return "JSON_EXTRACT({$column}, '$.{$path}')";
        }

        // SQLite fallback
        return "JSON_EXTRACT({$column}, '$.{$path}')";
    }

    /**
     * Get date format for current database
     */
    public static function dateFormat($column, $format = 'Y-m-d')
    {
        if (self::isPostgreSQL()) {
            return "DATE({$column})";
        } elseif (self::isMySQL()) {
            return "DATE({$column})";
        }

        return "DATE({$column})";
    }
}
