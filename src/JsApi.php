<?php
namespace Orq\Wxpay;

/**
* JSAPI支付——H5网页端调起支付接口
*/
class JsApi
{
    var $code;//code码，用以获取openid
    var $openid;//用户的openid
    var $parameters;//jsapi参数，格式为json
    var $prepay_id;//使用统一支付接口得到的预支付id
    var $curl_timeout;//curl超时时间
    protected $config;
 
    public function __construct(WxpayConfigInterface $config) 
    {
        //设置curl超时时间
        $this->curl_timeout = $config->getCurlTimeout();
        $this->config = $config;
    }
     
    /**
     *  作用：生成可以获得code的url
     */
    public function createOauthUrlForCode($redirectUrl)
    {
        // $urlObj["appid"] = $this->config->getAppid();
        // $urlObj["redirect_uri"] = urlencode($redirectUrl);
        // $urlObj["response_type"] = "code";
        // $urlObj["scope"] = "snsapi_base";
        // $urlObj["state"] = "STATE"."#wechat_redirect";
        // $bizString = $this->formatBizQueryParaMap($urlObj, false);
        // return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
         
         
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->config->getAppid()."&redirect_uri=".urlencode($redirectUrl)."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        return $url;
    }
 
    /**
     *  作用：生成可以获得openid的url
     */
    public function createOauthUrlForOpenid()
    {
        $urlObj["appid"] = $this->config->getAppid();
        $urlObj["secret"] = $this->config->getAppSecret();
        $urlObj["code"] = $this->code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = Utility::formatBizQueryParaMap($urlObj, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }
     
     
    /**
     *  作用：通过curl向微信提交code，以获取openid
     */
    public function getOpenid()
    {
        $url = $this->createOauthUrlForOpenid();
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以json形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res,true);
        $this->openid = $data['openid'];
        return $this->openid;
    }
 
    /**
     *  作用：设置prepay_id
     */
    public function setPrepayId($prepayId)
    {
        $this->prepay_id = $prepayId;
    }
 
    /**
     *  作用：设置code
     */
    public function setCode($code_)
    {
        $this->code = $code_;
    }

    /**
     *  Set openid
     */
    public function setOpenid($openid_)
    {
        $this->openid = $openid_;
    }
 
    /**
     *  作用：设置jsapi的参数
     */
    public function getParameters()
    {
        $jsApiObj["appId"] = $this->config->getAppid();
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = Utility::createNoncestr();
        $jsApiObj["package"] = "prepay_id=$this->prepay_id";
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = Utility::getSign($jsApiObj, $this->config);
        $this->parameters = json_encode($jsApiObj);
         
        return $this->parameters;
    }
}