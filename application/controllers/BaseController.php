<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseController extends CI_Controller {

	const TOKEN = "weixin";

	public function __construct() {
		parent::__construct();
		$this->init();
	}

	public function init() {

	}

	/**
	 * 获取参数函数
	 * @return [type] [description]
	 */
	public function getParam($name, $default = '') {
		$v = isset($_GET[$name]) ? $_GET[$name] : '';
		if ($v === false) {
			$v = isset($_POST[$name]) ? $_POST[$name] : '';
		}
		return $v === ''? $default : $v;
	}

	/**
	 * 验证微信协议
	 * @return [type] [description]
	 */
	private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}

?>
