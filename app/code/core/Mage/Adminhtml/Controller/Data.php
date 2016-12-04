<?php
/***
 * 
 */
class Mage_Adminhtml_Controller_Data extends Mage_Adminhtml_Controller_Action
{
	public function preDispatch() {
		$app = new Varien_Object();
        $app->setName("大数据");
        $app->setCode("data");
        Mage::register("current_app",$app,true);
        parent::preDispatch();
        return $this;
	}
		
    protected function _setActiveMenu($menuPath)
    {
    	if (strpos($menuPath,"data")!==0) {
    		$menuPath = "data/".$menuPath;
    	}
    	$this->getLayout()->getBlock("navigation")->setActive($menuPath);
        $this->getLayout()->getBlock("menu")->setActive($menuPath);
        return $this;
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton("admin/session")->isAllowed("data");
    }
}

