<?php
/**
 * Lesti_Fpc (http:gordonlesti.com/lestifpc)
 *
 * PHP version 5
 *
 * @link      https://github.com/GordonLesti/Lesti_Fpc
 * @package   Lesti_Fpc
 * @author    Gordon Lesti <info@gordonlesti.com>
 * @copyright Copyright (c) 2013-2016 Gordon Lesti (http://gordonlesti.com)
 * @license   http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class Lesti_Fpc_Helper_Block_Messages
 */
class Lesti_Fpc_Helper_Block_Messages extends Mage_Core_Helper_Abstract
{
    /**
     * @param $layout
     * @return mixed
     */
    function initLayoutMessages(
        Mage_Core_Model_Layout $layout,
		# 2024-02-27 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# «Invalid messages storage "tag/session" for layout messages initialization»:
		# https://github.com/thehcginstitute-com/m1/issues/430
        $messagesStorage = ['catalog/session', 'checkout/session', 'customer/session']
    )
    {
        $block = $layout->getMessagesBlock();
        if ($block) {
            foreach ($messagesStorage as $storageName) {
                $storage = Mage::getSingleton($storageName);
                if ($storage) {
                    $block->addMessages($storage->getMessages(true));
                    $block->setEscapeMessageFlag(
                        $storage->getEscapeMessages(true)
                    );
                } else {
                    Mage::throwException(
                        Mage::helper('core')->__(
                            'Invalid messages storage "%s" for layout '.
                            'messages initialization',
                            (string)$storageName
                        )
                    );
                }
            }
        }
        return $layout;
    }
}
