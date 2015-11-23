<?php

/**
 * 日志类
 * User: mingwang3
 * Date: 15-11-22
 * Time: 下午5:13
 */
class Logger {

	public static function debug($msg) {
		log_message('debug', $msg);
	}

	public static function info($msg) {
		log_message('info', $msg);
	}

	public static function error($msg) {
		log_message('error', $msg);
	}
}

?>
