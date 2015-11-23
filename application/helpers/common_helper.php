<?php

/**
 * 返回JSON数据给客户端
 * @param  [type] $code 状态码
 * @param  [type] $msg  消息
 * @return [type]       JSON信息
 */
function json_return($code, $msg = NULL) {
    $return = array('status' => $code, 'result' => ($msg !== NULL) ? $msg : sys_status($code));
    echo json_encode($return);
    exit;
}

/**
 * 系统状态定义
 * @param  integer $code 状态码
 * @return [type]        [description]
 */
function sys_status($code = 1001) {
    $stat = array(
        // *****************  1-1000 vendor 错误 ***************** //
        '1'     => '无法连接三方会员服务器',
        // *****************  系统级 ***************** //
        '1001'  => '操作成功',
        '1002'  => '接口验证失败',
        '1003'  => '没有权限进行访问',
        '1004'  => '手机号码格式不正确',
        '1005'  => '操作失败',
        '1006'  => '新密码格式不正确',
        '1007'  => '没有数据',
        '1008'  => '身份证号码格式不正确', //card_id_err
        '1009'  => '身份证号码或手机号码格式不正确',
        '1010'  => '参数错误',
        '1011'  => '系统错误',
        '1012'  => '数据库连接失败',
        '1013'  => '无版本更新',
        //*****************  用户模块 10001~11000****************//
        '10001' => '用户名和密码不能为空',
        '10002' => '登录密码错误',
    );

    return $stat[$code];
}

//用于后端写入日志
function crond_log($message, $filename) {
    $today_path = APP_PATH . "/log/" . date('Ymd') . '/';

    if (!is_dir($today_path)) {
        mkdir($today_path, 0777);
        chmod($today_path, 0777);
    }

    $filename = $today_path . $filename;
    $is_chmod = !file_exists($filename);

    error_log(date('Y-m-d H:i:s') . ' ' . $message . "\n", 3, $filename);
    if (true == $is_chmod) {
        chmod($filename, 0777);
    }
}

/**
 * 是否符合手机格式
 * @param string $mobile
 * @return bool
 */
function is_mobile($mobile) {

    if (!preg_match("/^1[3|4|5|7|8][0-9]\\d{8}$/", $mobile)) {
        return false;
    }
    return true;
}

function is_password($password) {
    if (!preg_match("/^([0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\-\_]){6,16}$/", $password)) {
        return false;
    }
    return true;
}

/**
 * 是否符合身份证号格式
 * @param string $mobile
 * @return bool
 */
function is_id_card($id_card) {

    if (preg_match("/^\d{14}(\d|x|X)$/", $id_card) || preg_match("/^\d{17}(\d|x|X)$/", $id_card)) {
        return true;
    }
    return false;
}

/**
 * 格式化输出变量
 * @param  string $label  标签
 * @param  [type] $vars   变量名
 * @param  [type] $return 是否返回到变量里
 * @return [type]         格式化输出的变量
 */
function dump($vars, $label = '', $return = false) {
    if (ini_get('html_errors')) {
        $content = "<pre>\n";
        if ($label != '') {
            $content .= "<strong>{$label} :</strong>\n";
        }
        $content .= htmlspecialchars(print_r($vars, true), ENT_COMPAT | ENT_IGNORE);
        $content .= "\n</pre>\n";
    } else {
        $content = $label . " :\n" . print_r($vars, true);
    }
    if ($return) {
        return $content;
    }
    echo $content;
    return null;
}

/**
 * 判断变量是否为空
 * @param  [type]  $param [description]
 * @return boolean        [description]
 */
function is_empty($paramArr = false, $key = false) {
    if ($paramArr === false || $key === false || !isset($paramArr[$key]) ||
            empty($paramArr[$key])) {
        return true;
    }
    return false;
}

if (!function_exists("is_int_number")) {
    /**
     * 判断是否是数字
     * @param  [type]  $param [description]
     * @return boolean        [description]
     */
    function is_int_number($param = false) {
        if ($param === false || !(is_numeric($param) && is_int($param+0))) {
            return false;
        }
        return true;
    }
}

if (!function_exists("page_start")) {
    /**
     * 判断是否是数字
     * @param  [type]  $param [description]
     * @return boolean        [description]
     */
    function page_start($pageNo = false, $pageSize = false) {
        if ($pageNo === false || $pageSize === false) {
            return false;
        }

        return ($pageNo-1) * $pageSize;
    }
}

if (!function_exists("get_some_column")) {
    /**
     * 获取 数组中的某些列
     * @param  [type] $newsArr [description]
     * @return [type]          [description]
     */
    function get_some_column($paramArr = false, $columnArr = false) {
        // 参数判断
        if ($paramArr === false || !is_array($paramArr) || $columnArr === false ||
                !is_array($columnArr)) {
            return false;
        }

        $returnArr = array();
        foreach ($paramArr as $pk => $pv) {

            $tempArr = array();
            foreach ($columnArr as $cv) {
                if (!is_empty($pv, $cv)) {
                    // 存入值
                    $tempArr[$cv] = $pv[$cv];
                }
            }

            // 存入某条数据
            $returnArr [] = $tempArr;
        }

        return $returnArr;
    }
}

?>
