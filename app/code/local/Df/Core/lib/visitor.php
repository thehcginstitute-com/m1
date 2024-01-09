<?php
/** @return string */
function df_visitor_ip() {return df_my_local() ? '176.237.6.164' : df_mage_http_h()->getRemoteAddr();}