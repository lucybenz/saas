<?php
class Mage_Attendance_Block_Adminhtml_Fieldwork extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_fieldwork';
    	$this->_blockGroup = 'attendance';
    	$this->_headerText = "我的待办";
        parent::__construct();
        
        $this->_updateButton('add', 'label', "+ 新建申请");
    }
}
