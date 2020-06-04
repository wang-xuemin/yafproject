<?php

/**
 * Class RabbitMQ
 * @desc RabbitMQ操作类，提供生产和消费方法。使用需amqp扩展
 * @author wangxuemin
 */
class RabbitMQ
{

    private static $connect;
    private static $channel;

    /**
     * 创建连接
     * @return AMQPConnection
     */
    private static function connect()
    {
        try {
            if (empty(self::$connect)) {
                $config = Yaf_Registry::get('config');
                self::$connect = new AMQPConnection(array(
                    'host' => $config->mq->host,
                    'port' => $config->mq->port,
                    'login' => $config->mq->user,
                    'password' => $config->mq->password,
                    'vhost' => $config->mq->virtual->host
                ));
                self::$connect->connect();
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return self::$connect;
    }

    /**
     * 创建一个通道
     * @return mixed
     */
    private static function channel()
    {
        try {
            if (empty(self::$channel)) {
                self::$channel = new AMQPChannel(self::connect());
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return self::$channel;
    }

    /**
     * 生产者，消息推送
     * @param string $exchange_name 交换机名称
     * @param array $message 消息内容
     * @param string $routing_key 路由key
     * @return bool
     */
    public static function publish($exchange_name = 'exchange', $message = array(), $routing_key = 'routing')
    {
        $result = false;
        try {
            // 创建交换机
            $ex = new AMQPExchange(self::channel());
            // 创建或选择交换机，不存在则添加一个
            $ex->setName($exchange_name);
            // 交换机类型
            // direct类型交换机 （直连交换机） // define(‘AMQP_EX_TYPE_DIRECT‘, ‘direct‘);
            // fanout类型交换机 （扇型交换机）// define(‘AMQP_EX_TYPE_FANOUT‘, ‘fanout‘);
            // topic类型交换机  （主题交换机）// define(‘AMQP_EX_TYPE_TOPIC‘, ‘topic‘);
            // header类型交换机 （头交换机）// define(‘AMQP_EX_TYPE_HEADERS‘, ‘headers‘);
            $ex->setType(AMQP_EX_TYPE_DIRECT);
            // 持久化交换机和队列,当代理重启动后依然存在,并包括它们中的完整数据    // define(‘AMQP_DURABLE‘, 2);
            $ex->setFlags(AMQP_DURABLE);
            $ex->declareExchange();
            // 推送消息
            $result = $ex->publish(json_encode($message), $routing_key);
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return $result;
    }

    /**
     * 消费者，获取消息
     * @param string $exchange_name 交换机名称
     * @param string $queue_name 列队名称
     * @param string $routing_key 路由key
     * @return array|mixed
     */
    public static function get($exchange_name = 'exchange', $queue_name = 'queue', $routing_key = 'routing')
    {
        $message = array();
        try {
            // 创建列队
            $q = new AMQPQueue(self::channel());
            // 设置列队名称，不存在则添加一个
            $q->setName($queue_name);
            // 持久化交换机和队列,当代理重启动后依然存在,并包括它们中的完整数据    // define(‘AMQP_DURABLE‘, 2);
            $q->setFlags(AMQP_DURABLE);
            // 声明一个新队列，amqp扩展版本不同高版本废弃掉了声明列队
            // $q->declare();
            // 将给定的队列绑定到交换机上
            $q->bind($exchange_name, $routing_key);
            //消息获取 当在队列get方法中作为标志传递这个参数的时候,消息将在被服务器输出之前标志为acknowledged (已收到)
            $messages = $q->get(AMQP_AUTOACK);
            if ($messages){
                $message = json_decode($messages->getBody(), true);
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return $message;
    }

}