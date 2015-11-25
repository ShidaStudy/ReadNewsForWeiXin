<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WeixinController extends BaseController {

	/**
	 * 分页条数
	 * @var integer
	 */
	private $_pageSize = 10;
	/**
	 * 微信模板
	 */
	private $_textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>";

	private $_imageTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Image>
					<MediaId><![CDATA[%s]]></MediaId>
				</Image>
				<FuncFlag>0</FuncFlag>
				</xml>";

	private $_fromUsername;
	private $_toUsername;
	private $_time;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 获取 新闻列表
	 * @return [type] [description]
	 */
	public function index() {
		if (isset($_GET['echostr'])) {
		    $this->_valid();
		}else{
		    $this->_responseMsg();
		}
	}

	private function _valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

	private function _responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->_fromUsername = $postObj->FromUserName;
            $this->_toUsername = $postObj->ToUserName;
			$msgType = trim($postObj->MsgType);
            $this->_time = time();
            $textTpl = $this->_textTpl;

			switch ($msgType) {
				case 'text':
					$resultStr = $this->_handleTextResponse($postObj->Content);
					break;

				case 'image':
					$responseContent = sprintf("图片链接：%s\n媒体 id:%s", trim($postObj->PicUrl), trim($postObj->MediaId));
					$responseMsgType = "text";
					$resultStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, $responseMsgType, $responseContent);
					break;

				default:
					$responseContent = "暂时不支持该类型";
					$responseMsgType = "text";
					$resultStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, $responseMsgType, $responseContent);
					break;
			}

			echo $resultStr;
        }else{
            echo "<b><i'>ceshi</i></b>";
            exit;
        }
    }

	/**
	 * 处理普通文本消息
	 * @param  [type] $keyword [description]
	 * @return [type]          [description]
	 */
	private function _handleTextResponse($keyword = false) {

		// 参数为空
		if ($keyword === false || empty($keyword)) {
			return false;
		}

		// 返回文本
		$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", '我也不知道该说啥了。。。');;

		if (strpos($keyword, "梁丽") > -1) {
			$tmpStr = "我爱你";
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", $tmpStr);
		}
		if (strpos($keyword, "?") > -1 || strpos($keyword, "？") > -1) {
			$tmpStr = "你想表达啥";
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", $tmpStr);
		}
		if (strpos($keyword, "ceshi") > -1) {
			$tmpStr = "不怕我打你。。。";
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", $tmpStr);
		}
		if (strpos($keyword, "时间") > -1 || strpos($keyword, "time") > -1) {
			$tmpStr = date("Y-m-d H:i:s",time());
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", $tmpStr);
		}
		if (strpos($keyword, "图片") > -1) {
			$tmpStr = "7idpN30xDpUjUHHB7GYDYbncFD0kppGiuNOY6qIkZp77ItKE8j8D1PZTYr-rqQfB";
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "image", $tmpStr);
		}

		return $returnStr;
	}

	/**
	 * 处理普通文本消息
	 * @param  [type] $keyword [description]
	 * @return [type]          [description]
	 */
	private function _handleImageResponse($keyword = false) {

		return $returnStr;
	}
}
