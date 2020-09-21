<?php

class VS7_DefValues_Block_Adminhtml_Catalog_Product_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->removeButton('add');
        $this->_addButton('restrict', array(
            'label'     => 'Restrict',
            'onclick'   => 'setLocation(\'' . $this->getRestrictUrl() .'\')',
            'class'     => 'add',
        ));
        $this->_controller = 'adminhtml_catalog_product_attribute';
        $this->_blockGroup = 'vs7_defvalues';
        $this->_headerText = Mage::helper('catalog')->__('Manage Attributes Defaults');
    }

    public function getRestrictUrl()
    {
        return $this->getUrl('*/*/restrict');
    }
}