<?php
namespace WormOfTime\Client;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use WormOfTime\Response\Response;

/**
 * Class HttpClient
 * @package WormOfTime\Client
 */
class HttpClient
{
    use Response;

    /**
     * @var string
     */
    protected $base_uri = '';

    protected static $instance = null;

    /**
     * @var null|Client
     */
    protected $http_client = null;

    /**
     * HttpClient constructor.
     * @param $base_uri
     */
    public function __construct($base_uri)
    {
        $this->base_uri = $base_uri;
    }

    /**
     * @return Client|null
     */
    public function getClient(): Client
    {
        $this->http_client = new Client([
            'base_uri' => $this->base_uri,
            'time_out' => 5.0
        ]);

        return $this->http_client;
    }

    /**
     * @param $base_uri
     * @return static|null
     */
    public static function getInstance($base_uri)
    {
        if ( is_null(self::$instance) ) {
            self::$instance = new static($base_uri);
        }

        return self::$instance;
    }

    /**
     * @param $uri
     * @param array $query
     * @return array
     * 发送get请求
     */
    public function get($uri, $query = array()): array
    {

        $response = $this->getClient()->get($uri, [
            'query' => $query
        ]);

        return $this->_getResponseData($response);
    }

    /**
     * @param $uri
     * @param array $data
     * @param string $data_type ['json', 'body', 'form_params', 'multipart']
     * @return array
     * 发送post请求
     */
    public function post($uri, $data = array(), $data_type = 'json'): array
    {
        $response = $this->getClient()->post($uri, [
            $data_type => $data
        ]);

        return $this->_getResponseData($response);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param string $data_type ['query', 'json', 'body', 'form_params', 'multipart']
     * @return array
     * 发送请求
     */
    public function request($method = 'GET', $uri = '', $data = array(), $data_type = 'query'): array
    {
        $response = $this->getClient()->request($method, $uri, array(
            $data_type => $data
        ));

        return $this->_getResponseData($response);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function _getResponseData($response): array
    {
        $content_type = current($response->getHeader('Content-type'));
        $is_json = strpos($content_type, 'application/json;') !== false;
        if ($response->getStatusCode() == 200) {
            $content = $response->getBody()->getContents();
            $data = array('content_type' => $content_type, 'data' => $content);
            if ($is_json) {
                $data = \GuzzleHttp\json_decode($content, true);
            }

            return $this->success($data);
        }

        return $this->error(40001, '请求链接出错');
    }
}