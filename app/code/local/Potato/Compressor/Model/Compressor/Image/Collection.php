<?php

class Potato_Compressor_Model_Compressor_Image_Collection extends Varien_Object implements Iterator
{
    private $__items = array();
    protected $_lastPageNumber = 1;
    protected $_ids = array();
    protected $_total = 0;
    protected $_isLoaded = false;

    public function __construct($array)
    {
        if (is_array($array)) {
            $this->__items = $array;
            $this->_total = count($this->__items);
        }
    }

    public function rewind()
    {
        reset($this->__items);
    }

    public function current()
    {
        $current = current($this->__items);
        $current = new Varien_Object(
            array(
                'id'  =>Mage::helper('po_compressor')->encode($current),
                'path'=> $current
            )
        );
        return $current;
    }

    public function key()
    {
        $var = key($this->__items);
        return $var;
    }

    public function next()
    {
        $next = next($this->__items);
        $next = new Varien_Object(
            array(
                'id'   => Mage::helper('po_compressor')->encode($next),
                'path' => $next
            )
        );
        return $next;
    }

    public function getAllIds()
    {
        return $this->_ids;
    }

    public function valid()
    {
        $key = key($this->__items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

    public function getLastPageNumber()
    {
        return $this->_lastPageNumber;
    }

    public function load()
    {
        $this->_lastPageNumber = max(ceil($this->getSize() / $this->_getLimit()), 1);
        $this->_ids = array_map("base64_encode", $this->__items);
        $this->__items = array_slice( $this->__items, $this->_getCurrentOffset() , $this->_getLimit() );
        $this->_isLoaded = true;
        return $this;
    }

    public function isLoaded()
    {
        return $this->_isLoaded;
    }

    public function getSize()
    {
        return $this->_total;
    }

    public function addPathFilter($value)
    {
        $_items = array();
        foreach ($this->__items as $item) {
            if (strpos($item, $value) !== false ) {
                array_push($_items, $item);
            }
        }
        $this->__items = $_items;
        $this->_total = count($_items);
        return $this;
    }

    protected function _getCurrentOffset()
    {
        return max((int) $this->getCurPage() - 1, 0) * $this->_getLimit();
    }

    protected function _getLimit()
    {
        return (int) $this->getPageSize();
    }

    public function count()
    {
        return $this->getSize();
    }

    public function getFirstItem()
    {
        $data = array();
        if (count($this->__items) > 0) {
            $data = $this->__items[0];
        }
        return new Varien_Object($data);
    }
}