<?php

class VS7_DefValues_Block_Adminhtml_Catalog_Product_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare product attributes grid collection object
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
//            ->addVisibleFilter()
            ->addFieldToFilter('additional_table.is_global', array('neq' => 1));;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', array(
            'header'=>Mage::helper('catalog')->__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        foreach (Mage::app()->getStores() as $store)
        {
            $this->addColumn('store' . $store->getId(), array(
                'header' => $store->getName(),
                'sortable' => false,
                'index' => 'defvalues' . $store->getId(),
                'renderer' => 'vs7_defvalues/adminhtml_renderer_defaults',
                'align' => 'center',
            ));
        }

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('attribute_code');
        $this->getMassactionBlock()->setFormFieldName('attribute');

        $this->getMassactionBlock()->addItem('setdefault', array(
            'label'=> Mage::helper('catalog')->__('Set Default'),
            'url'  => $this->getUrl('*/*/setdefault'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        Mage::dispatchEvent('vs7_defvalues_adminhtml_catalog_product_attribute_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
}
