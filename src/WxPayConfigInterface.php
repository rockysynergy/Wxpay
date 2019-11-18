<?php
namespace Orq\Wxpay;

interface WxPayConfigInterface {
    public function getAppid();
    public function getMchid();
    public function getCurlTimeout();
    public function getAppSecret();
    public function getKey();
    public function getSslCertPath();
    public function getSslKeyPath();
    public function getServerIp();
}