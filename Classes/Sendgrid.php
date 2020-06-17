<?php
require("Vendor/sendgrid-php/sendgrid-php.php");

class SendgridAPI {

    public function send($mailTo = null, $mailToName = null, $from = null, $fromName = null, $subject = null, $content = "", $cc = "") {

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

        $sendgrid = new \SendGrid(MAILING_CORE["sendgrid"]["api_key"]);
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