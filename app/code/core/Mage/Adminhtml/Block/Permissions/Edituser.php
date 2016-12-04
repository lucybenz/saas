<?php


class Mage_Adminhtml_Block_Permissions_Edituser extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('user_edit_form');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('account', array(
            'label'     => Mage::helper('adminhtml')->__('User Info'),
            'title'     => Mage::helper('adminhtml')->__('User Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_useredit')->toHtml(),
            'active'    => true
        ));
        if( $this->getUser()->getUserId() ) {
            $this->addTab('roles', array(
                'label'     => Mage::helper('adminhtml')->__('Roles'),
                'title'     => Mage::helper('adminhtml')->__('Roles'),
                'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_userroles')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
}
