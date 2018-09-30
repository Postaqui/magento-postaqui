<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
require 'HttpCarrier.php';

class Linebus_Postaqui_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'linebus_postaqui';
    protected $carrier;

    public function __construct(){
        $this->carrier = new HttpCarrier(
            'http://api.postaquilog.com.br:3100/shipping-company/calc-price-deadline',
            $this->getConfigData('auth'));

        /* Cart Info */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $weight = $quote->getShippingAddress()->getWeight();
        if(isset($_REQUEST['zip_id']) && !empty($_REQUEST['zip_id'])) // CEP que vem da página do produto
            $zipcode = $_REQUEST['zip_id'];
        else
            $zipcode = $quote->getShippingAddress()->getPostcode();
        /* // */

        $post = ($this->carrier->post(array("cepOrigem" => $this->getConfigData('ceporigem'),
            "cepDestino" => $zipcode,
            "peso" => $weight,
            "altura" => 10,
            "largura" => 10,
            "comprimento" => 10)));
        $_SESSION['token'] = $this->getConfigData('auth');
        $_SESSION['methods_delivery_linebus'] = $post->data;
        $this->carrier->stripCarrierServices($post->data);
    }

    public function collectRates(
    Mage_Shipping_Model_Rate_Request $request
    ) {
        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */
        if(count($this->carrier->data)){
            foreach ($this->carrier->data as $carrier){
                $result->append($this->_getStandardShippingRate($carrier));
            }
        }
        //$result->append($this->_getExpressShippingRate());

        $quote = Mage::getModel('sales/quote');
        $quote->getShippingAddress()->collectTotals();
        $quote->getShippingAddress()->setCollectShippingRates(true);

        return $result;
    }

    protected function _getStandardShippingRate($carrier) {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */

        $rate->setCarrier($this->_code);
        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        $rate->setMethod($carrier->type_send);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethodTitle($carrier->name.' ('.$carrier->deadline.') - ');
        $rate->setPrice($carrier->price_finish);
        $rate->setCost(0);

        return $rate;
    }

    protected function _getExpressShippingRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setMethod($this->carrier->express->type_send);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethodTitle($this->carrier->express->name.' ('.$this->carrier->express->deadline.') - ');
        $rate->setPrice($this->carrier->express->price_finish);
        $rate->setCost(0);
        return $rate;
    }

    public function getAllowedMethods() {
        return array(
            'standard' => 'Standard',
            'express' => 'Express',
        );
    }

}
