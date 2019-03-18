<?php
namespace App\Common;
/**
 * RemoteClient
 * <br>使用示例：<br>
 * ```
 * $rs = RemoteClient::create()
 *   ->withHost('http://www.meetsocial.cn/')
 *   ->withService('Def/Index')
 *   ->withParams('name', 'dogstar')
 *   ->withTimeout(3000)
 *   ->request();
 *
 * var_dump($rs->getRet(), $rs->getData(), $rs->getMsg());
 * ```
 *
 * ===========
 * 并发使用示例
 * ```
 * $ret = RemoteClient::create()
 *   ->withTimeout(3000)
 *   ->withMultiParams(array(
 *          array('url'=>'Ad/getAdList','postData'=>array('page_size'=>2,'client_id'=>6877)),
 *          array('url'=>'Ad/getAdList','postData'=>array('page_size'=>2,'client_id'=>6877)),
 *    ))
 *   ->addMultiParams(array('url'=>'Ad/getAdList','postData'=>array('page_size'=>1,'client_id'=>6877)))
 *   ->multiRequest();
 * ```
 */
class RemoteClient
{
    const MAX_RETRY_TIMES = 10;
    protected $host;
    protected $filter;
    protected $parser;
    protected $service;
    protected $remote_service;
    protected $timeoutMs;
    protected $headers = [];
    protected $params = array();
    protected $retryTimes;
    protected $multiParams = array();
    protected $timeout;

    /**
     * 创建一个接口实例，注意：不是单例模式
     * @return RemoteClient
     */
    public static function create()
    {
        return new self();
    }

    protected function __construct($retryTimes = 1)
    {
        $this->host = "";
        $this->retryTimes = $retryTimes < self::MAX_RETRY_TIMES
            ? $retryTimes : self::MAX_RETRY_TIMES;
        $this->parser = new RemoteClientParserJson();
    }

    /**
     * 设置接口域名
     * @param string $host
     * @return RemoteClient
     */
    public function withHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * 设置过滤器，与服务器的DI()->filter对应
     * @param RemoteClientFilter $filter 过滤器
     * @return RemoteClient
     */
    public function withFilter(RemoteClientFilter $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * 设置结果解析器，仅当不是JSON返回格式时才需要设置
     * @param RemoteClientParser $parser 结果解析器
     * @return RemoteClient
     */
    public function withParser(RemoteClientParser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * 重置，将接口服务名称、接口参数、请求超时进行重置，便于重复请求
     * @return RemoteClient
     */
    public function reset()
    {
        $this->service = "";
        $this->timeoutMs = 3000;
        $this->timeout = 20;
        $this->params = array();
        $this->multiParams = array();

        return $this;
    }

    /**
     * 设置将在调用的接口服务名称，如：Default.Index
     * @param string $service 接口服务名称
     * @return RemoteClient
     */
    public function withService($service)
    {
        $this->service = $service;
        return $this;
    }

    public function remoteService($remote_service)
    {
        $this->remote_service = $remote_service;
        return $this;
    }

    /**
     * 设置接口参数，此方法是唯一一个可以多次调用并累加参数的操作
     * @param string $name 参数名字
     * @param string $value 值
     * @return RemoteClient
     */
    public function withParams($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }
    /**
     * 设置并发性请求数据
     * @param array $multiParams
     * @multiParams : url  postData  header
     * @return RemoteClient
     */
    public function withMultiParams($multiParams){
        $this->multiParams = $multiParams;
        return $this;
    }
    /**
     * 增加并发请求数据
     * @param array $multiParams
     * @return RemoteClient
     */
    public function addMultiParams($multiParams){
        $this->multiParams[] = $multiParams;
        return $this;
    }

    /**
     * 设置接口参数 多参数设置
     * @param $params
     */
    public function setParams($params = array())
    {
        if (empty($params)) {
            return $this;
        }

        foreach ($params as $key => $param) {
            $this->params[$key] = $param;
        }
        return $this;
    }
    public function setAllParams($params = array())
    {
        if (empty($params)) {
            return $this;
        }
        $this->params = [];
        foreach ($params as $key => $param) {
            $this->params[$key] = $param;
        }
        return $this;
    }
    /**
     * 设置超时时间，单位毫秒
     * @param int $timeoutMS 超时时间，单位毫秒
     * @return RemoteClient
     */
    public function withTimeout($timeoutMS)
    {
        $this->timeoutMS = $timeoutMS;
        return $this;
    }
    /**
     * 设置超时时间，单位秒
     * @param int $timeoutMS 超时时间，单位毫秒
     * @return RemoteClient
     */
    public function withTimeoutS($timeoutS)
    {
        $this->timeout = $timeoutS;
        return $this;
    }
    public function withHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * GET方式的请求
     * @param string $url 请求的链接
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     */
    public function get($url, $timeoutMs = 3000)
    {
        return $this->toRequest($url, FALSE, $timeoutMs);
    }

    /**
     * POST方式的请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     */
    public function post($url, $data, $timeoutMs = 3000)
    {
        return $this->toRequest($url, $data, $timeoutMs);
    }

    /**
     * 统一接口请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     */
    protected function toRequest($url, $data, $timeoutMs = 3000)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while ($rs === FALSE && $curRetryTimes >= 0);

        curl_close($ch);

        return $rs;
    }

    /**
     * 发起接口请求
     * @return RemoteClientResponse
     */
    public function request()
    {
        $url = $this->host;

        if (!empty($this->remote_service)) {
            $url .= '/?service=' . $this->remote_service;
        }

        if (!empty($this->service)) {
            $url .= '/' . $this->service;
        }

        if ($this->filter !== null) {
            $this->filter->filter($this->service, $this->params);
        }
#echo $url;
#echo "\n\n";

        $rs = $this->doRequest($url, $this->params, $this->timeoutMs);
        return $this->parser->parse($rs);
    }

    protected function doRequest($url, $data, $timeoutMs = 3000)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $rs = curl_exec($ch);

        curl_close($ch);
//        if(\think\Config::get('DEBUG_REMOTE')==1){
//            $debug = [];
//            $debug['url'] = $url;
//            $debug['data'] = $data;
//            $debug['result'] = $rs;
//            print_r($debug);
//            exit;
//        }
        return $rs;
    }

