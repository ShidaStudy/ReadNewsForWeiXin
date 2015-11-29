<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WeixinController extends BaseController {

	/**
	 * 分页条数
	 * @var integer
	 */
	private $_pageSize = 5;
	/**
	 * 微信模板
	 */
	private $_textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";

	private $_imageTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Image>
					<MediaId><![CDATA[%s]]></MediaId>
				</Image>
				</xml>";

	private $_musicTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Music>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<MusicUrl><![CDATA[%s]]></MusicUrl>
					<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
				</Music>
				<FuncFlag>0</FuncFlag>
				</xml>";

	private $_newsTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<ArticleCount><![CDATA[%s]]></ArticleCount>
				<Articles>
					%s
				</Articles>
				</xml>";
	private $_newsItemTpl = "<item>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
				</item>";

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


		// 实例化 apc 缓存
		// $cacheUtil = CacheFactory::create('apc')->set(1,1,1);
		// die;

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

				case 'event':
					$resultStr = $this->_handleEventResponse($postObj);
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
			$returnStr = sprintf($this->_imageTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "image", $tmpStr);
		}
		if (strpos($keyword, "音乐") > -1) {
			$title = "被爱伤过的男人";
			$description = "被爱伤过的男人， 演唱者：吕方";
			$musicUrl = "http://sc.111t.com/up/mp3/1708610E79400060F.mp3";
			$mediaId = "7idpN30xDpUjUHHB7GYDYbncFD0kppGiuNOY6qIkZp77ItKE8j8D1PZTYr-rqQfB";
			$returnStr = sprintf($this->_musicTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "music", $title, $description, $musicUrl, $musicUrl, $mediaId);
		}
		if (strpos($keyword, "新闻") > -1) {
			$tmpStr = $this->_getNews();
			$returnStr = sprintf($this->_newsTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "news", $this->_pageSize, $tmpStr);
		}


		return $returnStr;
	}

	/**
	 * 处理响应事件
	 * @param  [type] $keyword [description]
	 * @return [type]          [description]
	 */
	private function _handleEventResponse($postObj = false) {

		// 参数为空
		if ($postObj === false) {
			return false;
		}

		// 关注事件
		if ($postObj->Event == "subscribe") {
			$tmpStr = "感谢您的关注！";
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", $tmpStr);
		}
		// 已添加关注着扫描二维码
		if ($postObj->Event == "SCAN") {
			$tmpStr = "您已经关注我了";
			$returnStr = sprintf($this->_textTpl, $this->_fromUsername, $this->_toUsername, $this->_time, "text", $tmpStr);
		}

		return $returnStr;
	}

	/**
	 * 获取新闻列表
	 * @return [type] [description]
	 */
	private function _getNews() {
		// 加载NewsModel
		$this->load->model("newsModel");
		// 计算页码
		$newsArr = $this->newsModel->getNews(1, $this->_pageSize);

		$articles = '';
		foreach ($newsArr as $value) {
			$articles = $articles . sprintf($this->_newsItemTpl, $value['title'], $value['title'], "http://img0.bdstatic.com/img/image/shouye/bizhi1124.jpg", $value['article_url']);
		}

		return $articles;
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
