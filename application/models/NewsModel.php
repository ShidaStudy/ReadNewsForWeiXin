<?php
include "BaseModel.php";

class NewsModel extends BaseModel {

    private $_tableName = 'news';

    public function __construct() {
        // 加载数据库
        $this->load->database();
    }

    /**
     * 获取新闻列表
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
    public function getNews($start = false, $pageSize = false) {

        // 分页查询
        if ($start !== false && $pageSize !== false) {
            $query = $this->db->limit($pageSize, $start)->get($this->_tableName);
            return $query->result_array();
		}
        $query = $this->db->get($this->_tableName);
        return $query->result_array();
    }

    /**
     * 插入新闻列表
     * @param  [type] $newsArr [description]
     * @return [type]          [description]
     */
    public function insertNewsArr($newsArr) {
        $newsData = array();
        foreach ($newsArr as $key => $value) {
            $tempArr = array();
            $tempArr['title'] = $value['title'];
            $tempArr['article_url'] = $value['article_url'];
            $tempArr['behot_time'] = substr($value['behot_time'], 0, -3);
            // 创建时间
            if (is_empty($value, 'created_time')) {
                $tempArr['created_time'] = time();
            }
            // 更新时间
            if (is_empty($value, 'updated_time')) {
                $tempArr['updated_time'] = time();
            }

            // 存入每条新闻
            $newsData [] = $tempArr;
        }
        // dump($newsData);
        // die;
        // 插入多条数据
        return $this->db->insert_batch($this->_tableName, $newsData);
    }
}



?>
