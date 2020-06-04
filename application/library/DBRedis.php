<?php

/**
 * Class DBRedis
 * @desc Redis链接类，提供Redis链接方法 DBRedis::redis();
 * @author wangxuemin
 */
class DBRedis
{
    private static $redis;

    /**
     * @return Redis
     */
    public static function redis()
    {
        try {
            if (empty(self::$redis)) {
                // 获取redis相关配置
                $config = Yaf_Registry::get('config');
                self::$redis = new Redis();
                // 链接Redis
                self::$redis->connect($config->redis->host, $config->redis->port);
                // 验证身份密码
                self::$redis->auth($config->redis->password);
                // 选择切换到指定数据库
                self::$redis->select($config->redis->db);
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return self::$redis;
    }
}