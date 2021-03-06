<?php
/**
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Post additional info block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Mio Core Team <developer@ioe9.com>
 */
class Mage_Catalog_Block_Post_View_Additional extends Mage_Core_Block_Template
{

    protected $_list;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/post/view/additional.phtml');
    }

    public function getChildHtmlList()
    {
        if (is_null($this->_list)) {
            $this->_list = array();
            foreach ($this->getSortedChildren() as $name) {
                $block = $this->getLayout()->getBlock($name);
                if (!$block) {
                    Mage::exception(Mage::helper('catalog')->__('Invalid block: %s.', $name));
                }
                $this->_list[] = $block->toHtml();
            }
        }
        return $this->_list;
    }

}
