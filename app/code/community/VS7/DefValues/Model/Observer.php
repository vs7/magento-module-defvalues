<?php

class VS7_DefValues_Model_Observer
{
    public function addDefValuesColumn($observer)
    {
        $block = $observer->getEvent()->getBlock();

        if(
            $block instanceof Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid
            && $block->getRequest()->getControllerName() == 'catalog_product_attribute')
        {
            $header = 'Not Default:';
            foreach (Mage::app()->getStores() as $store)
            {
                $header .= ' ' . $store->getName();
            }

            $block->addColumnAfter('defvalues', array(
                'header' => $header,
                'sortable' => true,
                'index' => 'defvalues',
                'type' => 'options',
                'renderer' => 'vs7_defvalues/adminhtml_renderer_defaults',
                'align' => 'center',
            ), 'is_comparable');
        }
    }
}