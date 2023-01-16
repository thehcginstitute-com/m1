<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Customfields_Grid extends Mage_Adminhtml_Block_Widget_Grid
 {
    /**
    *Constructor. set default sort order for grid
    *
    **/
	public function __construct() {
        parent::__construct();
        $this->setId("customfieldsGrid");
        $this->setDefaultSort("index_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
    * Set collection and joins customer attribute table with customfiled table.
    *@return Collection 
    *
    **/
    protected function _prepareCollection() {
        $prefix = Mage::getConfig()->getTablePrefix();        
        $collection = Mage::getModel("customfield/customfields")->getCollection();
        $collection->getSelect()
            ->joinLeft(array('t2' => $prefix."eav_attribute"),'main_table.customer_attribute_id = t2.attribute_id');
        $collection->addFieldToFilter('dependent_on',array("null" => true));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
    * set columns of the Grid.
    *
    **/
    protected function _prepareColumns() {
        $this->addColumn("index_id", array(
            "header"    => $this->__("ID"),
            "align"     => "center",
            "width"     => "30px",
            "index"     => "index_id"
        ));

        $this->addColumn("frontend_label", array(
            "header"    => $this->__("Label"),
            "index"     => "frontend_label"
        ));

        $this->addColumn("inputname", array(
            "header"    => $this->__("Name"),
            "index"     => "inputname"
        ));

        $this->addColumn("frontend_input", array(
            "header"    => $this->__("Type"),
            "index"     => "frontend_input",
            "type"      => "options",
            'renderer'    => 'Webkul_CustomField_Block_Adminhtml_Customfields_Grid_Renderer',
            "options"   => array("" => $this->__("Please Select"),
                                "text" => $this->__("Text"),
                                "textarea" => $this->__("Text Area"),
                                "date" => $this->__("Date Of Birth"),
                                "select" => $this->__("Dropdown"),
                                "multiselect" => $this->__("Multiple Select"),
                                "radio" => $this->__("Radio Button"),
                                "image" => $this->__("Media Image"),
                                "file" => $this->__("File"),
                                "dependable" => $this->__("Dependable Field")
                            )
        ));


        $this->addColumn("sort_order", array(
            "header"    => $this->__("Set Order"),
            "index"     => "sort_order"
        ));

        $this->addColumn("is_in_saif", array(
            "header"    => $this->__("Visible in Sales Account Information"),
            "align"     => "left",
            "width"     => "80px",
            "index"     => "is_in_saif",
            "type"      => "options",
            "options"   => array(0 => "No",1 => "Yes")
        ));

        $this->addColumn("is_in_semail", array(
            "header"    => $this->__("Visible in Sales Email"),
            "align"     => "left",
            "width"     => "80px",
            "index"     => "is_in_semail",
            "type"      => "options",
            "options"   => array(0 => "No",1 => "Yes")
        ));

        $this->addColumn("status", array(
            "header"    => $this->__("Status"),
            "align"     => "left",
            "width"     => "80px",
            "index"     => "status",
            "type"      => "options",
            "options"   => array(1 => "Enabled",2 => "Disabled")
        ));

        $this->addColumn("action", array(
            "header"    => $this->__("Action"),
            "width"     => "80",
            "type"      => "action",
            "getter"    => "getId",
            "actions"   => array(array(
            "caption"   => $this->__("Edit"),
            "url"       => array("base" => "*/*/edit"),
            "field"     => "id")),
            "filter"    => false,
            "sortable"  => false,
            "index"     => "stores",
            "is_system" => true
        ));

        return parent::_prepareColumns();
    }
    /**
    * Set massactions for customer registration fields
    * 
    **/
    protected function _prepareMassaction() {
        $this->setMassactionIdField("index_id");
        $this->getMassactionBlock()->setFormFieldName("ids");
        $this->getMassactionBlock()->addItem("delete", array(
            "label"     => $this->__("Delete"),
            "url"       => $this->getUrl("*/*/massDelete"),
            "confirm"   => $this->__("Are you sure?")
        ));
        $statuses = Mage::getSingleton("customfield/status")->getOptionArray();
        array_unshift($statuses, array("label" => "", "value" => ""));
        $this->getMassactionBlock()->addItem("status", array(
            "label"     => $this->__("Change status"),
            "url"       => $this->getUrl("*/*/massStatus", array("_current" => true)),
            "additional"=> array("visibility" => array(
                                                    "name"  => "status",
                                                    "type"  => "select",
                                                    "class" => "required-entry",
                                                    "label" => $this->__("Status"),
                                                    "values"=> $statuses))
        ));
        return $this;
    }
    /**
    * Set Grid Url
    * 
    **/
    public function getGridUrl(){
        return $this->getUrl("*/*/grid",array("_current"=>true));
    }
    /**
    * Set Row Url of the Grid
    * 
    **/
    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getIndexId()));
    }

}