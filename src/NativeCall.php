<?php
namespace Orq\Wxpay;

 
/**
 * 请求商家获取商品信息接口
 */
class NativeCall extends Server
{
    /**
     * 生成接口参数xml
     */
    public function createXml()
    {
        if($this->returnParameters["return_code"] == "SUCCESS"){
            $this->returnParameters["appid"] = $this->config->getAppid();//公众账号ID
            $this->returnParameters["mch_id"] = $this->config->getMchid();//商户号
            $this->returnParameters["nonce_str"] = Utility::createNoncestr();//随机字符串
            $this->returnParameters["sign"] = Utility::getSign($this->returnParameters, $this->config);//签名
        }
        return Utility::arrayToXml($this->returnParameters);
    }
     
    /**
     * 获取product_id
     */
    function getProductId()
    {
        $product_id = $this->data["product_id"];
        return $product_id;
    }
     
}