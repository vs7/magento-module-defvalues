<?php

class VS7_DefValues_Block_Adminhtml_Catalog_Product_Restrict_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'filter_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $helper = Mage::helper('vs7_defvalues');
        $fieldset = $form->addFieldset('display', array(
            'legend' => $helper->__('Enter IDs'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('cat_ids', 'text', array(
            'name' => 'cat_ids',
            'label' => $helper->__('Categories IDs'),
        ));

        $data = Mage::getSingleton('adminhtml/session')->getRestrictData();

        if (!empty($data)) {
            $form->setValues(array(
                    'cat_ids' => implode(', ', $data['categories'])
                )
            );
        }

        $fieldset->addField('submit', 'submit', array(
            'name' => 'submit',
            'label' => $helper->__('Set Filter'),
        ))->setValue('Set Filter');

        return parent::_prepareForm();
    }
}