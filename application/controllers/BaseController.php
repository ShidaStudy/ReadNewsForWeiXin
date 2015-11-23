<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseController extends CI_Controller {

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

}

?>
