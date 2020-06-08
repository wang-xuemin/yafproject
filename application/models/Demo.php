<?php

/**
 * Class DemoModel
 * @desc demo数据获取类, 可以访问数据库，文件，其它系统等
 * @author wangxuemin
 */
class DemoModel
{
    public function demo()
    {
        // redis
        DBRedis::redis()->set("yaf_redis_demo", "yaf_redis", 100000);
        var_dump(DBRedis::redis()->get('yaf_redis_demo'));
        // mysql
        foreach (DBMySQL::pdo()->query("select * from demo limit 1") as $item) {
            var_dump('<pre>', $item);
        }
        // memcache
        Cache::memcache()->add('yaf_memcache_demo', 'yaf_memcache', 10000);
        var_dump(Cache::memcache()->get('yaf_memcache_demo'));
        // mongo
        $result = DBMongo::insert('demo', array('name' => 'yaf'));
        var_dump('<pre>', $result);
        $data = array(
            array('name' => 'yaf - 1'),
            array('name' => 'yaf - 2'),
            array('name' => 'yaf - 3'),
            array('name' => 'yaf - 4'),
            array('name' => 'yaf - 5'),
            array('name' => 'yaf  -6')
        );
        $result = DBMongo::inserts('demo', $data);
        var_dump('<pre>', $result);
        $result = DBMongo::delete('demo', array('name' => 'yaf'), array('limit' => true));
        var_dump('<pre>', $result);
        $result = DBMongo::update(
            'demo',
            array('name' => 'yaf'),
            array('$set' => array('name' => 'yaf - update')),
            array('multi' => true, 'upsert' => false)
        );
        var_dump('<pre>', $result);
        $field = array(
            'name' => 'yaf - update'
        );
        $options = array(
            'skip' => 0,
            'limit' => 5,
            'stor' => array('_id' => -1)
        );
        $cursor = DBMongo::find('demo', $field, $options);
        foreach ($cursor as $item) {
            var_dump('<pre>', $item);
        }
    }

    public function mq()
    {
        // rabbitmq
        RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 1), 'routing_yaf');
        var_dump('<pre>', RabbitMQ::get('exchange_yaf', 'queue_yaf', 'routing_yaf'));
    }

    public function curl()
    {
        $url = "http://www.yaf.com/index/curl";
        var_dump(CURL::get($url, array('id' => 10, 'method' => 'get')));
        echo '<br />';
        var_dump(CURL::post($url, array('id' => 11, 'method' => 'post')));
        echo '<br />';
        var_dump(CURL::put($url, array('id' => 12, 'method' => 'put')));
        echo '<br />';
        var_dump(CURL::delete($url, array('id' => 13, 'method' => 'delete')));
    }

    public function method()
    {
        if($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $content = file_get_contents('php://input');
            $data = (array)json_decode($content, true);
            echo json_encode(array('method' => $_SERVER['REQUEST_METHOD'], 'data' => $data));
        } else {
            echo json_encode(array('method' => $_REQUEST['method'], 'id' => $_REQUEST['id']));
            exit;
        }
    }
}