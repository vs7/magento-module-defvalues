<?php

class VS7_DefValues_Block_Adminhtml_Catalog_Product_Restrict extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_blockGroup = 'vs7_defvalues';
        $this->_controller = 'adminhtml';
        $this->_mode = 'catalog_product_restrict';
        $this->_headerText = Mage::helper('vs7_defvalues')->__('Set Categories to Restrict');
    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/' . $this->_controller . '/save');
    }
}