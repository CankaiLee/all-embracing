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

    /**
     * 检测图片，音频是否包含违法违规内容
     * @param string $media_url
     * @param int $media_type 类型。1：音频；2：图片
     * @return array
     */
    public function mediaCheck($media_url = '', $media_type = 2)
    {
        try {
            $access_token = $this->get_access_token();

            if ($access_token === false) {
                return $this->json();
            }

            $uri = "/wxa/media_check_async?access_token=" . $access_token;

            $response = $this->getHttpClient()->post($uri, array(
                'media_url' => $media_url,
                'media_type' => $media_type
            ));

            if ($response['code'] > 0) {
                return $response;
            }

            if (isset($response['data']['errcode']) && $response['data']['errcode'] > 0) {
                return $this->error($response['data']['errcode'], $response['data']['errmsg']);
            }

            return $this->success();

        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 检测文字是否包含违法违规内容
     * @param string $content
     * @return array
     */
    public function msgSecCheck($content = ''): array
    {
        try {
            $access_token = $this->get_access_token();

            if ($access_token === false) {
                return $this->json();
            }

            $uri = "/wxa/msg_sec_check?access_token=" . $access_token;

            $response = $this->getHttpClient()->post($uri, array(
                'content' => $content
            ));

            if ($response['code'] > 0) {
                return $response;
            }

            if (isset($response['data']['errcode']) && $response['data']['errcode'] > 0) {
                return $this->error($response['data']['errcode'], $response['data']['errmsg']);
            }

            return $this->success();
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }
}