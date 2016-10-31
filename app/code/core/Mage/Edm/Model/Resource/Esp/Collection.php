<?php
class Mage_Edm_Model_Resource_Esp_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('edm/esp');
    }
    public function getAsOptionArray()
    {
        $arr = array();
        foreach ($this as $item) {
            $id = $item->getData('esp_id');
			$data = array();
            $data['value'] = $id;
            $data['label'] = $item->getData('esp_name');
            $res[] = $data;
        }

        return $res;
    }
}
