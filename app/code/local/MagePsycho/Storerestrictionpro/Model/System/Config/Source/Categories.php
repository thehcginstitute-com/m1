<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_System_Config_Source_Categories
{

    private $options    = array();
    private $categories = array();

    public function getAllCategories($catId)
    {
        $childCategories = Mage::getModel('catalog/category')
                               ->getCollection()
                               ->addAttributeToSelect('*')
            //->setStoreId(Mage::app()->getStore()->getId())
            //->addAttributeToFilter('is_active','1')
            //->addAttributeToFilter('include_in_menu','1')
                               ->addAttributeToFilter('parent_id', array('eq' => $catId))
                               ->addAttributeToSort('position')
        ;

        foreach ($childCategories as $_category) {
            $this->categories[] = $_category->getId();
            $this->getAllCategories($_category->getId());
        }

        return $this->categories;
    }

    public function toOptionArray()
    {
        $storeId        = 1;
        $storeGroupId   = Mage::getModel('core/store')->load($storeId)->getGroupId();
        $rootCategoryId = Mage::getModel('core/store_group')->load($storeGroupId)->getRootCategoryId();
        $categoryIds    = $this->getAllCategories($rootCategoryId);

        $this->categories   = array();
        $this->options      = array();

        foreach ($categoryIds as $catId) {
            $category       = Mage::getModel('catalog/category')->load($catId);
            $indentation    = '';

            if ($category->getLevel() > 2) {
                $indentation .= str_repeat("... ", $category->getLevel() - 2);
            }

            $this->options[] = array(
                'value' => $catId,
                'label' => $indentation . $category->getName()
            );
        }

        return $this->options;
    }
}