<?php

/**
 * php文件动态加载类
 * User: mingwang3
 * Date: 15-11-22
 * Time: 下午5:13
 */
class AutoLoadUtil {
    private $filepath = null;

    public function __construct() {
        // 一些没有规则的文件定义路径
        $this->filepath = array(
            // php mailer
            // 这里定义成具体的php文件，
            // 这样的定义一般用于处理类名和文件名不一致（没有关联的情况）
            // 'PHPMailer' => INCLUDE_PATH . '/PHPMailer/class.phpmailer.php',
            // 'PHPMailer' => INCLUDE_PATH . '/PHPMailer/class.phpmailer.php',
        );
        // 注册autoload函数到autoload栈中
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     * 自定义目录查找逻辑
     * lib(除lib_base之外)、model、controller(除base_controller之外)可以通过
     * speedPHP自带的spClass方法进行动态加载
     * 但对于一些不使用spClass方法进行实例化的类（比如静态类，一些三方类），
     * 就需要自定义自动加载方法去加载这些类
     * @param $className string 要加载的类名称
     */
    private function loader($className) {
        $path = null;
        switch(true) {
            // 类名称包含'Controller'，则在LIB_PATH中查找
            case strpos($className, 'Controller') > -1:
                $path = APPPATH.'/controllers/'.$className.'.php';
                // die($path);
                break;
            // 类名称包含'fake_'，则在FAKE_PATH中查找
            case strpos($className, 'Model') > -1:
                $path = APPPATH.'/models/'.$className.'.php';
                break;
            default:
                $fp = $this->filepath[$className];

                // 其他的不规则的路径全部在这里查找
                if(!isset($fp)) {
                    return;
                }
                // 如果不是目录，说明是确切的文件
                // 直接进行引入
                if(!is_dir($fp)) {
                    $path = $fp;
                } else {
                    $path = $fp . '/' . $className . '.php';
                }
                break;
        }
        if( is_readable( $path ) ){
            require_once($path); // 载入文件
        }
    }
}

$autoloader = new AutoLoadUtil();
