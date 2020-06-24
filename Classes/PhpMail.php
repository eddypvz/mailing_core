<?php
class PhpMail {
    public function send($mailTo = null, $from = null, $subject = null, $content = "", $cc = "", $bcc = "") {

        $arrStatus = [];
        $arrStatus["status"] = 0;
        $arrStatus["msg"] = "";

        $headers = "From: " . strip_tags($from) . "\r\n";
        $headers .= "Reply-To: ". strip_tags($from) . "\r\n";
        $headers .= "CC: {$cc}\r\n";
        $headers .= "Bcc: {$bcc}" . "\r\n" .
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        if (!empty($mailTo)) {
            try {
                $response = @mail($mailTo, $subject, $content, $headers);

                if ($response) {
                    $arrStatus["status"] = 1;
                    $arrStatus["msg"] = "Sended OK";
                }
                else {
                    $arrStatus["msg"] = "Error sending email";
                }
            } catch (Exception $e) {
                $arrStatus["msg"] = "Error sending email, please verify logs";
            }
        }

        return $arrStatus;
    }
}