<?php
/**
 * Created by PhpStorm.
 * User: Junior Maia
 * Date: 04/01/19
 * Time: 09:36
 */

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$codigo = 'postaqui_comprimento';
$config = array(
    'group'    => 'Postaqui',
    'position' => 1,
    'required' => 0,
    'label'    => 'Comprimento (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Comprimento da embalagem do produto'
);

$setup->addAttribute('catalog_product', $codigo, $config);

$codigo = 'postaqui_altura';
$config = array(
    'group'    => 'Postaqui',
    'position' => 1,
    'required' => 0,
    'label'    => 'Altura (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Altura da embalagem do produto'
);

$setup->addAttribute('catalog_product', $codigo, $config);

$codigo = 'postaqui_largura';
$config = array(
    'group'    => 'Postaqui',
    'position' => 1,
    'required' => 0,
    'label'    => 'Largura (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Largura da embalagem do produto'
);

$setup->addAttribute('catalog_product', $codigo, $config);

$installer->endSetup();