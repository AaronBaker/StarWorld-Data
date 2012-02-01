<?php
class OtpComponent extends Object{
    var $components = array('Auth');
    
    function createOTP($parameters){
           return  $this->Auth->password(implode("", $parameters));
    }

    function authenticateOTP($otp,$parameters ){
        return $otp== $this->Auth->password(implode("", $parameters));
    }

}

?>
