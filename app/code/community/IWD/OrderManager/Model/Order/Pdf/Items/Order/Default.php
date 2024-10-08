<?php

class IWD_OrderManager_Model_Order_Pdf_Items_Order_Default extends IWD_OrderManager_Model_Order_Pdf_Items_Abstract
{
    function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $shift = array(0, 10, 0);

        $this->_setFontRegular();

        $page->drawText($item->getQtyOrdered() * 1, 435, $pdf->y, 'UTF-8');

        /* in case Product name is longer than 80 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {
            $page->drawText($part, 35, $pdf->y - $shift[0], 'UTF-8');
            $shift[0] += 10;
        }

        $options = $this->getItemOptions();
        if (isset($options)) {
            foreach ($options as $option) {
                // draw options label
                $this->_setFontItalic();
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {
                    $page->drawText($_option, 35, $pdf->y - $shift[0], 'UTF-8');
                    $shift[0] += 10;
                }
                // draw options value
                $this->_setFontRegular();
                if ($option['value']) {
                    $values = explode(', ', strip_tags($option['value']));
                    foreach ($values as $value) {
                        foreach (Mage::helper('core/string')->str_split($value, 60, true, true) as $_value) {
                            $page->drawText($_value, 40, $pdf->y - $shift[0], 'UTF-8');
                            $shift[0] += 10;
                        }
                    }
                }
            }
        }

        foreach ($this->_parseDescription() as $description) {
            $page->drawText(strip_tags($description), 65, $pdf->y - $shift[1], 'UTF-8');
            $shift[1] += 10;
        }

        /* in case Product SKU is longer than 36 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $key => $part) {
            if ($key > 0) {
                $shift[2] += 10;
            }
            $page->drawText($part, 240, $pdf->y - $shift[2], 'UTF-8');
        }

        $font = $this->_setFontBold();

        $row_total = $order->formatPriceTxt($item->getRowTotal());
        $page->drawText($row_total, 565 - $pdf->widthForStringUsingFontSize($row_total, $font, 7), $pdf->y, 'UTF-8');

        $price = $order->formatPriceTxt($item->getPrice());
        $page->drawText($price, 395 - $pdf->widthForStringUsingFontSize($price, $font, 7), $pdf->y, 'UTF-8');

        $tax = $order->formatPriceTxt($item->getTaxAmount());
        $page->drawText($tax, 495 - $pdf->widthForStringUsingFontSize($tax, $font, 7), $pdf->y, 'UTF-8');

        $pdf->y -= max($shift) + 10;
    }
}
