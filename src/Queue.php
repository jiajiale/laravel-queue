<?php
namespace Jiajiale\LaravelQueue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
    protected $config;

    protected $_connection;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 创建连接
     * @return AMQPStreamConnection
     */
    protected function createConnection()
    {
        $connection = new AMQPStreamConnection(
            $this->config['rabbitmq']['host'],
            $this->config['rabbitmq']['port'],
            $this->config['rabbitmq']['username'],
            $this->config['rabbitmq']['password'],
            $this->config['rabbitmq']['vhost'],
            $insist = false,
            $login_method = 'AMQPLAIN',
            $login_response = null,
            $locale = 'en_US',
            $connection_timeout = 60.0,
            $read_write_timeout = 60.0,
            $context = null,
            $keepalive = false,
            $heartbeat = 30
        );
        return $connection;
    }

    /**
     * 连接
     */
    protected function connect()
    {
        if(!$this->_connection || !$this->_connection->isConnected()){
            $this->_connection = $this->createConnection();
        }
    }

    /**
     * 断开连接
     */
    protected function disconnect()
    {
        if($this->_connection && $this->_connection->isConnected()){
            $this->_connection->close();
        }
    }

    /**
     * 发布消息
     * @param $exchange
     * @param $queue
     * @param $data
     * @param string $type
     */
    public function publish($exchange,$queue,$data,$type = 'fanout')
    {
        $this->connect();
        $channel = $this->_connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, $type, false, true, false);
        $channel->queue_bind($queue, $exchange);

        if(is_array($data)){
            $messageBody = json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            $messageBody = $data;
        }
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);
        $channel->close();
        $this->disconnect();
    }

    /**
     * 订阅消息
     * @param $exchange
     * @param $queue
     * @param callable $callback
     * @param string $type
     */
    public function subscribe($exchange,$queue,callable $callback,$type = 'fanout')
    {
        $this->connect();
        $channel = $this->_connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, $type, false, true, false);
        $channel->queue_bind($queue, $exchange);

        $channel->basic_consume($queue, '', false, false, false, false,  function($message) use($callback){
            $result = call_user_func($callback,$message->body);

            if($result){    // 根据调用方法return结果确定是否将消息确认掉
                // 手动确认消息
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            }
        });

        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $this->disconnect();
    }
}