<?php
namespace Orq\Wxpay;

 
/**
 * 响应型接口基类
 */
class Server 
{

    public $data;//接收到的数据，类型为关联数组
    var $returnParameters;//返回参数，类型为关联数组
    protected $config;

    public function __construct(WxpayConfigInterface $config) {
        $this->config = $config;
    }
     
    /**
     * 将微信的请求xml转换成关联数组，以方便数据处理
     */
    public function saveData($xml)
    {
        $this->data = Utility::xmlToArray($xml);
    }
     
    function checkSign()
    {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = Utility::getSign($tmpData);//本地签名
        if ($this->data['sign'] == $sign) {
            return TRUE;
        }
        return FALSE;
    }
     
    /**
     * 获取微信的请求数据
     */
    function getData()
    {       
        return $this->data;
    }
     
    /**
     * 设置返回微信的xml数据
     */
    function setReturnParameter($parameter, $parameterValue)
    {
        $this->returnParameters[Utility::trimString($parameter)] = Utility::trimString($parameterValue);
    }
     
    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        return Utility::arrayToXml($this->returnParameters);
    }
     
    /**
     * 将xml数据返回微信
     */
    function returnXml()
    {
        $returnXml = $this->createXml();
        return $returnXml;
    }
}