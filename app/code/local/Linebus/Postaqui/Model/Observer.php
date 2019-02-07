<?php
//session_start();
//require 'HttpCarrier.php';

class Linebus_Postaqui_Model_Observer extends Varien_Event_Observer
{

    protected $_code = 'linebus_postaqui';
    protected $carrier;


    public function __construct()
    {

        $_url = Mage::getModel('linebus_postaqui/url')->getUrlTicket();

        /*
        $this->carrier = new HttpCarrier(
            $_url,
            $_SESSION['token']);
        */

        $modelHttp = Mage::getModel('linebus_postaqui/http');
        $modelHttp->setUrl($_url);

        $this->carrier = $modelHttp;
    }


    function updateOrder($observer)
    {


        $order_id = $observer->getData('order_ids');
        $order = Mage::getModel('sales/order')->load($order_id);

        $customer_id = $order->getCustomerId();
        $customerData = Mage::getModel('customer/customer')->load($customer_id);

        $address = $order->getShippingAddress()->getData();
        $shipping = $order->getShippingAddress();

        $shipping_method = $order->getShippingMethod();
        $method = $this->getAtualDelivery($shipping_method, $_SESSION['methods_delivery_linebus']);

        $itens_volume = array();
        $order_name = 'Postaqui - Magento plugin';


        foreach ($order->getAllVisibleItems() as $item) {

            if (!isset($first_item))
                $order_name = $item->getName();

            $qty = (int)$item->getQtyOrdered();

            //carrega o produto simples
            $_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());

            //pega dimensões do produto no cadastro individual ou no default do módulo
            $postaqui_comprimento = Mage::getModel('linebus_postaqui/product')->getComprimento($_product) * $qty;
            $postaqui_altura = Mage::getModel('linebus_postaqui/product')->getAltura($_product) * $qty;
            $postaqui_largura = Mage::getModel('linebus_postaqui/product')->getLargura($_product) * $qty;

            $itens_volume[] = array(
                'comprimento' => $postaqui_comprimento,
                'altura' => $postaqui_altura,
                'largura' => $postaqui_largura,
                'peso' => $item->getWeight() * $qty
            );

            $first_item = false;
        }

        if (!isset($_SESSION['last_order_exec']) || $_SESSION['last_order_exec'] != $order_id) {


            $total_produtos = $order->getGrandTotal() - $order->getShippingAmount();
            $post = $this->carrier->post(
                array("_id" => $method->_id,
                    "conteudo" => $order_name,
                    "peso_total" => (float)$order->getWeight(),
                    "valor_total" => (float)$total_produtos,
                    "tipo_envio" => $method->type_send,
                    "origem" => 'magento-postaqui',
                    "email" => $customerData->getEmail(),
                    "destinatario" => array(
                        "nome" => $address['firstname'] . ' ' . $address['lastname'],
                        "cnpjCpf" => $customerData->getData('taxvat'),
                        "endereco" => $shipping->getStreet(1),
                        "numero" => $shipping->getStreet(2),
                        "complemento" => $shipping->getStreet(3),
                        "bairro" => $shipping->getStreet(4),
                        "cidade" => $address['city'],
                        "uf" => $shipping->getRegionCode(),
                        "cep" => $address['postcode'],
                        "celular" => $address['telephone'],
                    ),
                    "volume" => $itens_volume
                ));
        }


//        echo '<pre>';
//        print_r($itens_volume);
//        die();

        unset($_SESSION['methods_delivery_linebus']);
        unset($_SESSION['token']);

        $_SESSION['last_order_exec'] = $order_id; // Flag de controle para executar este fluxo uma vez, mesmo o hook se repetindo
    }

    function getAtualDelivery($shipping_method, $carriers)
    {
        foreach ($carriers as $carrier) {
            if ('linebus_postaqui_' . $carrier->type_send == $shipping_method) //$_code
                return $carrier;
        }
    }

}