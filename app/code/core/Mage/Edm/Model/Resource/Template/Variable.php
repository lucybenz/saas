<?php
class Mage_Edm_Model_Resource_Template_Variable extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('edm/template_variable', 'variable_id');
    }
}
