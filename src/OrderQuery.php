<?php
namespace Orq\Wxpay;

/**
 * 订单查询接口
 */
class OrderQuery extends Client
{
    function __construct(WxpayConfigInterface $config) 
    {
        parent::__construct($config);
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
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
            //检测必填参数
            if($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) 
            {
                throw new SDKRuntimeException("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
            }
            $this->parameters["appid"] = $this->config->getAppid();//公众账号ID
            $this->parameters["mch_id"] = $this->config->getMchid();//商户号
            $this->parameters["nonce_str"] = Utility::createNoncestr();//随机字符串
            $this->parameters["sign"] = Utility::getSign($this->parameters, $this->config);//签名
            return  Utility::arrayToXml($this->parameters);
        }catch (SDKRuntimeException $e)
        {
            throw $e;
        }
    }
 
}