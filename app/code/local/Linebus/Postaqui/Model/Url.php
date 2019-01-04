<?php
/**
 * Created by PhpStorm.
 * User: Junior Maia
 * Date: 04/01/19
 * Time: 13:49
 */

class Linebus_Postaqui_Model_Url extends Mage_Core_Model_Abstract {

    private $ambiente;

    public function __construct(){
       $this->ambiente = Mage::getStoreConfig('carriers/linebus_postaqui/ambiente');
    }
    public function getUrlCalc() {

        $sandobox = 'http://api-test.postaquilogistica.com.br:4100/shipping-company/calc-price-deadline';
        $producao = 'http://api.postaquilog.com.br:3100/shipping-company/calc-price-deadline';

        if ($this->ambiente == 0) {
            return $sandobox;
        } else {
            return $producao;
        }
    }

    public function getUrlTicket() {

        $sandobox = 'http://api-test.postaquilogistica.com.br:4100/tickets';
        $producao = 'http://api.postaquilog.com.br:3100/tickets';

        if ($this->ambiente == 0) {
            return $sandobox;
        } else {
            return $producao;
        }
    }
}