<?php
session_start();
require 'HttpCarrier.php';

class Linebus_Postaqui_Model_Observer extends Varien_Event_Observer {

    protected $_code = 'linebus_postaqui';
    protected $carrier;


    public function __construct(){
        $this->carrier = new HttpCarrier(
            'http://api.postaquilog.com.br:3100/tickets',
            $_SESSION['token']);
    }


    function updateOrder($observer){

        $order_id = $observer->getData('order_ids');
        $order = Mage::getModel('sales/order')->load($order_id);

        $customer_id = $order->getCustomerId();
        $customerData = Mage::getModel('customer/customer')->load($customer_id);

        $address = $order->getShippingAddress()->getData();
        $shipping = $order->getShippingAddress();

        $shipping_method = $order->getShippingMethod();
        $method = $this->getAtualDelivery($shipping_method, $_SESSION['methods_delivery_linebus']);

        $itens_volume =  array();
        foreach ($order->getAllItems() as $item) {
            $itens_volume[] = array(
                'peso'            => $item->getWeight()
            );
        }

        $total_produtos = $order->getGrandTotal() - $order->getShippingAmount();
        $post = $this->carrier->post(
                array("_id" => $method->_id,
                    "conteudo" => 'Postaqui - Magento plugin',
                    "peso_total" => $order->getWeight(),
                    "valor_total" => $total_produtos,
                    "tipo_envio" => $method->type_send,
                    "origem" => 'magento-postaqui',
                    "email" => $customerData->getEmail(),
                    "destinatario" => array(
                        "nome" => $address['firstname'].' '.$address['lastname'],
                        "cnpjCpf" => $customerData->getData('taxvat'),
                        "endereco" => $shipping->getStreet(2),
                        "numero" => $shipping->getStreet(3),
                        "complemento" => $shipping->getStreet(4),
                        "bairro" => $shipping->getStreet(1),
                        "cidade" => $address['city'],
                        "uf" => $shipping->getRegionCode(),
                        "cep" => $address['postcode'],
                        "celular" => $address['telephone'],
                    ),
                    "volume" => $itens_volume
                ));
        //echo '<pre>';
        //print_r($post);
        //die();
        unset($_SESSION['methods_delivery_linebus']); unset($_SESSION['token']);

        Mage::log($post);
    }

    function getAtualDelivery($shipping_method, $carriers){
        foreach($carriers as $carrier){
            if('linebus_postaqui_'.$carrier->type_send == $shipping_method) //$_code
                return $carrier;
        }
    }

}