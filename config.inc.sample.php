<?php
require("Tools/Debug.php");

// Constants
define("MAILING_CORE_TEMPLATE_FOLDER", dirname(__FILE__)."/DefaultTemplates");

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