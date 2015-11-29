<?php

include_once 'ICache.php';

class ApcCache implements ICache {

	private static $_instance;

    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new ApcCache();
        }
        return self::$_instance;
    }

	/**
     * Apc缓存-获取缓存
     * 通过KEY获取缓存数据
     * @param  string $key   KEY值
     */
	public function get($key) {
        return apc_fetch($key);
    }

	/**
     * Apc缓存-设置缓存
     * 设置缓存key，value和缓存时间
     * @param  string $key   KEY值
     * @param  string $value 值
     * @param  string $time  缓存时间
     */
	public function set($key, $value, $expireTime = false) {
		//null情况下永久缓存
		if ($expireTime === false) {
			$expireTime = null;
		}

		return apc_store($key, $value, $expireTime);
	}

	/**
     * Apc缓存-清除一个缓存
     * 从memcache中删除一条缓存
     * @param  string $key   KEY值
     */
    public function clear($key) {
        return apc_delete($key);
    }

    /**
     * Apc缓存-清空所有缓存
     * 不建议使用该功能
     * @return
     */
    public function clear_all() {
        apc_clear_cache('user'); //清除用户缓存
        return apc_clear_cache(); //清除缓存
    }

    /**
     * 检查APC缓存是否存在
     * @param  string $key   KEY值
     */
    public function exists($key) {
        return apc_exists($key);
    }

    /**
     * 字段自增-用于记数
     * @param string $key  KEY值
     * @param int    $step 新增的step值
     */
    public function inc($key, $step = 1) {
        return apc_inc($key, (int) $step);
    }

    /**
     * 字段自减-用于记数
     * @param string $key  KEY值
     * @param int    $step 新增的step值
     */
    public function dec($key, $step = 1) {
        return apc_dec($key, (int) $step);
    }

    /**
     * 返回APC缓存信息
     */
    public function info() {
        return apc_cache_info();
    }


    /*     * ************************************ private function ******************************* */


    /*     * ************************************ private function ******************************* */
}
