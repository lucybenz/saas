<?php
/**
 * Config category field backend
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Mio Core Team <developer@ioe9.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Category extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        if ($this->getScope() == 'stores') {
            $rootId     = $this->getValue();
            $storeId    = $this->getScopeId();

            $category   = Mage::getSingleton('catalog/category');
            $tree       = $category->getTreeModel();

            // Create copy of categories attributes for choosed store
            $tree->load();
            $root = $tree->getNodeById($rootId);

            // Save root
            $category->setStoreId(0)
               ->load($root->getId());
            $category->setStoreId($storeId)
                ->save();

            foreach ($root->getAllChildNodes() as $node) {
                $category->setStoreId(0)
                   ->load($node->getId());
                $category->setStoreId($storeId)
                    ->save();
            }
        }
        return $this;
    }
}
