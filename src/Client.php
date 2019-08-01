<?php
namespace Orq\Wxpay;


/**
* 请求型接口的基类
*/
class Client
{
    var $parameters;//请求参数，类型为关联数组
    public $response;//微信返回的响应
    public $result;//返回参数，类型为关联数组
    var $url;//接口链接
    var $curl_timeout;//curl超时时间
    protected $config;

    function __construct(WxpayConfigInterface $config) {
        $this->config = $config;
    }

    /**
     *  作用：设置请求参数
     */
    public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[Utility::trimString($parameter)] = Utility::trimString($parameterValue);
    }
     
    /**
     *  作用：设置标配的请求参数，生成签名，生成接口参数xml
     */
    protected function createXml()
    {
        $this->parameters["appid"] = $this->config->getAppid();//公众账号ID
        $this->parameters["mch_id"] = $this->config->getMchid();//商户号
        $this->parameters["nonce_str"] = Utility::createNoncestr();//随机字符串
        $this->parameters["sign"] = Utility::getSign($this->parameters, $this->config);//
        $abc =Utility::arrayToXml($this->parameters);;
        return  $abc;
    }
     
    /**
     *  作用：post请求xml
     */
    public function postXml()
    {
        $xml = $this->createXml();
        $this->response = Utility::postXmlCurl($xml,$this->url, $this->curl_timeout);
        return $this->response;
    }
     
    /**
     *  作用：使用证书post请求xml
     */
    public function postXmlSSL()
    {   
        $xml = $this->createXml();
        $this->response = Utility::postXmlSSLCurl($xml,$this->url,$this->curl_timeout);
        return $this->response;
    }
 
    /**
     *  作用：获取结果，默认不使用证书
     */
    public function getResult() 
    {       
        $this->postXml();
        $this->result = Utility::xmlToArray($this->response);
        return $this->result;
    }
}