<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseController extends CI_Controller {

	const TOKEN = "weixin";
	private $appID = "wx144bfba14c569582";
	private $appSecret = "038dc0230812d8f9aff0160c6ee076ba";

	public $accessToken;

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
	public function checkSignature() {
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

	/**
	 * 验证微信协议
	 * @return [type] [description]
	 */
	public function getAccessToken() {
		// url
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
		$url = sprintf($url, $this->_appID, $this->_appSecret);

		// 访问
		// 实例化 curl类
		$httpCurl = new HttpCurl($url, 10);
		// 发送消息 GET/POST
		$httpResult = $httpCurl->send();
		// 获取状态码
        $httpStatus = $httpCurl->getStatus();

        //请求状态码
        if ($httpStatus === 200) {

			// {"access_token":"ACCESS_TOKEN","expires_in":7200}
			$httpResultarr = json_decode($httpResult, true);
			$this->accessToken = $httpResultarr['access_token'];

			// success
			return true;
        } else {

            // 记录错误日志
            Logger.error(sprintf("错误状态码：?；错误信息：?"), $httpResult, json_encode($httpResult));

			// fail
            return false;
        }
    }

}

?>
