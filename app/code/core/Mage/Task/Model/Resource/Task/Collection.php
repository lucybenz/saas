<?php
class Mage_Task_Model_Resource_Task_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('task/task');
    }
    public function getAsOptionArray()
    {
        $arr = array();
        foreach ($this as $item) {
            $id = $item->getData('task_id');
			$data = array();
            $data['value'] = $id;
            $data['label'] = $item->getData('task_name');
            $res[$id] = $data;
        }

        return $res;
    }
}