<?php
namespace WormOfTime\Url;

class UrlParser
{
    /**
     * @var string
     */
    private $_url = '';

    /**
     * @var string
     */
    private $_scheme = '';

    /**
     * @var string
     */
    private $_host = '';

    /**
     * 访问路径
     * @var string
     */
    private $_path = '';

    /**
     * 端口
     * @var int
     */
    private $_port = 0;

    /**
     * query 参数
     * @var array
     */
    private $_params = array();

    /**
     * @var string
     */
    private $_query = '';

    public function __construct($url)
    {
        $this->_url = $url;
        $this->init();
    }

    /**
     * 初始化
     */
    private function init()
    {
        $path_info = parse_url($this->_url);
        $this->_scheme = isset($path_info['scheme']) ? $path_info['scheme'] : 'http';
        $this->_host = isset($path_info['host']) ? $path_info['host'] : '';
        $this->_path = isset($path_info['path']) ? $path_info['path'] : '';
        $this->_port = isset($path_info['port']) ? $path_info['port'] : 80;
        $this->_query = isset($path_info['query']) ? $path_info['query'] : '';
        if ($this->_query) {
            $params = explode('&', $this->_query);
            foreach ($params as $param) {
                list($key, $value) = explode('=', $param);
                $this->_params[$key] = $value;
            }
        }
    }

    public function getHost()
    {
        return $this->_host;
    }

    /**
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        $this->_query = http_build_query($this->_params, '&');
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key) {
        return $this->_params[$key];
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @param bool $with_port
     * @return string
     */
    public function getFullUrl($with_port = false)
    {
        $port = $with_port ? ':' . $this->_port : '';
        $url = "{$this->_scheme}://{$this->_host}{$port}{$this->_path}";
        if ($this->_query) {
            $this->_query = html_entity_decode($this->_query);
            $url .= "?{$this->_query}";
        }
        return $url;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * @param int $port
     * @return UrlParse
     */
    public function setPort($port)
    {
        $this->_port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getTinyUrl()
    {
        $url = "{$this->_path}";
        if ($this->_query) {
            $this->_query = html_entity_decode($this->_query);
            $url .= "?{$this->_query}";
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        if (substr($this->_url, 0, 1) !== '/') {
            return $this->_url;
        }

        return WX_HOST . $this->_url;
    }

    /**
     * @return string
     */
    public function getTinyPath()
    {
        if (strpos($this->_url, 'shop.chjchina.com') !== false || strpos($this->_url, 'superchic.com.cn') !== false || strpos($this->_url, 'chjsuperchic.webapp.lmh5.com') !== false) {
            return $this->getTinyUrl();
        }

        return $this->_url;
    }
}