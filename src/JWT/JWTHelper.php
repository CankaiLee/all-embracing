<?php

namespace WormOfTime\JWT;

use WormOfTime\Response\Response;

class JWTHelper
{
    /**
     * @var null|JWT
     */
    protected static $jwt = null;

    public static function _init()
    {
        if (is_null(self::$jwt)) {
            self::$jwt = new JWT();
        }
    }

    /**
     * 获取Json Web Token
     *
     * @param $union_id
     * @return string
     * @throws \Exception
     */
    public static function create($union_id)
    {
        self::_init();
        return  self::$jwt->encode(array(
            'consumerKey' => env('CONSUMER_KEY'),
            'userId' => $union_id,
            'issuedAt' => date(DATE_ISO8601, strtotime("now")),
            'ttl' => env('CONSUMER_TTL')
        ), env('CONSUMER_SECRET'));
    }

    /**
     * 验证token是否过期
     *
     * @param $token
     * @return bool
     */
    public static function validate($token)
    {
        try {
            $decodeToken = self::decode($token);
            if ($decodeToken === false) {
                return false;
            }
            // validate token is not expired
            $ttl_time = strtotime($decodeToken->issuedAt);
            $now_time = strtotime(date(DATE_ISO8601, strtotime("now")));
            if(($now_time - $ttl_time) > $decodeToken->ttl) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 解析获取token内容
     *
     * @param $token
     * @return bool|object
     */
    public static function decode($token)
    {
        try {
            self::_init();
            return self::$jwt->decode($token, env('CONSUMER_SECRET'));
        } catch (\Exception $e) {
            return false;
        }
    }

}