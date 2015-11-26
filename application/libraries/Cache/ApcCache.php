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

	public function set($key, $value, $expireTime) {
		die("APC缓存");
	}

    /*     * ************************************ private function ******************************* */


    /*     * ************************************ private function ******************************* */
}
