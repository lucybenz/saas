<?php
class Mage_Edm_Model_Resource_Template_Module_Item_Relate extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('edm/template_module_item_relate', 'relate_id');
    }
}
