<?php
namespace WormOfTime\ENV;

class ENV
{
    public static function Config()
    {
        $env = parse_ini_file(ROOT_PATH . '.env', true);
        return $env;
    }
}