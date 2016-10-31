<?php
class Mage_Edm_Block_Adminhtml_Community_Article_Layer extends Mage_Core_Block_Template
{
	protected $_categoryCollection;
	protected $_wxCollection;

	public function getCategoryCollection(){
		if (!$this->_categoryCollection) {
			$collection = Mage::getResourceModel('edm/article_category_collection');
			$collection->addFieldToFilter('status',1)
				->setOrder('position','desc');
			$this->_categoryCollection = $collection;
		}
		return $this->_categoryCollection;
	}
	
	public function getWxCollection(){
		if (!$this->_wxCollection) {
			$collection = Mage::getResourceModel('edm/article_wx_collection');
			$collection->addFieldToFilter('status',1)
				->addFieldToFilter('is_hot',1)
				->setOrder('position','desc')
				->setPageSize('6');
			$this->_wxCollection = $collection;
		}
		return $this->_wxCollection;
	}
}
