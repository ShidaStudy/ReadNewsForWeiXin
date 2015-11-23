<?php

class BaseModel extends CI_Model {

    public function __construct() {
        // 加载数据库
        $this->load->database();
    }

	/**
	 * 获取最后的sql语句
	 * @return [type] [description]
	 */
	public function getLastSql(){

		// 加载数据库
        $this->load->database();
		return $this->db->last_query();
	}
}



?>
