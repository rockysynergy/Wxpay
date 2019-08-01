<?php
namespace Orq\Wxpay;

 
/**
 * 退款查询接口
 */
class RefundQuery extends Client
{
     
    public function __construct(WxpayConfigInterface $config) 
    {
        parent::__construct($config);
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //设置curl超时时间
        $this->curl_timeout = $this->config->getCurlTimeout();       
    }
     
    /**
     * 生成接口参数xml
     */
    public function createXml()
    {       
        try
        {
            if($this->parameters["out_refund_no"] == null &&
                $this->parameters["out_trade_no"] == null &&
                $this->parameters["transaction_id"] == null &&
                $this->parameters["refund_id "] == null) 
            {
                throw new SDKRuntimeException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！"."<br>");
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
     *  作用：获取结果，使用证书通信
     */
    public function getResult() 
    {       
        $this->postXmlSSL();
        $this->result = Utility::xmlToArray($this->response);
        return $this->result;
    }
 
}