<?php
namespace WormOfTime\JWT;

use DomainException;
use Exception;
use UnexpectedValueException;

class JWT
{
    /**
     * @param object|array $payload PHP object or array
     * @param string $key The secret key
     * @param string $algo The signing algorithm
     *
     * @return string A JWT
     * @throws Exception
     */
    public function encode($payload, $key, $algo = 'HS256')
    {
        $header = array('typ' => 'jwt', 'alg' => $algo);

        $segments = array();
        $segments[] = $this->urlsafeB64Encode(json_encode($header));
        $segments[] = $this->urlsafeB64Encode(json_encode($payload));
        $signing_input = implode('.', $segments);

        $signature = $this->sign($signing_input, $key, $algo);
        $segments[] = $this->urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    /**
     * @param string $jwt The JWT
     * @param string|null $key The secret key
     * @param bool $verify Don't skip verification process
     *
     * @return object The JWT's payload as a PHP object
     * @throws Exception
     */
    public function decode($jwt, $key = null, $verify = true)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $payloadb64, $cryptob64) = $tks;
        if (null === ($header = json_decode($this->urlsafeB64Decode($headb64)))
        ) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        if (null === $payload = json_decode($this->urlsafeB64Decode($payloadb64))
        ) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        $sig = $this->urlsafeB64Decode($cryptob64);
        if ($verify) {
            if (empty($header->alg)) {
                throw new DomainException('Empty algorithm');
            }
            if ($sig != $this->sign("$headb64.$payloadb64", $key, $header->alg)) {
                throw new UnexpectedValueException('Signature verification failed');
            }
        }
        return $payload;
    }

    /**
     * @param string $msg The message to sign
     * @param string $key The secret key
     * @param string $method The signing algorithm
     *
     * @return string An encrypted message
     * @throws Exception
     */
    public function sign($msg, $key, $method = 'HS256')
    {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    /**
     * @param string $input A base64 encoded string
     *
     * @return string A decoded string
     */
    public function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param string $input Anything really
     *
     * @return string The base64 encode of what you passed in
     */
    public function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}