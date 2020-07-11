<?php
require("Tools/Debug.php");
require("Classes/Sendgrid.php");
require("Classes/Mailgun.php");
require("Classes/PhpMail.php");

class MailingCore {

    private static $instance = null;
    private $config;
    private $template_path;
    private $template_uri;

    private function __construct() {
        $this->config = [];
    }

    public static function getInstance() {
        if (self::$instance == null)
        {
            self::$instance = new MailingCore();
        }

        return self::$instance;
    }

    public function SetDefaultMailCore($mailcore) {
        $this->config["default_core"] = $mailcore;
    }

    public function SetTemplateConfigs($templatePath, $templateUrl) {
        $this->template_path = $templatePath;
        $this->template_uri = $templateUrl;
    }

    public function EnableSendgrid($api_key) {
        $this->config["sendgrid"] = [];
        $this->config["sendgrid"]["api_key"] = $api_key;
    }

    public function EnableMailgun($api_key, $defaultDomain = "") {
        $this->config["mailgun"] = [];
        $this->config["mailgun"]["api_key"] = $api_key;
        $this->config["mailgun"]["default_domain"] = $defaultDomain;
    }

    public function ValidateTemplate($templateName) {
        $tplFile = $this->template_path."/".$templateName."/tpl.php";
        if (file_exists($tplFile)) {
            return $tplFile;
        }
        else {
            return false;
        }
    }

    public function LoadTemplate($templateName, $data = []) {

        $tplFile = $this->ValidateTemplate($templateName);
        if (!file_exists($tplFile)) {
            $templateName = "default";
            $tplFile = $this->ValidateTemplate("default");
        }

        // If has a template
        if ($tplFile) {

            // Default replaced vars
            $reservedVars = [
                "TPL_URL" => $this->template_uri."/".$templateName
            ];

            // Vars to replace
            $vars = array_merge($reservedVars, $data);

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

    public function SendMail($mailTo, $from, $subject, $content = "", $cc = "", $bcc = "", $mailClient = "") {

        // Vars
        $mailTo = (is_array($mailTo)) ? $mailTo[0] : $mailTo;
        $mailToName = (is_array($mailTo)) ? $mailTo[1] : null;
        $from = (is_array($from)) ? $from[0] : $from;
        $fromName = (is_array($from)) ? $from[1] : null;

        $arrStatus = [];
        $arrStatus["status"] = 0;
        $arrStatus["msg"] = 0;

        if ($this->config["default_core"] === "sendgrid" || $mailClient === "sendgrid") {
            if(isset($this->config["sendgrid"]["api_key"])) {
                $sendgrid = new SendgridAPI($this->config["sendgrid"]["api_key"]);
                $arrStatus = $sendgrid->send($mailTo, $mailToName, $from, $fromName, $subject, $content, $cc, $bcc);
            }
            else {
                $arrStatus["msg"] = "Sendgrid is not enabled";
            }
        }
        else if ($this->config["default_core"] === "mailgun" || $mailClient === "mailgun") {
            if(isset($this->config["mailgun"]["api_key"])) {
                $mailgun = new MailgunAPI($this->config["mailgun"]["api_key"], $this->config["mailgun"]["default_domain"]);
                $arrStatus = $mailgun->send($from, $mailTo, $subject, $content, $cc, $bcc, false);
            }
            else {
                $arrStatus["msg"] = "Mailgun is not enabled";
            }
        }
        else if ($this->config["default_core"] === "phpmail" || $mailClient === "phpmail") {
            // PHP integration here
            $phpMail = new PhpMail();
            $arrStatus = $phpMail->send($mailTo, $from, $subject, $content, $cc, $bcc);
        }
        else{
            $arrStatus["msg"] = "Mail client is not defined";
        }

        return $arrStatus;
    }
}