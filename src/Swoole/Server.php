<?php
namespace WormOfTime\Swoole;

abstract class Server
{
    protected $host = '';
    protected $port = '';

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Server
     */
    public function setHost(string $host): Server
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param string $port
     * @return Server
     */
    public function setPort(string $port): Server
    {
        $this->port = $port;
        return $this;
    }
}