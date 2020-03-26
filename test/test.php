<?php

require_once __DIR__ . '/../vendor/autoload.php';

$token = \WormOfTime\JWT\JWTHelper::create('3333');

sleep(2);

if (\WormOfTime\JWT\JWTHelper::validate($token)) {
    echo 'hello world,';
} else {
    echo 'f**k,';
}

echo "are you";