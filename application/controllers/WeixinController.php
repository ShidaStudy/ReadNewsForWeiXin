<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WeixinController extends BaseController {

	/**
	 * 分页条数
	 * @var integer
	 */
	private $_pageSize = 10;

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
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            if($keyword == "?" || $keyword == "？")
            {
                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {

				// 测试输出
				// 加载NewsModel
				$this->load->model("newsModel");
				// 计算页码
				$newsArr = $this->newsModel->getNews(1, $this->_pageSize);

				// 只获取显示的几列
				$newsArr = get_some_column($newsArr, array(
					"id", "title", "article_url", "behot_time"
				));


                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, json_encode($contentStr, JSON_UNESCAPED_UNICODE));
                echo $resultStr;
            }
        }else{
            echo "";
            exit;
        }
    }
}
