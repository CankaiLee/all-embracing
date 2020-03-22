<?php
namespace WormOfTime\Swoole\TCP;

use WormOfTime\Swoole\Server;

class TCPServer extends Server
{
    /**
     * @var \Swoole\Server|null
     */
    protected static $server = null;

    public function __construct($host, $port)
    {
        parent::__construct($host, $port);
        self::$server = new \Swoole\Server($host, $port);

        //监听连接进入事件
        self::$server->on('Connect', array(TCPServer::class, 'onConnect'));

        //监听数据接收事件
        self::$server->on('Receive', array(TCPServer::class, 'onReceive'));

        //监听连接关闭事件
        self::$server->on('Close', array(TCPServer::class, 'onClose'));

        //启动服务器
        self::$server->start();
    }

    public static function onConnect($server, $fd)
    {
        echo "hello word";
    }

    public static function onReceive($server, $fd, $form_id, $form_data)
    {

    }

    public static function onClose($server, $fd)
    {
        self::$server->close($fd);
    }
}