<?php
namespace Orq\Wxpay;

/**
 * 短链接转换接口
 */
class ShortUrl extends Client
{
    function __construct(WxpayConfigInterface $config) 
    {
        parent::__construct($config);
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/tools/shorturl";
        //设置curl超时时间
        $this->curl_timeout = $this->config->getCurlTimeout();       
    }
     
    /**
     * 生成接口参数xml
     */
    function createXml()
    {       
        try
        {
            if($this->parameters["long_url"] == null ) 
            {
                throw new SDKRuntimeException("短链接转换接口中，缺少必填参数long_url！"."<br>");
            }
            $this->parameters["appid"] = $this->config->getAppid();//公众账号ID
            $this->parameters["mch_id"] = $this->config->getMchid();//商户号
            $this->parameters["nonce_str"] = Utility::createNoncestr();//随机字符串
            $this->parameters["sign"] = Utility::getSign($this->parameters);//签名
            return  Utility::arrayToXml($this->parameters);
        }catch (SDKRuntimeException $e)
        {
            die($e->errorMessage());
        }
    }
     
    /**
     * 获取prepay_id
     */
    function getShortUrl()
    {
        $this->getResult();
        $short_url = $this->result["short_url"];
        return $short_url;
    }
}