<?php

final class HttpCurl {

    /**
     * 发送的cookie
     * @var string
     */
    protected $cookie = array();

    /**
     * 发送的http头
     * @var array
     */
    protected $header = array();

    /**
     * 发送的数据
     * @var array
     */
    protected $data   = array();

    /**
     * 错误信息
     * @var string
     */
    protected $err = '';

    /**
     * 错误编码
     * @var string
     */
    protected $eno = 0;

    /**
     * 响应的状态信息
     * @var string
     */
    protected $status;

    /**
     * 超时时间
     * @var string
     */
    protected $timeout       = 0;

    /**
     * 访问的URL地址
     * @var array
     */
    protected $url           = '';

    /**
     * 重定向次数
     * @var int
     */
    protected $_maxRedirs    = 1;

    /**
     * 是否支持重定向
     * @var boolean
     */
    protected $_redirects    = false;

    /**
     * 是否读取头信息,false 不读取，true 读取
     * @var boolean
     */
    protected $_header       = false;

    /**
     * 是否读取内容体信息，false 不读取，true 读取
     * @var boolean
     */
    protected $_body         = true;

    /**
     * Enter description here ...
     * @var unknown_type
     */
    protected $_waitResponse = true;

    /**
     * http连接句柄
     */
    protected $httpHandler = null;

    /**
     * 声明受保护的构造函数,避免在类的外界实例化
     * @param string $url
     * @param int $timeout
     */
    public function __construct($url = '', $timeout = 30) {
        $this->url         = $url;
        $this->timeout     = $timeout;
        $this->httpHandler = $this->createHttpHandler();
    }

    /**
     * 设置http头,支持单个值设置和批量设置
     *
     * @param string|array $key
     * @param string $value
     * @return void
     */
    public function setHeader($key, $value) {
        if (is_array($value))
            return $this->header = array_merge($this->header, $value);

        if ($key === null)
            $key                = count($this->header);
        if (!isset($this->header[$key]))
            $this->header[$key] = $value;
    }

    /**
     * 设置cookie,支持单个值设置和批量设置
     *
     * @param string|array $key
     * @param string $value
     */
    public function setCookie($key, $value = null) {
        if (!$key)
            return;
        if (is_array($key))
            $this->cookie       = array_merge($this->cookie, $key);
        else
            $this->cookie[$key] = $value;
    }

