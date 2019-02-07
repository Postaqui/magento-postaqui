<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
//require 'HttpCarrier.php';

class Linebus_Postaqui_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'linebus_postaqui';
    protected $carrier;

    protected $postaqui_comprimento;
    protected $postaqui_altura;
    protected $postaqui_largura;
    protected $postaqui_valor_declarado;

    public function __construct(){

        $_url = Mage::getModel('linebus_postaqui/url')->getUrlCalc();

        /*
        $this->carrier = new HttpCarrier(
            $_url,
            $this->getConfigData('auth'));
        */

        $modelHttp = Mage::getModel('linebus_postaqui/http');
        $modelHttp->setUrl($_url);

        $this->carrier = $modelHttp;

        /* Cart Info */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $weight = $quote->getShippingAddress()->getWeight();

        if(isset($_REQUEST['zip_id']) && !empty($_REQUEST['zip_id'])) // CEP que vem da página do produto
            $zipcode = $_REQUEST['zip_id'];
        else
            $zipcode = $quote->getShippingAddress()->getPostcode();
        /* // */

        /* Preço dos produtos */
        $items = $quote->getAllVisibleItems();

        $subtotal = $quote->getData('base_subtotal');

        $this->postaqui_valor_declarado = 0;
        $this->postaqui_comprimento = 0;
        $this->postaqui_altura = 0;
        $this->postaqui_largura = 0;

        foreach ($items as $item) {
            $_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
            $this->postaqui_valor_declarado += $item->getPrice();
            $this->postaqui_comprimento += Mage::getModel('linebus_postaqui/product')->getComprimento($_product);
            $this->postaqui_altura += Mage::getModel('linebus_postaqui/product')->getAltura($_product);
            $this->postaqui_largura += Mage::getModel('linebus_postaqui/product')->getLargura($_product);
        }


        $params = array(
            "cepOrigem" => $this->getConfigData('ceporigem'),
            "cepDestino" => $zipcode,
            "peso" => $weight,
            "valorDeclarado" => $this->postaqui_valor_declarado,
            "altura" => $this->postaqui_altura,
            "largura" => $this->postaqui_largura,
            "comprimento" => $this->postaqui_comprimento);

        $post = ($this->carrier->post($params));


//        echo '<pre>';
//        print_r($params);
//        print_r($post);
//        die();


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

                //verifica se está habilitado para mostrar
                if (!$carrier->disabled) {
                    $result->append($this->_getStandardShippingRate($carrier));
                }
            }
        }

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
