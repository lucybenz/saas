<?php
/***
 * 
 */
class Mage_Adminhtml_Controller_News extends Mage_Adminhtml_Controller_Action
{
	public function preDispatch() {
		$app = new Varien_Object();
        $app->setName("外贸头条");
        $app->setCode("news");
        Mage::register("current_app",$app,true);
        parent::preDispatch();
        return $this;
	}
		
    protected function _setActiveMenu($menuPath)
    {
    	if (strpos($menuPath,"news")!==0) {
    		$menuPath = "news/".$menuPath;
    	}
    	$this->getLayout()->getBlock("navigation")->setActive($menuPath);
        $this->getLayout()->getBlock("menu")->setActive($menuPath);
        return $this;
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton("admin/session")->isAllowed("news");
    }
}

