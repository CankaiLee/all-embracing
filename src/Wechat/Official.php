<?php

namespace WormOfTime\Wechat;

use WormOfTime\Url\UrlParser;

class Official extends Weixin
{
    /**
     * @param $redirect_uri
     * @param string $scope
     * @param string $state
     * @return string
     * 获取授权回调地址
     */
    public function getRedirectUrl($redirect_uri, $scope = 'snsapi_base', $state = '')
    {
        $base_uri = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $urlParser = new UrlParser($base_uri);
        $urlParser->setParam('app_id', $this->getAppId())
            ->setParam('redirect_uri', urlencode($redirect_uri))
            ->setParam('response_type', 'code')
            ->setParam('scope', $scope)
            ->setParam('state', $state);
        return $urlParser->getFullUrl() . '#wechat_redirect';
    }

    /**
     * @param $code
     * @return array
     * 通过code换取一个特殊的网页授权access_token
     */
    public function getOauthAccessToken($code)
    {
        try {
            if (! $code) {
                return $this->error(40001, '缺少必要参数：code');
            }

            $uri = '/sns/oauth2/access_token';

            $response = array();
            $response = $this->getHttpClient()->get($uri, array(
                'appid' => $this->getAppId(),
                'secret' => $this->getAppSecret(),
                'code' => $code,
                'grant_type' => 'authorization_code'
            ));

            return $response;
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * @param $refresh_token
     * @return array
     * 刷新授权Access Token
     */
    public function refreshOauthAccessToken($refresh_token)
    {
        try {
            $uri = 'sns/oauth2/refresh_token';

            $response = array();
            $response = $this->getHttpClient()->get($uri,array(
                'grant_type'=>'refresh_token',
                'appid'=>$this->getAppId(),
                'refresh_token'=>$refresh_token

            ));

            return $response;
        }catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * @param $access_token
     * @param $openid
     * @return array
     * 检验授权凭证（access_token）是否有效
     */
    public function checkOauthAccessToken($access_token, $openid): array
    {
        try {
            if (! $access_token || ! $openid) {
                return $this->error(40001, '缺少必要参数');
            }

            $uri = '/sns/auth';

            $response = $this->getHttpClient()->get($uri, array(
                'access_token' => $access_token,
                'openid' => $openid
            ));

            if ($response['code'] > 0) {
                return $response;
            }

            if ($response['data']['errcode'] > 0) {
                return $this->error(element('errcode', $response['data']), element('errmsg', $response['data']));
            }

            return $this->success();
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * @param string $access_token 网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
     * @param string $openid 用户的唯一标识
     * @param string $lang 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @return array
     * 拉取用户信息(需scope为 snsapi_userinfo)
     */
    public function getWxUserInfo($access_token = '', $openid = '', $lang = 'zh_CN'): array
    {
        try {
            $uri = '/sns/userinfo';

            $response = array();
            $response = $this->getHttpClient()->get($uri, array(
                'access_token' => $access_token,
                'openid' => $openid,
                'lang' => $lang
            ));

            return $response;

        } catch (\Exception $exception) {
            $this->error($exception->getCode(), $exception->getMessage());
        }
    }
}