<?php
/**
 * Adminhtml grid item renderer currency
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Mio Core Team <developer@ioe9.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 100;
    /**
     * Currency objects cache
     */
    protected static $_currencies = array();

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $currency_code = $this->_getCurrencyCode($row);

            if (!$currency_code) {
                return $data;
            }

            $data = floatval($data) * $this->_getRate($row);
            $data = sprintf("%F", $data);
            $data = Mage::app()->getLocale()->currency($currency_code)->toCurrency($data);
            return $data;
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code for the row, false on error
     *
     * @param Varien_Object $row
     * @return string|bool
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }
        return false;
    }

    /**
     * Returns rate for the row, 1 by default
     *
     * @param Varien_Object $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        if ($rate = $this->getColumn()->getRate()) {
            return floatval($rate);
        }
        if ($rate = $row->getData($this->getColumn()->getRateField())) {
            return floatval($rate);
        }
        return 1;
    }

    /**
     * Renders CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }
}
