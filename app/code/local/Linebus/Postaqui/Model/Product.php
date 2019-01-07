<?php
/**
 * Created by PhpStorm.
 * User: Junior Maia
 * Date: 04/01/19
 * Time: 11:44
 */

class Linebus_Postaqui_Model_Product extends Mage_Core_Model_Abstract {

    public function getComprimento($_product) {

        $postaqui_comprimento = 0;

        if ($_product->getData('postaqui_comprimento') != '' || (int) $_product->getData('postaqui_comprimento') != 0) {

            $postaqui_comprimento = $_product->getData('postaqui_comprimento');

        } else {

            $postaqui_comprimento = Mage::getStoreConfig('carriers/linebus_postaqui/postaqui_comprimento_default');

        }

        return $postaqui_comprimento;
    }

    public function getAltura($_product) {

        $postaqui_altura = 0;

        if ($_product->getData('postaqui_altura') != '' || (int) $_product->getData('postaqui_altura') != 0) {

            $postaqui_altura = $_product->getData('postaqui_altura');

        } else {

            $postaqui_altura = Mage::getStoreConfig('carriers/linebus_postaqui/postaqui_altura_default');

        }

        return $postaqui_altura;
    }

    public function getLargura($_product) {

        $postaqui_largura = 0;

        if ($_product->getData('postaqui_largura') != '' || (int) $_product->getData('postaqui_largura') != 0) {

            $postaqui_largura = $_product->getData('postaqui_largura');

        } else {

            $postaqui_largura = Mage::getStoreConfig('carriers/linebus_postaqui/postaqui_largura_default');

        }

        return $postaqui_largura;
    }
}