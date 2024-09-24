<?php
use Mage_Customer_Model_Customer as C;

/**
 * 2024-06-02
 * @used-by HCG\MailChimp\Tags::attCustomer() (https://github.com/thehcginstitute-com/m1/issues/589)
 */
function df_gender_s(C $c):string {return df_tr($c->getGender(), df_genders());}

/**
 * 2024-06-02
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_setMailchimpTagToCustomer() (https://github.com/thehcginstitute-com/m1/issues/589)
 */
function df_gender_s2i(string $s):int {return dfa(array_flip(df_genders()), $s, 0);}

/**
 * 2024-06-02
 * @used-by df_gender_s()
 * @used-by df_gender_s2i()
 */
function df_genders():array {return [1 => 'Male', 2 => 'Female'];}