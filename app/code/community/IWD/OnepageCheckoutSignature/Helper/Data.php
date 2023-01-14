<?php

/**
 * Signature to Image: A supplemental script for Signature Pad that
 * generates an image of the signature’s JSON output server-side using PHP.
 *
 * @project ca.thomasjbradley.applications.signaturetoimage
 * @author Thomas J Bradley <hey@thomasjbradley.ca>
 * @link http://thomasjbradley.ca/lab/signature-to-image
 * @link http://github.com/thomasjbradley/signature-to-image
 * @copyright Copyright MMXI–, Thomas J Bradley
 * @license New BSD License
 * @version 1.1.0
 */

/**
 * Accepts a signature created by signature pad in Json format
 * Converts it to an image resource
 * The image resource can then be changed into png, jpg whatever PHP GD supports
 *
 * To create a nicely anti-aliased graphic the signature is drawn 12 times it's original size then shrunken
 *
 * @param string|array $json
 * @param array $options OPTIONAL; the options for image creation
 * imageSize => array(width, height)
 * bgColour => array(red, green, blue) | transparent
 * penWidth => int
 * penColour => array(red, green, blue)
 * drawMultiplier => int
 *
 * @return object
 */

class IWD_OnepageCheckoutSignature_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	
	function getSignature($order) {
		
		$collection = Mage::getModel('opcsignature/signaturer')->getCollection();
		
		$collection->addFieldToFilter('order_id', $order->getId());
		$json = "";
		$name = "";
		
		foreach( $collection as $item)
		{
			$name = $item['signature_name'];
			$json = $item['signature_json'];
		}
		
		if(empty($json) && empty($name))
			return null;
		
		$answer = array();
		if(!empty($json))
		{
			$img = $this->sigJsonToImage($json);
			$media = Mage::getBaseDir('media')."/iwd_signature/";
			
			if(!file_exists($media.".htaccess"))
			{
				@$file = fopen($media.".htaccess","x+");
				@fwrite($file,'deny from all');
				@fclose($file);
			}
			
			if(!file_exists($media))
				@mkdir($media, 0755);
			$img_url = $media."signature_".$order->getId().'.png';
			imagepng($img, $img_url);
			imagedestroy($img);
			$answer['type'] = 'image';
			$answer['value'] =  $img_url;
			return $answer;
			
		}
		elseif(!empty($name))
		{
			$answer['type'] = 'typed';
			$answer['value'] = $name;
			return $answer;
		}
	}
	
	
	
	
	function sigJsonToImage ($json, $options = array()) {
	  $defaultOptions = array(
	    'imageSize' => array(285, 55)
	    ,'bgColour' => array(0xff, 0xff, 0xff)
	    ,'penWidth' => 2
	    ,'penColour' => array(0x14, 0x53, 0x94)
	    ,'drawMultiplier'=> 12
	  );
	
	  $options = array_merge($defaultOptions, $options);
	
	  $img = imagecreatetruecolor($options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][1] * $options['drawMultiplier']);
	
	  if ($options['bgColour'] == 'transparent') {
	    imagesavealpha($img, true);
	    $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
	  } else {
	    $bg = imagecolorallocate($img, $options['bgColour'][0], $options['bgColour'][1], $options['bgColour'][2]);
	  }
	
	  $pen = imagecolorallocate($img, $options['penColour'][0], $options['penColour'][1], $options['penColour'][2]);
	  imagefill($img, 0, 0, $bg);
	  if (is_string($json))
	    $json = json_decode(stripslashes(preg_replace('/^([\'"])(.*)\\1$/', '\\2', $json)));
	
	  foreach ($json as $v)
	    $this->drawThickLine($img, $v->lx * $options['drawMultiplier'], $v->ly * $options['drawMultiplier'], $v->mx * $options['drawMultiplier'], $v->my * $options['drawMultiplier'], $pen, $options['penWidth'] * ($options['drawMultiplier'] / 2));
	
	  $imgDest = imagecreatetruecolor($options['imageSize'][0], $options['imageSize'][1]);
	
	  if ($options['bgColour'] == 'transparent') {
	    imagealphablending($imgDest, false);
	    imagesavealpha($imgDest, true);
	  }
	
	  imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $options['imageSize'][0], $options['imageSize'][0], $options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][0] * $options['drawMultiplier']);
	  imagedestroy($img);
	
	  return $imgDest;
	}
	
	/**
	* Draws a thick line
	* Changing the thickness of a line using imagesetthickness doesn't produce as nice of result
	*
	* @param object $img
	* @param int $startX
	* @param int $startY
	* @param int $endX
	* @param int $endY
	* @param object $colour
	* @param int $thickness
	*
	* @return void
	*/
	function drawThickLine ($img, $startX, $startY, $endX, $endY, $colour, $thickness) {
	  $angle = (atan2(($startY - $endY), ($endX - $startX)));
	
	  $dist_x = $thickness * (sin($angle));
	  $dist_y = $thickness * (cos($angle));
	
	  $p1x = ceil(($startX + $dist_x));
	  $p1y = ceil(($startY + $dist_y));
	  $p2x = ceil(($endX + $dist_x));
	  $p2y = ceil(($endY + $dist_y));
	  $p3x = ceil(($endX - $dist_x));
	  $p3y = ceil(($endY - $dist_y));
	  $p4x = ceil(($startX - $dist_x));
	  $p4y = ceil(($startY - $dist_y));
	
	  $array = array(0=>$p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
	  imagefilledpolygon($img, $array, (count($array)/2), $colour);
	}

	public function ifEnabled()
	{
		if (Mage::getStoreConfig('opcsignature/general/enabled'))
			return true;
		return false;
	}
}