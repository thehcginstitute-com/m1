<?php

class IWD_OrderManager_Model_Observer_History
{
    /**
     * @param Varien_Event_Observer $observer
     */
    function addAdminToOrderComment(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_ordermanager')->isOrderManagerEnabled()) {
            return;
        }

        $comment = $observer->getEvent()->getData('data_object');
        $commentId = $comment->getData('entity_id');
        if (empty($commentId)) {
            $user = Mage::getSingleton('admin/session');
            if ($user && $user->getUser()) {
                $userId = $user->getUser()->getUserId();
                $userEmail = $user->getUser()->getEmail();
                $comment->setAdminId($userId);
                $comment->setAdminEmail($userEmail);
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    function changeNewLinesToBr(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_ordermanager')->isOrderManagerEnabled()) {
            return;
        }

        $comment = $observer->getEvent()->getData('data_object');
        $commentText = $comment->getComment();
        $commentText = nl2br($commentText);
        $comment->setComment($commentText);
    }
}
