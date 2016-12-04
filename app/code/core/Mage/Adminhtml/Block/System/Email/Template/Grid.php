<?php
/**
 * Adminhtml  system templates grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Mio Core Team <developer@ioe9.com>
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        $this->setEmptyText(Mage::helper('adminhtml')->__('No Templates Found'));
        $this->setId('systemEmailTemplateGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('core/email_template_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('template_id',
            array(
                  'header'=>Mage::helper('adminhtml')->__('ID'),
                  'index'=>'template_id'
            )
        );

        $this->addColumn('code',
            array(
                'header'=>Mage::helper('adminhtml')->__('Template Name'),
                'index'=>'template_code'
        ));

        $this->addColumn('added_at',
            array(
                'header'=>Mage::helper('adminhtml')->__('Date Added'),
                'index'=>'added_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('modified_at',
            array(
                'header'=>Mage::helper('adminhtml')->__('Date Updated'),
                'index'=>'modified_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('subject',
            array(
                'header'=>Mage::helper('adminhtml')->__('Subject'),
                'index'=>'template_subject'
        ));
        /*
        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('adminhtml')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/system_email_template_grid_renderer_sender'
        ));
        */
        $this->addColumn('type',
            array(
                'header'=>Mage::helper('adminhtml')->__('Template Type'),
                'index'=>'template_type',
                'filter' => 'adminhtml/system_email_template_grid_filter_type',
                'renderer' => 'adminhtml/system_email_template_grid_renderer_type'
        ));

        $this->addColumn('action',
            array(
                'header'	=> Mage::helper('adminhtml')->__('Action'),
                'index'		=> 'template_id',
                'sortable'  => false,
                'filter' 	=> false,
                'width'		=> '100px',
                'renderer'  => 'adminhtml/system_email_template_grid_renderer_action'
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

