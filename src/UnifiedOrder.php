<?php
namespace Orq\Wxpay;

 
/**
 * 统一支付接口类
 */
class UnifiedOrder extends Client
{   
    function __construct(WxpayConfigInterface $config) 
    {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //设置curl超时时间
        $this->curl_timeout =$config->getCurlTimeout();
        parent::__construct($config);
    }
     
    /**
     * 生成接口参数xml
     */
    protected function createXml()
    {
        try
        {
            //检测必填参数
            if($this->parameters["out_trade_no"] == null) 
            {
                throw new SDKRuntimeException("缺少统一支付接口必填参数out_trade_no！"."<br>");
            }elseif($this->parameters["body"] == null){
                throw new SDKRuntimeException("缺少统一支付接口必填参数body！"."<br>");
            }elseif ($this->parameters["total_fee"] == null ) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数total_fee！"."<br>");
            }elseif ($this->parameters["notify_url"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数notify_url！"."<br>");
            }elseif ($this->parameters["trade_type"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数trade_type！"."<br>");
            }elseif ($this->parameters["trade_type"] == "JSAPI" &&
                $this->parameters["openid"] == NULL){
                throw new SDKRuntimeException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！"."<br>");
            }
            $this->parameters["appid"] = $this->config->getAppid();//公众账号ID
            $this->parameters["mch_id"] = $this->config->getMchid();//商户号
            $this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];//终端ip       
            $this->parameters["nonce_str"] = Utility::createNoncestr();//随机字符串
            $this->parameters["sign"] = Utility::getSign($this->parameters, $this->config);//签名
            $xyz = Utility::arrayToXml($this->parameters);
            return  $xyz;
        }catch (SDKRuntimeException $e)
        {
            die($e->errorMessage());
        }
    }
     
    /**
     * 获取prepay_id
     */
    function getPrepayId()
    {
        $this->postXml();
        $this->result = Utility::xmlToArray($this->response);
        // file_put_contents('Debug.txt', date('Y-m-d H:i:s').' result of getting prepay_id '.json_encode($this->result)."\r\n", FILE_APPEND);
        if (!$this->result['prepay_id']) {
            return json_encode($this->result);
        } else {
            $prepay_id = $this->result["prepay_id"];
            return $prepay_id;
        }
    }
     
}