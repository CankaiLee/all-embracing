<?php

namespace WormOfTime\Wechat;

use WormOfTime\Wechat\Crypt\WXBizDataCrypt;

class MiniProgram extends Weixin
{
    /**
     * 获取服务器Session
     * @param $js_code
     * @return array
     */
    public function getSession($js_code): array
    {
        try {
            $access_token = $this->get_access_token();

            if ($access_token === false) {
                return $this->json();
            }

            $uri = '/sns/jscode2session';

            $response = $this->getHttpClient()->get($uri, [
                'grant_type' => 'authorization_code',
                'appid' => $this->getAppId(),
                'secret' => $this->getAppSecret(),
                'js_code' => $js_code
            ]);

            if ($response['code'] > 0) {
                return $response;
            }

            if (isset($response['data']['errcode']) && $response['data']['errcode'] > 0) {
                return $this->error($response['data']['errcode'], $response['data']['errmsg']);
            }

            $session_key = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . time() . random_string());
            $unionid = null;
            if (isset($response['data']['unionid'])) {
                $unionid = $response['data']['unionid'];
            }

            return $this->success(array(
                'session_key' => $session_key,
                'openid' => element('openid', $response['data']),
                'unionid' => $unionid
            ));
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 获取微信用户信息
     * @param $session_key
     * @param $encrypted_data
     * @param $iv
     * @param $raw_data
     * @param $signature
     * @return array
     */
    public function getWxUserInfo($session_key, $encrypted_data, $iv, $raw_data, $signature): array
    {
        try {
            if (sha1($raw_data . $session_key) != $signature) {
                return $this->error(40011, '签名不正确');
            }

            $pc = new WXBizDataCrypt($this->getAppId(), $session_key);
            $decrypt_data = '';
            $errCode = $pc->decryptData($encrypted_data, $iv, $decrypt_data);

            if ($errCode !== 0) {
                return $this->error($errCode, '加密数据错误');
            }

            $decrypt_data = \GuzzleHttp\json_decode($decrypt_data, true);
            $nickname = $decrypt_data['nickName'];
            $avatar_url = $decrypt_data['avatarUrl'];
            $gender = $decrypt_data['gender'];
            $province = $decrypt_data['province'];
            $city = $decrypt_data['city'];
            $country = $decrypt_data['country'];
            $openid = $decrypt_data['openId'];

            $unionid = null;
            if (isset($decrypt_data['unionId'])) {
                $unionid = $decrypt_data['unionId'];
            }

            return $this->success(
                array(
                    'nickname' => $nickname,
                    'avatar_url' => $avatar_url,
                    'sex' => $gender,
                    'province' => $province,
                    'city' => $city,
                    'country' => $country,
                    'openid' => $openid,
                    'unionid' => $unionid
                )
            );
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 创建小程序二维码
     * @param string $path
     * @param int $width
     * @return array
     */
    public function createQRCode($path = '/', $width = 430): array
    {
        try {
            $access_token = $this->get_access_token();
            if ($access_token === false) {
                return $this->json();
            }

            $uri = '/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $access_token;

            $response = $this->getHttpClient()->post($uri, array(
                'path' => $path,
                'width' => $width
            ));

            if (isset($response['data']['errcode']) && $response['data']['errcode'] > 0) {
                return $this->error($response['data']['errcode'], $response['data']['errmsg']);
            }

            return $response;
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 创建小程序码，有数量限制。与小程序二维码共用100000个
     * @param string $path
     * @param int $width
     * @return array
     */
    public function createCodeLimit($path = '/', $width = 430): array
    {
        try {
            $access_token = $this->get_access_token();
            if ($access_token === false) {
                return $this->json();
            }

            $uri = '/wxa/getwxacode?access_token=' . $access_token;

            $response = $this->getHttpClient()->post($uri, array(
                'path' => $path,
                'width' => $width,
                'auto_color' => false,
            ));

            if (isset($response['data']['errcode']) && $response['data']['errcode'] > 0) {
                return $this->error($response['data']['errcode'], $response['data']['errmsg']);
            }

            return $response;
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制。
     * @param string $path 必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index, 根路径前不要填加 /,不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面
     * @param string $scene 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * @param int $width
     * @return array
     */
    public function createCode($path = '/', $scene = '', $width = 430): array
    {
        try {
            $access_token = $this->get_access_token();
            if ($access_token === false) {
                return $this->json();
            }

            $uri = '/wxa/getwxacodeunlimit?access_token=' . $access_token;

            $response = $this->getHttpClient()->post($uri, array(
                'page' => $path,
                'scene' => $scene,
                'width' => $width,
                'auto_color' => false,
            ));

            if (isset($response['data']['errcode']) && $response['data']['errcode'] > 0) {
                return $this->error($response['data']['errcode'], $response['data']['errmsg']);
            }

            return $response;
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
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