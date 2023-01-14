<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class IWD_OnepageCheckoutSignature_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
   
	/**
	 * Draw header for item table
	 *
	 * @param Zend_Pdf_Page $page
	 * @return void
	 */
	protected function _drawHeader(Zend_Pdf_Page $page)//added for compatibility with Magento 1.7-1.8
	{
		/* Add table head */
		$this->_setFontRegular($page, 10);
		$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setLineWidth(0.5);
		$page->drawRectangle(25, $this->y, 570, $this->y -15);
		$this->y -= 10;
		$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
	
		//columns headers
		$lines[0][] = array(
				'text' => Mage::helper('sales')->__('Products'),
				'feed' => 35
		);
	
		$lines[0][] = array(
				'text'  => Mage::helper('sales')->__('SKU'),
				'feed'  => 290,
				'align' => 'right'
		);
	
		$lines[0][] = array(
				'text'  => Mage::helper('sales')->__('Qty'),
				'feed'  => 435,
				'align' => 'right'
		);
	
		$lines[0][] = array(
				'text'  => Mage::helper('sales')->__('Price'),
				'feed'  => 360,
				'align' => 'right'
		);
	
		$lines[0][] = array(
				'text'  => Mage::helper('sales')->__('Tax'),
				'feed'  => 495,
				'align' => 'right'
		);
	
		$lines[0][] = array(
				'text'  => Mage::helper('sales')->__('Subtotal'),
				'feed'  => 565,
				'align' => 'right'
		);
	
		$lineBlock = array(
				'lines'  => $lines,
				'height' => 5
		);
	
		$this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$this->y -= 20;
	}
	
    
    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
	public function getPdf($invoices = array())
    {	if(Mage::getVersion()>='1.7')
   		 {   $this->_beforeGetPdf();
	        $this->_initRenderer('invoice');
	
	        $pdf = new Zend_Pdf();
	        $this->_setPdf($pdf);
	        $style = new Zend_Pdf_Style();
	        $this->_setFontBold($style, 10);
	
	        foreach ($invoices as $invoice) {
	            if ($invoice->getStoreId()) {
	                Mage::app()->getLocale()->emulate($invoice->getStoreId());
	                Mage::app()->setCurrentStore($invoice->getStoreId());
	            }
	            $page  = $this->newPage();
	            $order = $invoice->getOrder();
	            /* Add image */
	            $this->insertLogo($page, $invoice->getStore());
	            /* Add address */
	            $this->insertAddress($page, $invoice->getStore());
	            /* Add head */
	            $this->insertOrder(
	                $page,
	                $order,
	                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
	            );
	            $this->insertSignature($page, $order);
	            /* Add document text and number */
	           
	            $this->insertDocumentNumber(
	                $page,
	                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
	            );
	            /* Add table */
	            $this->_drawHeader($page);
		            /* Add body */
	            foreach ($invoice->getAllItems() as $item){
	                if ($item->getOrderItem()->getParentItem()) {
	                    continue;
	                }
	                /* Draw item */
	                $this->_drawItem($item, $page, $order);
	                $page = end($pdf->pages);
	            }
	            /* Add totals */
	            $this->insertTotals($page, $invoice);
	            if ($invoice->getStoreId()) {
	                Mage::app()->getLocale()->revert();
	            }
	        } 
	        $this->_afterGetPdf();
	        return $pdf;
    }
    else
    {
    	
    	$this->_beforeGetPdf();
    	$this->_initRenderer('invoice');
    	
    	$pdf = new Zend_Pdf();
    	$this->_setPdf($pdf);
    	$style = new Zend_Pdf_Style();
    	$this->_setFontBold($style, 10);
    	
    	foreach ($invoices as $invoice) {
    		if ($invoice->getStoreId()) {
    			Mage::app()->getLocale()->emulate($invoice->getStoreId());
    			Mage::app()->setCurrentStore($invoice->getStoreId());
    		}
    		$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
    		$pdf->pages[] = $page;
    	
    		$order = $invoice->getOrder();
    	
    		/* Add image */
    		$this->insertLogo($page, $invoice->getStore());
    	
    		/* Add address */
    		$this->insertAddress($page, $invoice->getStore());
    		
    		/* Add head */
    		$this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));
    	
    		$this->insertSignature($page, $order);
    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
    		$this->_setFontRegular($page);
    		$page->drawText(Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId(), 35, 780, 'UTF-8');
    	
    		/* Add table */
    		$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
    		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
    		$page->setLineWidth(0.5);
    	
    		$page->drawRectangle(25, $this->y, 570, $this->y -15);
    		$this->y -=10;
    	
    		/* Add table head */
    		$page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
    		$page->drawText(Mage::helper('sales')->__('Products'), 35, $this->y, 'UTF-8');
    		$page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
    		$page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
    		$page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
    		$page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
    		$page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');
    	
    		$this->y -=15;
    	
    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    	
    		/* Add body */
    		foreach ($invoice->getAllItems() as $item){
    			if ($item->getOrderItem()->getParentItem()) {
    				continue;
    			}
    	
    			if ($this->y < 15) {
    				$page = $this->newPage(array('table_header' => true));
    			}
    	
    			/* Draw item */
    			$page = $this->_drawItem($item, $page, $order);
    		}
    	
    		/* Add totals */
    		$page = $this->insertTotals($page, $invoice);
    	
    		if ($invoice->getStoreId()) {
    			Mage::app()->getLocale()->revert();
    		}
    	}
    	
    	$this->_afterGetPdf();
    	
    	return $pdf;
    }
   }
   protected function insertSignature(&$page, $order)

    {
    	

    	$this->y = $this->y ? $this->y : 815;
    	$answer =Mage::helper('opcsignature')->getSignature($order);
    	if($answer)
    	{	
    		$fist_name = $order->getBillingAddress()->getData('firstname');
    		$middle_name = $order->getBillingAddress()->getData('middlename');

    		$last_name = $order->getBillingAddress()->getData('lastname');

    		
    		$top = $this->y;

    		$top += 8;

    		$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));

    		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));

    		$page->setLineWidth(0.5);

    		$page->drawRectangle(25, $top, 570, ($top - 15));

    		

    		$this->_setFontRegular($page, 10);

    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

    		$page->drawText(Mage::helper('sales')->__('Signature'), 35, $this->y-2, 'UTF-8');
			

    		
    		$top-=20;	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

    		
    	

    		
	    	if($answer['type'] == 'image')
	    	{
	    		$image = $answer['value'];

	    	
	    		if (is_file($image)) {

	    			$image       = Zend_Pdf_Image::imageWithPath($image);

	    			$top         = $this->y-20; //top border of the page

	    			$widthLimit  = 90; //half of the page width

	    			$heightLimit = 90; //assuming the image is not a "skyscraper"

	    			$width       = $image->getPixelWidth();

	    			$height      = $image->getPixelHeight();

	    	

	    			//preserving aspect ratio (proportions)

	    			$ratio = $width / $height;

	    			if ($ratio > 1 && $width > $widthLimit) {

	    				$width  = $widthLimit;

	    				$height = $width / $ratio;

	    			} elseif ($ratio < 1 && $height > $heightLimit) {

	    				$height = $heightLimit;

	    				$width  = $height * $ratio;

	    			} elseif ($ratio == 1 && $height > $heightLimit) {

	    				$height = $heightLimit;

	    				$width  = $widthLimit;

	    			}

	    	

	    			$y1 = $top - 10 -$height;

	    			$y2 = $top-10;

	    			$x1 = 35;

	    			$x2 = $x1 + $width;

	    	

	    			//coordinates after transformation are rounded by Zend

	    			$font = $this->_setFontRegular($page, 10);

	    			$page->drawRectangle(25, ($top +15), 570, $top - 210);

	    			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
	    			$page->drawText($fist_name, 35, $top, 'UTF-8');
	    			$right_margin = $this->widthForStringUsingFontSize($fist_name, $font, 10)+40;
	    			if($middle_name)
	    			{
	    				$page->drawText($middle_name, $right_margin, $top, 'UTF-8');
	    				$right_margin += $this->widthForStringUsingFontSize($middle_name, $font, 10)+5;
	    			}
	     
	    			$page->drawText($last_name, $right_margin, $top, 'UTF-8');
	    			
	    			$page->drawImage($image, $x1, $y1, $x2, $y2);
					$this->y -=63;
			    
			    // $page->drawText(Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('sinnature_text1')->getValue('html'), 35, $top, 'UTF-8');
	    		 $dta=explode("/",Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('sinnature_text1')->getValue('html'));
				$signd=count($dta);
				 	$this->y -=40;
				 $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
				 foreach($dta as $iss)
				  {
					$page->drawText(strip_tags(ltrim($iss)),30, $this->y - 13, 'UTF-8');            
                     $this->y -=15;

				 }
					
					
					$this->_setFontRegular($page, 10);
 
	    			$this->y -=63;
					
	    			@unlink($answer['value']);

	    		}

	    	}
	    	elseif($answer['type'] == 'typed')
	    	{
	    		
	    		$font = $this->_setFontRegular($page, 10);
	    		
	    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

	    		$page->drawRectangle(25, ($top+5 ), 570, $top - 210);
	    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
	    		$this->_setFontRegular($page, 10);
	    		$top-=10;
	    		$page->drawText($fist_name, 35, $top, 'UTF-8');
	    		$right_margin = $this->widthForStringUsingFontSize($fist_name, $font, 10)+40;

	    		if($middle_name)
	    		{
	    			$page->drawText($middle_name, $right_margin, $top, 'UTF-8');
	    			$right_margin += $this->widthForStringUsingFontSize($middle_name, $font, 10)+5;
	    		}
	    		
               
	    		$page->drawText($last_name, $right_margin, $top, 'UTF-8');
	    		$font = $this->_setFontItalic($page, 10);
	    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
				$font = $this->_setFontHarlowItalic($page, 10);
				$page->setFillColor(new Zend_Pdf_Color_Html('#1f497d'));
	    		$page->drawText(Mage::helper('sales')->__($answer['value']), 35, ($top-15), 'UTF-8');
				$font = $this->_setFontItalic($page, 10);
	    		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
				//$page->drawText(Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('sinnature_text1')->getValue('html'), 35, $top, 'UTF-8');
		        $dta=explode("/",Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('sinnature_text1')->getValue('html'));
			    $signd=count($dta);
				$this->y -=40;
				 foreach($dta as $iss)
				  {
			 
					    $page->drawText(strip_tags(ltrim($iss)), 30, $this->y - 13, 'UTF-8');            
                         $this->y -=15;
				 }
				 $this->_setFontRegular($page, 10);
	    		$this->y -=55;
	    	}
	    	
    	}
    }
    
    
    /**

     * After getPdf processing

     */

    protected function _afterGetPdf() {

    	$translate = Mage::getSingleton('core/translate');

    	/* @var $translate Mage_Core_Model_Translate */

    	$translate->setTranslateInline(true);
    }
   }
