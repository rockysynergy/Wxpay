<?php
namespace Orq\Wxpay;

/**
 * 静态链接二维码
 */
class NativeLink
{
    var $parameters;//静态链接参数
    var $url;//静态链接
    protected $config;
 
    public function __construct(WxpayConfigInterface $config) 
    {
        $this->config = $config;
    }
     
    /**
     * 设置参数
     */
    public function setParameter($parameter, $parameterValue) 
    {
        $this->parameters[Utility::trimString($parameter)] = Utility::trimString($parameterValue);
    }
     
    /**
     * 生成Native支付链接二维码
     */
    public function createLink()
    {
        try
        {       
            if($this->parameters["product_id"] == null) 
            {
                throw new SDKRuntimeException("缺少Native支付二维码链接必填参数product_id！"."<br>");
            }           
            $this->parameters["appid"] = $this->config->getAppid();//公众账号ID
            $this->parameters["mch_id"] = $this->config->getMchid();//商户号
            $time_stamp = time();
            $this->parameters["time_stamp"] = "$time_stamp";//时间戳
            $this->parameters["nonce_str"] = Utility::createNoncestr();//随机字符串
            $this->parameters["sign"] = Utility::getSign($this->parameters, $this->config);//签名         
            $bizString = Utility::formatBizQueryParaMap($this->parameters, false);
            $this->url = "weixin://wxpay/bizpayurl?".$bizString;
        }catch (SDKRuntimeException $e)
        {
            die($e->errorMessage());
        }
    }
     
    /**
     * 返回链接
     */
    public function getUrl() 
    {       
        $this->createLink();
        return $this->url;
    }
}