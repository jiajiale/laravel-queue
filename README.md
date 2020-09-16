## laravel-queue 包使用说明

##### laravel-queue包是在php-amqplib包的基础上做的简单封装

##### 1、配置参数

RABBITMQ_HOST：rabbitmq地址

RABBITMQ_PORT：rabbitmq端口

RABBITMQ_USER：rabbitmq用户名

RABBITMQ_PASSWORD：rabbitmq密码

RABBITMQ_VHOST：rabbitmq vhost

##### 2、发布消息到队列

    Queue::publish(交换机名称,队列名称,消息内容);
    // 消息内容可以是字符串或数组
    Queue::publish('uc','uc',["aaa" => 'cccc']);
    
##### 2、订阅消息内容

    Queue::subscribe('交换机名称',队列名称,function ($msg){
        return true; // 消息ACK
    });