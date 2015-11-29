<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseController extends CI_Controller {

	// accessToken 字符串
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
        $signature = $this->getParam("signature");
        $timestamp = $this->getParam("timestamp");
        $nonce = $this->getParam("nonce");

        $token = WEIXIN_VALIDATE_TOKEN;
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

		// 读取APC缓存，看看有无值
		// 返回token
		if ($this->accessToken = CacheFactory::create()->get(CACHE_KEY_WEIXIN_TOKEN)) {
			return $this->accessToken;
		}

		// url
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
		$url = sprintf($url, config_item("wx_app_id"), config_item("wx_app_secret"));

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
			$httpResultArr = json_decode($httpResult, true);
			if (!is_array($httpResultArr) || is_empty($httpResultArr, 'access_token')) {
				Logger::error("wx_getAccessToken---获取微信信息错误。具体信息为：" . $httpResult);
				return false;
			}
			$this->accessToken = $httpResultArr['access_token'];
			// 写入缓存中
			CacheFactory::create()->set(CACHE_KEY_WEIXIN_TOKEN, $httpResultArr['access_token'], $httpResultArr['expires_in']-1000);

			// success
			return $this->accessToken;
        } else {

            // 记录错误日志
            Logger.error(sprintf("错误状态码：?；错误信息：?"), $httpResult, json_encode($httpResult));
			// fail
            return false;
        }
    }

}

?>
