<?php
class Mage_Edm_Model_Resource_Client_Email_Attr_Value extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('edm/client_email_attr_value', 'value_id');
    }
}