    //============  并发请求改造 START==========

    /**
     * 多个http请求同时执行
     * @param array $requestList
     * @return array
     */
    public  function MultiRequest(){
        $requestList = $this->multiParams;
        // 创建curl对象,存放到数组,添加到下载器中
        $requestCurlObjectList = array();
        $downloader = curl_multi_init();
        foreach ($requestList as $row){
            $url        = isset($row['url'])      ? $this->host . '/' . $row['url'] : '';
            $postData   = isset($row['postData']) ? $row['postData'] : array();
            $header     = isset($row['header'])   ? $row['header'] : array();
            $timeOut    = isset($row['timeOut'])  ? $row['timeOut'] : $this->timeout;
            $proxy      = isset($row['proxy'])    ? $row['proxy'] : '';

            $tmpCurlObject = $this->buildCurlObject($url,$postData,$header,$timeOut,$proxy);
            if(isset($row['key']) && !empty($row['key'])){
                $requestCurlObjectList[$row['key']] = $tmpCurlObject;
            }else{
                $requestCurlObjectList[] = $tmpCurlObject;
            }

            curl_multi_add_handle($downloader,$tmpCurlObject);
        }

        // 并行执行多个curl对象,等待所有请求完毕退出循环
        $active = true;
        $mrc = CURLM_OK;
        while ($active && $mrc == CURLM_OK) {
            do {
                $mrc = curl_multi_exec($downloader, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            if (curl_multi_select($downloader) == -1) {
                usleep(100);
            }
        }

        // 解析每一个请求对象
        $responseList = array();
        foreach ($requestCurlObjectList as $key=>$ch){
            $rel = curl_multi_getcontent($ch);
            $response = $this->fetchResponse($ch, $rel);
            $responseList[$key] = $response;

            curl_multi_remove_handle($downloader, $ch);
            curl_close($ch);
        }
        curl_multi_close($downloader);
//        var_dump($responseList);
        return $this->parser->multiParse($responseList);
//        return $responseList;
    }
    private function buildCurlObject($url, $postData, $header, $timeOut, $proxy){
        // 构造url请求
        $options = array();
        $url = trim($url);
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_TIMEOUT] = (int)$timeOut;
//        $options[CURLOPT_USERAGENT] = self::DEFAULT_USER_AGENT;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HEADER] = true;

        // 配置代理
        if (!empty($proxy)){
            $options[CURLOPT_PROXY] = $proxy;
        }

        // 合并请求头部信息
        foreach($header as $key=>$value){
            $options[$key] =$value;
        }

        // 是否是post请求
        if(!empty($postData) && is_array($postData)){
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($postData);
        }

        // 是否是https
        if(stripos($url,'https') === 0){
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }

        // 返回curl对象
        $ch = curl_init();
        curl_setopt_array($ch,$options);
        return $ch;
    }
    /**
     * 从curl对象中提取请求的结果
     * @param $ch
     * @param $rel
     * @return array
     */
    private function fetchResponse($ch, $rel){
        $response = array(
            'status' => false,
            'code' => 0,
            'header' => array(),
            'body' => '',
            'extraInfo' => array(),
            'errorInfo' => array()
        );
        $response['extraInfo'] = curl_getinfo($ch);
        if($rel == false){
            $error = array();
            $error['code'] = curl_errno( $ch );
            $error['info'] = curl_error($ch);
            $response['errorInfo'] = $error;
        }else{
            // 切割header 与 body
            $header_body = explode("\r\n\r\n",$rel);
            do{
                if (count($header_body) !== 2){
                    $error =array();
                    $error['code'] = 0;
                    $error['info'] = 'split header and body error';
                    $error['list'] = $rel;
                    $response['errorInfo'] = $error;
                    break;
                }

                // 格式化返回结果
                $response['body'] = $header_body[1];
                $response['header'] = $this->parseResponseHeader($header_body[0]);
                $response['code'] = $response['header']['Status'];
                $response['status'] = true;
            }while(false);
        }

        return $response;
    }
    /**
     * 将字符串解析为http响应头部数组
     * @param $strResponseHeader
     * @return array
     */
    private function parseResponseHeader($strResponseHeader){
        $headerList = array();
        $tempHeaderList = explode("\r\n",$strResponseHeader);
        foreach ($tempHeaderList as $row){
            if (stripos($row,':') === false){
                $tmp = explode(" ",$row);
                $headerList['Protocol'] = isset($tmp[0])?$tmp[0]:'';
                $headerList['Status'] = (int)(isset($tmp[1])?$tmp[1]:0);
                $headerList['Message'] = isset($tmp[2])?$tmp[2]:'';
            }else{
                $tmp = explode(":",$row, 2);
                if (count($tmp) != 2){
                    continue;
                }
                $key = trim($tmp[0]);
                $value = trim($tmp[1]);
                if ($key == 'Set-Cookie'){
                    if (!isset($headerList[$key])){
                        $headerList[$key] = array();
                    }
                    $tmpCookieList = explode(";",$value);
                    foreach ($tmpCookieList as $oneCookie){
                        $tmpCookie = explode("=",$oneCookie,2);
                        if (count($tmpCookie) != 2){
                            continue;
                        }
                        $key_cookie = trim($tmpCookie[0]);
                        $value_cookie = trim($tmpCookie[1]);
                        $headerList[$key][$key_cookie] = $value_cookie;
                    }
                }else{
                    $headerList[$key] = $value;
                }
            }
        }

        return $headerList;
    }


    // ================== 并发请求改造 END ===============
}

/**
 * 接口返回结果
 *
 * - 与接口返回的格式对应，即有：ret/data/msg
 */
class RemoteClientResponse
{

