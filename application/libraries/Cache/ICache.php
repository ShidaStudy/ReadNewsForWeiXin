<?php

/**
 * 缓存接口类
 *
 * User: mingwang
 * Date: 15/9/23
 * Time: 下午14:33
 */
interface ICache {

    /**
     * 获取缓存
     */
    function get($key);

    /**
     * 设置缓存
     */
    function set($key, $value, $expireTime);

}



?>
