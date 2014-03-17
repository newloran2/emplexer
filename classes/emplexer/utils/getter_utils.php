<?php


class GetterUtils
{

    public static function getValueOrDefault($value, $default=null){
        return isset($value) ? $value :  $default;
    }
}