    protected $code = 200;
    protected $data = array();
    protected $msg = '';

    public function __construct($ret, $data = array(), $msg = '')
    {
        $this->code = $ret;
        $this->data = $data;
        $this->msg = $msg;
    }

    public function getRet()
    {
        return $this->code;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMsg()
    {
        return $this->msg;
    }
}

/**
 * 接口过滤器
 *
 * - 可用于接口签名生成
 */
interface RemoteClientFilter
{

    /**
     * 过滤操作
     * @param string $service 接口服务名称
     * @param array $params 接口参数，注意是引用。可以直接修改
     * @return null
     */
    public function filter($service, array &$params);
}

/**
 * 接口结果解析器
 *
 * - 可用于不同接口返回格式的处理
 */
interface RemoteClientParser
{

    /**
     * 结果解析
     * @param string $apiResult
     * @return RemoteClientResponse
     */
    public function parse($apiResult);
}

/**
 * JSON解析
 */
class RemoteClientParserJson implements RemoteClientParser
{

    public function parse($apiResult)
    {
        $arr = json_decode($apiResult, true);
        if( !isset($arr['code']) && $apiResult && !is_bool($apiResult) ){
            print_r($apiResult);
            exit();
        }
        if ($arr === false || empty($arr)) {
            return new RemoteClientResponse(500, array(), 'Internal Server Error');
        }

        if ($apiResult === false) {
            return new RemoteClientResponse(408, array(), 'Request Timeout');
        }

        $message = isset($arr['msg']) ? $arr['msg'] : (isset($arr['message']) ? $arr['message'] : '');
        return new RemoteClientResponse($arr['code'], $arr['data'], $message);
    }
    /**
     * 解析并发请求返回结果
     */
    public function multiParse($apiResult)
    {
        if ($apiResult === false) {
            return new RemoteClientResponse(408, array(), 'Request Timeout');
        }
        $data = [];
        foreach($apiResult as $key => $value){
            $arr = json_decode($value['body'], true);

            if ($arr === false || empty($arr)) {
                $data[$key]['code'] = 500;
                $data[$key]['data'] = array();
                $data[$key]['msg'] = 'Internal Server Error';
//                return new RemoteClientResponse(500, array(), 'Internal Server Error');
            }else{
                $message = isset($arr['msg']) ? $arr['msg'] : (isset($arr['message']) ? $arr['message'] : '');
                $data[$key]['code'] = $arr['code'];
                $data[$key]['data'] = $arr['data'];
                $data[$key]['msg'] = $message;
//                return new RemoteClientResponse($arr['code'], $arr['data'], $message);
            }

        }
        return new RemoteClientResponse(200,$data,'');

    }
}