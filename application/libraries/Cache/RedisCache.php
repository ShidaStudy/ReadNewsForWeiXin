<?php

include_once 'ICache.php';

/**
 * redis操作
 *
 * @author zxcvdavid@gmail.com
 *
 */

class RedisCache  {
    static $_instance = NULL;

    private $_redis = NULL;

    public static function getInstance($cache_config = '') {

        // 获取 redis 配置
        $cacheHost = config_item("redis_host");
        $cachePort = config_item("redis_port");
        $cacheUser = config_item("redis_user");
        $cachePwd  = config_item("redis_password");

        if (is_null(self::$_instance)) {
            self::$_instance = new RedisCache();

            // 变量实例化成 redis 对象
            self::$_instance->_redis = new Redis();
            self::$_instance->_redis->connect($cacheHost, $cachePort, 3);
            /* user:password 拼接成AUTH的密码 */
            self::$_instance->_redis->auth($cacheUser . ":" . $cachePwd);
        }
        return self::$_instance;
    }

    /**
     * 根据 key 获取值
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function get($key) {
        return $this->_redis->get($key);
    }

    /**
     * 设置缓存
     * @param [type] $key        [description]
     * @param [type] $value      [description]
     * @param [type] $expireTime [description]
     */
    public function set($key, $value, $expireTime = false) {

        if ($expireTime === false) {
            return $this->_redis->set($key, $value);
        }

        // 设置带超时时间
        return $this->_redis->setex($key, $expireTime, $value);
	}

    public function __destruct() {
        $this->_redis->close();
    }
}
