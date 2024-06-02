<?php
use Mage_Newsletter_Model_Resource_Subscriber_Collection as C;
/**
 * 2024-06-02 "Implement `df_subscriber_c()`": https://github.com/thehcginstitute-com/m1/issues/626
 * @used-by Glew_Service_Model_Types_Subscribers::load()
 */
function df_subscriber_c():C {return new C;}