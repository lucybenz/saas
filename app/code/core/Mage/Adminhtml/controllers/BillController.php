<?php
/***
 * Bill 首页
 */
class Mage_Adminhtml_BillController extends Mage_Adminhtml_Controller_Bill
{
    public function indexAction()
    {
        $this->_title($this->__('费用管理'));
        $this->loadLayout();
        $this->_setActiveMenu('bill');
        
        $this->renderLayout();
    }
	
    public function ajaxBlockAction()
    {
        $output   = '';
        $blockTab = $this->getRequest()->getParam('block');
        if (in_array($blockTab, array('tab_orders', 'tab_amounts', 'totals'))) {
            $output = $this->getLayout()->createBlock('adminhtml/dashboard_' . $blockTab)->toHtml();
        }
        $this->getResponse()->setBody($output);
        return;
    }
    

}