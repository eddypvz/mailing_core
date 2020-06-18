<?php
require("Tools/Debug.php");

// Constants
define("MAILING_CORE_TEMPLATE_FOLDER", "/Templates");
define("MAILING_CORE_TEMPLATE_PATH", dirname(__FILE__).MAILING_CORE_TEMPLATE_FOLDER);

// Configurations for clients
define("MAILING_CORE", [
   "default_client" => "sendgrid", //Available: sendgrid, mailgun, phpmail
   "sendgrid" => [
       "api_key" => "--empty--",
   ],
   "mailgun" => [
       "api_key" => "--empty--"
   ]
]);