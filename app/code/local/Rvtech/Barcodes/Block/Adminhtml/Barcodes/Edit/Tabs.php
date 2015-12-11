 <?php
  class Rvtech_Barcodes_Block_Adminhtml_Barcodes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();
          $this->setId('barcodes_tabs');
          $this->setDestElementId('edit_form');
          $this->setTitle('Serial Information');
      }
      protected function _beforeToHtml()
      {
          $this->addTab('form_section', array(
                   'label' => 'Serial Information',
                   'title' => 'Serial Information',
                   'content' => $this->getLayout()
     ->createBlock('barcodes/adminhtml_barcodes_edit_tab_form')
     ->toHtml()
         ));
         return parent::_beforeToHtml();
    }
}