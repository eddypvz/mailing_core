<?php
require ("MailingCore.php");

// Constants
//define("MAILING_CORE_TEMPLATE_FOLDER", "/Templates");

// Configure
/*$mailingCore = MailingCore::getInstance();
$mailingCore->SetTemplateConfigs(dirname(__FILE__)."/Templates", "http://localhost/mailing_core/Templates");
$mailingCore->SetDefaultMailCore("sendgrid");
$mailingCore->EnableSendgrid("-- your api key --");
$template = $mailingCore->LoadTemplate("default", [
    "IMG_BANNER" => "img/banner.jpg"
]);

// Send mail
$emailSended = MailingCore::getInstance()->SendMail("youremail@gmail.com", "noreply@your-domain.com", "Subject for mail", $template);*/
// dd($emailSended);