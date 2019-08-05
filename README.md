# wxpay
微信支付开发PHP库

# 安装
`composer require orq/wxpay`

# 手册

## 公众号支付

1. [开发文档](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_1)
2. 代码调用示例：

```JavaScript
// 提交订单表单，后台获取支付params后，调用
$.post('/make_order', function (res) {
	jsApiCall(res);
});

// 支付
        //====微信支付
        //调用微信JS api 支付
		let jsApiCall = function jsApiCall(jsApiParameters) {
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				jsApiParameters,
				function (res) {
					WeixinJSBridge.log(res.err_msg);
					switch (res.err_msg){ 
						case 'get_brand_wcpay_request:cancel':   
							alert('请尽快完成支付'); 
							break; 
						case 'get_brand_wcpay_request:fail': 
							alert('支付失败') 
							break; 
						case 'get_brand_wcpay_request:ok': 
							alert('已提交审核，通过审核的会员请再次登录发布供需和优品');
							window.location = 'http://whlm.fs007.com.cn/?wd.html';
							break; 
					} 
					//alert(res.err_code+res.err_desc+res.err_msg);
				}
			);
		};
 
        let callpay = function callpay() {
            if (typeof WeixinJSBridge == "undefined"){
                alert('uuuuu');
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                alert("ok");
                jsApiCall(jsApiParameters);
            }
        };
```

```PHP
    // 支付

    public function makeOrder() {
        $prepay_id = $this->makeUnifiedOder($order, 'test_prod', $_GET['code']);
        $JsApi = new \Orq\Wxpay\JsApi();
        $jsApi->setPrepayId($prepay_id);
        return response()->json(json_decode($jsApi->getParameters(), true));
    }

    /**
     * 统一下单
     */
    protected function makeUnifiedOder($order, $item_title, $code) {
        $pay_amount = $order->pay_amount * 100;
        $unifiedOrder = new \Orq\Wxpay\UnifiedOrder();
        $JsApi = new \Orq\Wxpay\JsApi();
        $jsApi->setCode($code);
        $openId = $JsApi->getOpenId();

		$unifiedOrder->setParameter("openid", $openID);
		$unifiedOrder->setParameter("body", $item_title);//商品描述
		//自定义订单号，此处仅作举例
		// $timeStamp = time();
		$unifiedOrder->setParameter("out_trade_no", $order->no);//商户订单号 
		$unifiedOrder->setParameter("total_fee", $pay_amount);//总金额以分位单位必须是整数
		$unifiedOrder->setParameter("notify_url",\Orq\Wxpay\$this->config->getAppSecret()NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
        // file_put_contents('Debug.txt', date('Y-m-d H:i:s').' Finished settingData:'."\r\n", FILE_APPEND);
	 
        $prepay_id = $unifiedOrder->getPrepayId();
        // file_put_contents('Debug.txt', date('Y-m-d H:i:s').' prepay_id '.$prepay_id."\r\n", FILE_APPEND);
        return $prepay_id;
    }
    
    // 假如已经有openId则可以使用这个来统一下单
	protected function makeUnifiedOder($order, $item_title) {
		$unifiedOrder = new \Orq\Wxpay\UnifiedOrder();
		$unifiedOrder->setParameter("openid", session('openid'));//用户openid
		$unifiedOrder->setParameter("body", $item_title);//商品描述
		//自定义订单号，此处仅作举例
		$timeStamp = time();
		$unifiedOrder->setParameter("out_trade_no", $order->no);//商户订单号 
		$unifiedOrder->setParameter("total_fee", $order->pay_amount_);//总金额
		$unifiedOrder->setParameter("notify_url",$this->config->getAppSecret()NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
	 
		$prepay_id = $unifiedOrder->getPrepayId();
        return $prepay_id;
    }

    // 支付通知处理
        public function handleNotify() {
            $str = file_get_contents('php://input');
                
            $arr = \Orq\Wxpay\Utility::xmlToArray($str);
            $okMsg = ['return_code'=>'SUCCESS', 'return_msg'=>'OK'];

            $orderRepository = new OrderRepository();
            $order = $orderRepository->findByOrderNumber($arr['out_trade_no']);
            if (!$order || $order->getPayStatus()->getKey() == '2') {
                    return \Orq\Wxpay\Utility::arrayToXml($okMsg);
            } 
            
            $sign = $arr['sign'];
            $aSign = \Orq\Wxpay\Utility::getSign(array_diff_key($arr, ['sign'=>'']));
            if ($aSign === $sign) {
                if ($arr['total_fee'] == $order->getPayAmount()*100) {
                    try {
                        $order->setPayStatus('2');
                        $order->setUpdatedAt(date('Y-m-d H:i:s'));
                        $orderRepository->updateOrder($order);
                        return $util->arrayToXml($okMsg);
                    } catch (RepositoryException $e) {
                        Log::error('更新订单支付状态失败！OrderNumber: '.$order->getOrderNumber().' Message: '.$e->getMessage());
                    }
                }
            } else {
                throw new \Exception('签名不符');
            }
        }
````

