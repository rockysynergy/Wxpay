<?php
 namespace Orq\Wxpay;

class  SDKRuntimeException extends \Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
 
}
 
?>