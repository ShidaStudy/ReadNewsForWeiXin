<?php

include_once 'ApcCache.php';
include_once 'RedisCache.php';

/**
 * 缓存工厂类
 *
 * User: mingwang
 * Date: 15/11/26
 * Time: 下午14:33
 */
class CacheFactory {

    /**
     * 按需创建 HospitalRegister 对象
     *
     * 注意：三方的php处理文件需遵循要点：
     *      1、文件命名只能首字母大写，其他字母小写。如：Guiyifuyuan.php
     *      2、必须用单例模式
     * 参考案例：Guiyifuyuan.php
     *
     * @param type $which 需要实例化的HospitalRegister
     * @return type 实例化后的类或false
     */
    public static function create($which = false) {

        if ($which === false) {
            $which = config_item('redis_type');
        }

		switch ($which) {
			case 'apc':
				return ApcCache::getInstance();
				break;
			case 'redis':
				return RedisCache::getInstance();
				break;
			case 'memcached':
				# code...
				break;

			default:
				# code...
				break;
		}
    }

}
