<?php
namespace Orq\Wxpay;

/**
 * 对账单接口
 */
class DownloadBill extends Client
{
    protected $config;

    public function __construct(WxpayConfigInterface $config) 
    {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/downloadbill";
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
            if($this->parameters["bill_date"] == null ) 
            {
                throw new SDKRuntimeException("对账单接口中，缺少必填参数bill_date！"."<br>");
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
     *  作用：获取结果，默认不使用证书
     */
    public function getResult() 
    {       
        $this->postXml();
        $this->result = Utility::xmlToArray($this->result_xml);
        return $this->result;
    }
 
}