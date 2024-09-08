<?php
class Aftership_Track_Model_Methods
{
    function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('aftership')->__('Email')),            
            array('value'=>2, 'label'=>Mage::helper('aftership')->__('SMS')),                       
        );
    }

}
?>