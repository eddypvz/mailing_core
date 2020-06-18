<?php
require(dirname(__FILE__)."/../Vendor/sendgrid-php/sendgrid-php.php");

class SendgridAPI {

    private $api_key;

    function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function send($mailTo = null, $mailToName = null, $from = null, $fromName = null, $subject = null, $content = "", $cc = "", $bcc = "") {

        $arrStatus = [];
        $arrStatus["status"] = 0;
        $arrStatus["msg"] = "";

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from, $fromName);
        $email->setSubject($subject);
        $email->addTo($mailTo, $mailToName);
        $email->addContent(
            "text/html", $content
        );

        $cc = trim($cc);
        $bcc = trim($bcc);

        if ($cc !== "") {
            $ccArray = explode(",", $cc);
            foreach ($ccArray as $emailItem) {
                $email->addCc($emailItem);
            }
        }
        if ($cc !== "") {
            $bccArray = explode(",", $bcc);
            foreach ($bccArray as $emailItem) {
                $email->addBcc($emailItem);
            }
        }

        $sendgrid = new \SendGrid($this->api_key);
        try {
            $response = $sendgrid->send($email);
            $statusCode = $response->statusCode();

            if ($statusCode == 200 || $statusCode == 202) {
                $arrStatus["status"] = 1;
                $arrStatus["msg"] = "Sended OK";
            }
            else {
                $errorMsg = @json_decode($response->body(), true);
                $arrStatus["msg"] = (isset($errorMsg["errors"][0]["message"])) ? $errorMsg["errors"][0]["message"] : "";
            }
        } catch (Exception $e) {
            $arrStatus["msg"] = $e->getMessage();
        }
        return $arrStatus;
    }
}