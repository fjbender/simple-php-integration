<?php
/**
 * @TODO: Define $defaults array() here
 */
$defaults = array(
    "aid" => '',//"your_account_id",
    "mid" => '',//"your_merchant_id",
    "portalid" => '',
    "key" => hash("md5", ""), // the key has to be hashed as md5
    "mode" => "test", // can be "live" for actual transactions
    "api_version" => "3.10",
    "encoding" => "UTF-8"
);