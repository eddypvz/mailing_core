<?php
// Include config
if(!file_exists("config.inc.php")) {
    die("Mailing config not exists");
}
else {
    require("config.inc.php");
}
require("Classes/Sendgrid.php");

class MailingCore {

    static function ValidateTemplate($templateName) {
        $tplFile = MAILING_CORE_TEMPLATE_PATH."/".$templateName."/tpl.php";
        if (file_exists($tplFile)) {
            return $tplFile;
        }
        else {
            return false;
        }
    }

    static function LoadTemplate($templateName, $data = []) {

        // Default replaced vars
        $reservedVars = [
            "TPL_URL" => str_replace("\\",'/',"http://".$_SERVER['HTTP_HOST'].substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT']))).MAILING_CORE_TEMPLATE_FOLDER."/".$templateName
        ];

        // Vars to replace
        $vars = array_merge($reservedVars, $data);

        $tplFile = MailingCore::ValidateTemplate($templateName);
        if (!file_exists($tplFile)) {
            $tplFile = MailingCore::ValidateTemplate("default");
        }

        // If has a template
        if ($tplFile) {
            $tplTemp = file_get_contents($tplFile);
            foreach ($vars as $keyVar => $valueVar) {
                $tplTemp = str_replace("::{$keyVar}::", $valueVar, $tplTemp);
            }
            return $tplTemp;
        }
        else{
            return false;
        }
    }

    static function SendMail($mailTo, $from, $subject, $content = "", $cc = "", $mailClient = "") {

        // Vars
        $mailTo = (is_array($mailTo)) ? $mailTo[0] : $mailTo;
        $mailToName = (is_array($mailTo)) ? $mailTo[1] : null;
        $from = (is_array($from)) ? $from[0] : $from;
        $fromName = (is_array($from)) ? $from[1] : null;

        $arrStatus = [];
        $arrStatus["status"] = 0;
        $arrStatus["msg"] = 0;

        if (MAILING_CORE["default_client"] === "sendgrid" || $mailClient === "sendgrid") {
            $sendgrid = new SendgridAPI();
            $arrStatus = $sendgrid->send($mailTo, $mailToName, $from, $fromName, $subject, $content, $cc);
        }
        else if (MAILING_CORE["default_client"] === "mailgun" || $mailClient === "mailgun") {
            // Mailgun integration here
        }
        else if (MAILING_CORE["default_client"] === "phpmail" || $mailClient === "phpmail") {
            // PHP integration here
        }
        else{
            $arrStatus["msg"] = "Mail client is not defined";
        }

        return $arrStatus;
    }
}