<?php

namespace teamones\rpc;

use teamones\rpc\socket\Socket;

class Base
{
    /**
     * @var array
     */
    protected static $_instance = null;

    // 服务地址
    protected $_host = '';

    // 路由地址
    protected $_route = '';

    // 设置body参数
    protected $_body = null;

    /**
     * @return \teamones\rpc\socket\Socket
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Socket;
        }
        return self::$_instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}