<?php

namespace WormOfTime\Wechat;

use WormOfTime\Client\HttpClient;
use WormOfTime\Response\Response;

/**
 * Class Weixin
 * @package WormOfTime\Wechat
 */
class Weixin
{
    use Response;

    protected $app_id = '';
    protected $app_secret = '';
    protected $http_client = null;

    public function __construct($app_id, $app_secret)
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
    }

    public function getHttpClient()
    {
        $base_uri = 'https://api.weixin.qq.com/';
        $this->http_client = HttpClient::getInstance($base_uri);
        return $this->http_client;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->app_id;
    }

    /**
     * @param string $app_id
     * @return Weixin
     */
    public function setAppId(string $app_id): Weixin
    {
        $this->app_id = $app_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppSecret(): string
    {
        return $this->app_secret;
    }

    /**
     * @param string $app_secret
     * @return Weixin
     */
    public function setAppSecret(string $app_secret): Weixin
    {
        $this->app_secret = $app_secret;
        return $this;
    }

    /**
     * 获取Access Token
     * @return array
     */
    public function getAccessToken(): array
    {
        $uri = "/cgi-bin/token";

        return $this->getHttpClient()->get($uri, array(
            'grant_type' => 'client_credential',
            'appid' => $this->getAppId(),
            'secret' => $this->getAppSecret()
        ));
    }

    /**
     * 获取Access Token
     * @return string|bool
     */
    protected function get_access_token(): string
    {
        $result = $this->getAccessToken();

        if ( $result['code'] > 0 || ! isset($result['data']['access_token']) ) {
            return false;
        }
        return $result['data']['access_token'];
    }
}