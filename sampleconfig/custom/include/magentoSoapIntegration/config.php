<?php

/**
 * To change this template, choose Tools | Templates
 * @author Dhaval Darji
 */
$mage_url = 'http://localhost.biorbyt.com/index.php/api/?wsdl';
$mage_user = 'OrderStatus';
$mage_api_key = '123456789';

$soap = new SoapClient($mage_url);
try {
    $session_id = $soap->login($mage_user, $mage_api_key);
} catch (Exception $e) {
    
}
?>