    /**
     * 设置data,支持单个值设置和批量设置
     *
     * @param string|array $key
     * @param string $value
     */
    public function setData($key, $value = null) {
        if (!$key)
            return;
        if (is_array($key))
            $this->data       = array_merge($this->data, $key);
        else
            $this->data[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getInfo() {
        return curl_getinfo($this->httpHandler);
    }

    protected function createHttpHandler() {
        return curl_init();
    }

    public function request($name, $value = null) {
        return curl_setopt($this->getHttpHandler(), $name, $value);
    }

    public function response() {
        return curl_exec($this->getHttpHandler());
    }

    /**
     * 打开一个http请求,返回 http请求句柄
     *
     * @return httpResource
     */
    protected function getHttpHandler() {
        return $this->httpHandler;
    }

    /**
     * 清理链接
     */
    public function __destruct() {
        $this->close();
    }

    /* (non-PHPdoc)
     * @see AbstractWindHttp::close()
     */
    public function close() {
        if (null === $this->httpHandler)
            return;
        curl_close($this->httpHandler);
        $this->httpHandler = null;
    }

    /* (non-PHPdoc)
     * @see AbstractWindHttp::getError()
     */
    public function getError() {
        $this->err = curl_error($this->getHttpHandler());
        $this->eno = curl_errno($this->getHttpHandler());
        return $this->err ? $this->eno . ':' . $this->err : '';
    }

    /* (non-PHPdoc)
     * @see AbstractWindHttp::send()
     */
    public function send($method = 'GET', $options = array()) {
        if ($this->data) {
            switch (strtoupper($method)) {
                case 'GET':
                    $_url = $this->argsToUrl($this->data);
                    $url  = parse_url($this->url);
                    $this->url .= (isset($url['query']) ? '&' : '?') . $_url;
                    break;
                case 'POST':
                    $this->request(CURLOPT_POST, 1);
                    $data = array();
                    $this->_resolvedData($this->data, $data);
                    $this->request(CURLOPT_POSTFIELDS, $data);
                    break;
                default:
                    break;
            }
        }
        //var_dump($this->url);
        $this->request(CURLOPT_HEADER, $this->_header);
        $this->request(CURLOPT_NOBODY, !$this->_body);
        $this->request(CURLOPT_TIMEOUT, $this->timeout);
        $this->request(CURLOPT_FOLLOWLOCATION, 0);
        $this->request(CURLOPT_RETURNTRANSFER, 1);
        if ($options && is_array($options)) {
            curl_setopt_array($this->httpHandler, $options);
        }
        if (!isset($options[CURLOPT_USERAGENT]))
            $this->request(CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)');

        $_cookie = '';
        foreach ($this->cookie as $key => $value) {
            $_cookie .= ($_cookie !== '' ? "" : "; ") . $key . "=" . $value;
        }
        $this->request(CURLOPT_COOKIE, $_cookie);

        $_header = array();
        foreach ($this->header as $key => $value) {
            $_header[] = $key . ": " . $value;
        }
        //$_header && $this->request(CURLOPT_HTTPHEADER, $_header);
        $this->request(CURLOPT_URL, $this->url);
        if (isset($options[CURLOPT_FOLLOWLOCATION]))
            $this->_redirects = $options[CURLOPT_FOLLOWLOCATION];
        if (isset($options[CURLOPT_MAXREDIRS]))
            $this->_maxRedirs = intval($options[CURLOPT_MAXREDIRS]);
        $this->followLocation();
        return $this->response();
    }

    /**
     * 解析post data使其支持数组格式传递
     *
     * @param array $args
     * @param array $value
     * @param string $key
     * @return array
     */
    private function _resolvedData($args, &$value, $key = null) {
        foreach ((array) $args as $_k => $_v) {
            if ($key !== null)
                $_k = $key . '[' . $_k . ']';
            if (is_array($_v)) {
                $this->_resolvedData($_v, $value, $_k);
            } else
                $value[$_k] = $_v;
        }
        return $value;
    }

    /* (non-PHPdoc)
     * @see AbstractWindHttp::getStatus()
     */

    public function getStatus() {
        return curl_getinfo($this->httpHandler, CURLINFO_HTTP_CODE);
    }

    /**
     * url forward 兼容处理
     */
    private function followLocation() {
        $_safeMode = ini_get('safe_mode');
        if (ini_get('open_basedir') == '' && ($_safeMode == '' || strcasecmp($_safeMode, 'off') == 0))
            return;
        if (!$this->_redirects)
            return;
        if ($this->_maxRedirs <= 0)
            return;
        $maxRedirs = $this->_maxRedirs;

        $newurl = curl_getinfo($this->httpHandler, CURLINFO_EFFECTIVE_URL);
        $rch    = curl_copy_handle($this->httpHandler);
        curl_setopt($rch, CURLOPT_HEADER, true);
        curl_setopt($rch, CURLOPT_NOBODY, true);
        curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
        curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
        do {
            curl_setopt($rch, CURLOPT_URL, $newurl);
            $header = curl_exec($rch);

            if (curl_errno($rch)) {
                $code = 0;
            } else {
                $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                if ($code == 301 || $code == 302) {
                    preg_match('/Location:(.*?)\n/', $header, $matches);
                    $newurl = trim(array_pop($matches));
                } else {
                    $code = 0;
                }
            }
        } while ($code && --$maxRedirs);
        curl_close($rch);
        curl_setopt($this->httpHandler, CURLOPT_URL, $newurl);
    }

    /**
     * 将数组格式的参数列表转换为Url格式，并将url进行编码处理
     *
     * <code>参数:array('b'=>'b','c'=>'index','d'=>'d')
     * 分割符: '&='
     * 转化结果:&b=b&c=index&d=d
     * 如果分割符为: '/' 则转化结果为: /b/b/c/index/d/d/</code>
     * @param array $args
     * @param boolean $encode 是否进行url编码 默认值为true
     * @param string $separator url分隔符 支持双字符,前一个字符用于分割参数对,后一个字符用于分割键值对
     * @return string
     */
    public function argsToUrl($args, $encode = true, $separator = '&=', $key = null) {
        if (strlen($separator) !== 2)
            return;
        $_tmp = '';
        foreach ((array) $args as $_k => $_v) {
            if ($key !== null)
                $_k = $key . '[' . $_k . ']';
            if (is_array($_v)) {
                $_tmp .= self::argsToUrl($_v, $encode, $separator, $_k) . $separator[0];
                continue;
            }
            $_v = $encode ? rawurlencode($_v) : $_v;
            if (is_int($_k)) {
                $_v && $_tmp .= $_v . $separator[0];
                continue;
            }
            $_k = ($encode ? rawurlencode($_k) : $_k);
            $_tmp .= $_k . $separator[1] . $_v . $separator[0];
        }
        return trim($_tmp, $separator[0]);
    }

}
