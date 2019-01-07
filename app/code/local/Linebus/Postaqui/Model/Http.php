<?php
/**
 * Created by PhpStorm.
 * User: Junior Maia
 * Date: 07/01/19
 * Time: 10:57
 */

class Linebus_Postaqui_Model_Http extends Mage_Core_Model_Abstract {

    private $url;
    private $auth;

    public $express;
    public $conventional;
    public $others, $data = array();

    public function __construct(){
        $this->auth = Mage::getStoreConfig('carriers/linebus_postaqui/auth');
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function post($data){

        Mage::log('------- Send Resquest Data --------');
        Mage::log(json_encode($data));

        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: '.($this->auth))
        );

        $response = json_decode(curl_exec($curl));

        Mage::log('------- Response --------');
        Mage::log(json_encode($response));
//        if (curl_exec($curl) === false){
//            $response = curl_error($curl);
//            $info = curl_getinfo($curl);
//            echo '<pre>'; print_r($response); print_r($info); die();
//        }

        curl_close($curl);

        return $response;
    }

    public function stripCarrierServices($dataApi){
        if(count($dataApi)){
            foreach($dataApi as $service){
                $this->data[] = $service;
            }
        }
    }

}