<?php
ini_set('display_errors', 1);
$mageFileName = getcwd() . '/app/Mage.php';
require $mageFileName;
Mage::app();
echo "<b> Server Cookie Domain Configuration : </b> ".ini_get('session.cookie_domain')."<br>";
foreach (Mage::app()->getStores() as $store) {

    echo "<b>" . $store->getName() . "</b><br>";
    $configCookieDomain = Mage::getStoreConfig('web/cookie/cookie_domain', $store->getId());
    $storeConfigUrl = Mage::getStoreConfig('web/unsecure/base_url', $store->getId());
    $sourceUrl = parse_url($storeConfigUrl);
    $storeDomain = $sourceUrl['host'];
    $cookieDomainResult = ($configCookieDomain == $storeDomain || $configCookieDomain == '.' . $storeDomain) ? "" : "not";
    echo "Config cookie Domain : " . $configCookieDomain . " and Store Domain: " . $storeDomain . " " . $cookieDomainResult . " configured properly<br>";
}
//echo "<b>Request Cookies:</b> ";
$requestCookie = Mage::app()->getRequest()->getHeader('cookie');
$requestCookieArr = explode(';', $requestCookie);
$sessionIds = array();
foreach ($requestCookieArr as $requestCookieItem) {
    $cookieValue = explode('=', $requestCookieItem);
    // echo $requestCookieItem."<br>";
    if (trim($cookieValue[0]) == 'frontend' || trim($cookieValue[0]) == 'adminhtml') {
        $cookieName = trim($cookieValue[0]);
        $sessionId = trim($cookieValue[1]);
        $sessionIds[$cookieName][] = $sessionId;
    }
}
$areas = array("frontend", "adminhtml");
foreach ($areas as $area => $cookieName) {
    echo "<b>validating " . $cookieName . " cookie </b><br>";
    $cookieExpires = Mage::getModel('core/cookie')->getLifetime($cookieName);
    $cookiePath = Mage::getModel('core/cookie')->getPath($cookieName);
    $cookieDomain = Mage::getModel('core/cookie')->getDomain($cookieName);
    $cookieSecure = Mage::getModel('core/cookie')->isSecure($cookieName);
    $cookieHttpOnly = Mage::getModel('core/cookie')->getHttponly($cookieName);
    echo "Cookie Lifetime : " . $cookieExpires . " <br>";
    echo "Cookie Path : " . $cookiePath . " <br>";
    echo "Cookie Domain : " . $cookieDomain . " <br>";
    echo "Cookie Is Secure : " . $cookieSecure . " <br>";
    echo "Cookie Httponly : " . $cookieHttpOnly . " <br>";
    if (count($sessionIds[$cookieName]) > 1) {
        echo "<span style='color:red'><b>We have " . count($sessionIds[$cookieName]) . " " . $cookieName . " Cookies with values : </b>" . implode(',', $sessionIds[$cookieName]) . "<br>";
        //$encryptedSessionId =  Mage::getSingleton("core/session")->getEncryptedSessionId();
        $encryptedSessionId = Mage::getModel('core/cookie')->get($cookieName);
        echo "Original Cookie value : " . $encryptedSessionId . "<br>";
        echo "Please verify the Subdomain and Main Site Cookie Domain Configuration</span><br>";
    }
}


?>