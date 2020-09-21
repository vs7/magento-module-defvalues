<?php

class VS7_DefValues_Adminhtml_DefvaluesController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Attributes'))
            ->_title($this->__('Manage Attributes'));

        $this->loadLayout()
            ->_setActiveMenu('catalog/attributes')
            ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
            ->_addBreadcrumb(
                Mage::helper('catalog')->__('Manage Product Attributes'),
                Mage::helper('catalog')->__('Manage Product Attributes'));
        return $this;
    }

    public function indexAction()
    {
        $restrictData = Mage::getSingleton('adminhtml/session')->getRestrictData();
        if (!empty($restrictData['categories'])) {
            Mage::getSingleton('adminhtml/session')->addNotice('Categories in Filter: ' . implode(', ', $restrictData['categories']));
        }
        if (!empty($restrictData['products'])) {
            Mage::getSingleton('adminhtml/session')->addNotice('Products in Filter: ' . implode(', ', $restrictData['products']));
        }

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('vs7_defvalues/adminhtml_catalog_product_attribute'))
            ->renderLayout();
    }

    public function setdefaultAction()
    {
        $attributeIds = $this->getRequest()->getParam('attribute');

        if (!is_array($attributeIds)) {
            $this->_getSession()->addError($this->__('Please select attribute(s).'));
        } else {
            if (!empty($attributeIds)) {
                try {
                    $tables = array();
                    foreach ($attributeIds as $attributeId) {
                        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeId);
                        if ($attribute) {
                            $tableName = Mage::getSingleton('core/resource')->getTableName('catalog/product') . '_' . $attribute->getBackendType();
                            $tables[$tableName][] = $attribute->getId();
                        }
                    }

                    $stores = array();
                    foreach (Mage::app()->getStores() as $store) {
                        $stores[] = $store->getId();
                    }
                    $storesAsString = implode(',', $stores);

                    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                    $restrictData = Mage::getSingleton('adminhtml/session')->getRestrictData();
                    if (!empty($restrictData['products'])) {
                        $restrict = ' AND entity_id NOT IN (' . implode(', ', $restrictData['products']) . ')';
                    } else {
                        $restrict = '';
                    }

                    foreach ($tables as $tableName => $attributeIds) {
                        $attributeIdsAsString = implode(',', $attributeIds);
                        $q = "DELETE FROM {$tableName} WHERE attribute_id IN ({$attributeIdsAsString}) AND store_id IN ({$storesAsString})" . $restrict . ";";
                        $connection->query($q);
                    }

                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d attribute(s) have been set to default.', count($attributeIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    public function restrictAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('vs7_defvalues/adminhtml_catalog_product_restrict'))
            ->renderLayout();
    }

    public function saveAction()
    {
        $catIds = $this->getRequest()->getParam('cat_ids');
        if (!empty($catIds)) {
            if (preg_match('/^(\d+\,)+\d+$/', $catIds)) {
                $delimeter = ',';
            } elseif (preg_match('/^(\d+\s)+\d+$/', $catIds)) {
                $delimeter = ' ';
            } elseif(preg_match('/^\d+$/', $catIds)) {
                $catIds = array((int)$catIds);
            } else {
                throw new Exception('Incorrect Id');
            }
            if (isset($delimeter)) {
                $catIds = explode($delimeter, $catIds);
            }

            $restrictedProducts = array();

            foreach ($catIds as $categoryId) {
                $products = Mage::getSingleton('catalog/category')->load($categoryId)
                    ->getProductCollection()
                    ->addAttributeToSelect('*');
                foreach ($products as $product) {
                    $restrictedProducts[] = $product->getId();
                }
            }

            $data = array(
                'categories' => $catIds,
                'products' => $restrictedProducts
            );

            Mage::getSingleton('adminhtml/session')->setRestrictData($data);
        } else {
            Mage::getSingleton('adminhtml/session')->setRestrictData(false);
        }

        Mage::getSingleton('adminhtml/session')->addNotice('Successfully set filter!');
        $this->_redirect('*/*/index');
    }
}