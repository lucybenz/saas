<?php



class Mage_Adminhtml_Model_System_Config_Backend_Sitemap extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave()
    {
        $value     = $this->getValue();
            if ($value < 0 || $value > 1) {
                throw new Exception(Mage::helper('sitemap')->__('The priority must be between 0 and 1.'));
            } elseif (($value == 0) && !($value === '0' || $value === '0.0')) {
                throw new Exception(Mage::helper('sitemap')->__('The priority must be between 0 and 1.'));
            }
        return $this;
    }

}
