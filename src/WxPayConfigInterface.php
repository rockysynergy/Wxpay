<?php
namespace Orq\Wxpay;

interface WxpayConfigInterface {
    public function getAppid();
    public function getMchid();
    public function getCurlTimeout();
    public function getAppSecret();
    public function getKey();
    public function getSslCertPath();
    public function getSslKeyPath();
}