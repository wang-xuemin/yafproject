<?php

/**
 * Class DBMySQL
 * @desc MySQL链接类，提供MySQL - PDO 链接方法 DBMySQL::pdo();
 * @author wangxuemin
 */
class DBMySQL
{
    private static $pdo;

    /**
     * @return PDO
     */
    public static function pdo()
    {
        try {
            if (empty(self::$pdo)) {
                $config = Yaf_Registry::get('config');
                $host = $config->mysql->host;
                $user = $config->mysql->user;
                $password = $config->mysql->password;
                $port = $config->mysql->port;
                $db = $config->mysql->db;
                $charset = $config->mysql->charset;
                $dsn = sprintf("%s:host=%s;port=[%-4s];dbname=%s;charset=%s", "mysql", $host, $port, $db, $charset);
                $options = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );
                self::$pdo = @new PDO($dsn, $user, $password, $options);
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return self::$pdo;
    }
}