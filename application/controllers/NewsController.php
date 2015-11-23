<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// include 'Base.php';

class NewsController extends BaseController {

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
	public function getNewsList() {

		// Logger::error("fas");

		// 接收参数
		$inputArr['pageNo'] = $this->getParam("pageNo", 1);
		// 去除不合法的参数
		foreach ($inputArr as $key => $value) {
			if ($value === false) {
				unset($inputArr[$key]);
			}
		}

		// 参数验证
		if (is_empty($inputArr, 'pageNo') || !is_int_number($inputArr['pageNo'])) {
			json_return(1010, "pageNo不能为空，且pageNo必须为整数");
		}

		// 加载NewsModel
		$this->load->model("newsModel");
		// 计算页码
		$start = page_start($inputArr['pageNo'], $this->_pageSize);
		$newsArr = $this->newsModel->getNews($start, $this->_pageSize);

		// 只获取显示的几列
		$newsArr = get_some_column($newsArr, array(
			"id", "title", "article_url", "behot_time"
		));

		// 返回正常结果
		json_return(1001, $newsArr);
	}

	/**
	 * 批量插入新闻进入数据库
	 * @return [type] [description]
	 */
	public function insertMoreNews() {
		$url = 'http://api.1-blog.com/biz/bizserver/news/list.do?max_behot_time=&size=1000';
		// 实例化 curl类
		$httpCurl = new HttpCurl($url, 10);
		// 发送消息 GET/POST
		$httpResult = $httpCurl->send();
		// 获取状态码
        $httpStatus = $httpCurl->getStatus();

        //请求状态码
        if ($httpStatus === 200) {

			// 插入新闻
			$httpResultarr = json_decode($httpResult, true);

			// 加载NewsModel
			$this->load->model("newsModel");
			$this->newsModel->insertNewsArr($httpResultarr['detail']);

			json_return(1001);
        } else {

            // 记录错误日志
            // crond_log($httpCurlHelper->getError(), 'error.guiyiRegister.log');
            json_return(1011);
        }
	}

}
