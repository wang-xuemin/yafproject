<?php


/**
 * Class Cache
 * @desc memcache链接类，提供memcache链接方法 Cache::memcache();
 * @author wangxuemin
 */
class Cache
{

    private static $memcache;

    /**
     * @return Memcached
     */
    public static function memcache()
    {
        try {
            if (empty(self::$memcache)) {
                $config = Yaf_Registry::get('config');
                self::$memcache = new Memcached();
                self::$memcache->addServers(array(
                    array($config->memcache->host, $config->memcache->port, 1)
                ));
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return self::$memcache;
    }

}