<?php

class VS7_DefValues_Block_Adminhtml_Renderer_Defaults extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private $_attribute;
    private $_options;
    private $_firstRun = true;
    private $_resource;
    private $_connection;

    public function render(Varien_Object $row)
    {
        if ($row->getIsGlobal() == 1) {
            return '-';
        } else {
            if ($this->_resource == null) {
                $this->_resource = Mage::getSingleton('core/resource');
            }

            if ($this->_connection == null) {
                $this->_connection = $this->_resource->getConnection('core_read');
            }

            $tableName = $this->_resource->getTableName('catalog/product') . '_' . $row->getBackendType();
            $attributeId = $row->getId();

            $qty = array();

            $columnId = $this->getColumn()->getId();
            $storeId = null;
            if (preg_match('/store(\d+)/', $columnId, $matches)) {
                $storeId = (int)$matches[1];
            }

            $restrictData = Mage::getSingleton('adminhtml/session')->getRestrictData();
            if (!empty($restrictData['products'])) {
                $restrict = ' AND entity_id NOT IN (' . implode(', ', $restrictData['products']) . ')';
            } else {
                $restrict = '';
            }

            if (!empty($storeId)) {
                $q = "SELECT COUNT(*) FROM {$tableName} WHERE attribute_id = {$attributeId} AND store_id = {$storeId}" . $restrict . ";";
                $qty[$storeId] = (int)$this->_connection->fetchOne($q);
            } else {
                foreach (Mage::app()->getStores() as $storeId => $val)
                {
                    $q = "SELECT COUNT(*) FROM {$tableName} WHERE attribute_id = {$attributeId} AND store_id = {$storeId}" . $restrict . ";";
                    $qty[$storeId] = (int)$this->_connection->fetchOne($q);
                }
            }

            return implode(', ', $qty);
        }
    }
}