<?php
$this->addAttribute('customer', 'cpf', array(
    'type'      => 'varchar',
    'label'     => 'CPF',
    'input'     => 'text',
    'position'  => 120,
    'required'  => true,//or true
    'is_system' => 0,
));
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cpf');
$attribute->setData('used_in_forms', array(
    'adminhtml_customer',
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
));
$attribute->setData('is_user_defined', 0);
$attribute->save();