<?php

/**
 * Class DemoModel
 * @desc demo数据获取类, 可以访问数据库，文件，其它系统等
 * @author wangxuemin
 */
class DemoModel {
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
        $result =  DBMongo::insert('demo', array('name' => 'yaf'));
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
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 1), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 2), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 3), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 4), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 5), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 1), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 2), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 3), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 4), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 5), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 1), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 2), 'routing_yaf'));
        var_dump('<pre>', RabbitMQ::publish('exchange_yaf', array('name' => 'yaf', 'id' => 3), 'routing_yaf'));

        var_dump('<pre>', RabbitMQ::get('exchange_yaf', 'queue_yaf', 'routing_yaf'));
    }
}