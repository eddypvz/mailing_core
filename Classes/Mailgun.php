<?php
class MailgunAPI {

    private $api_key;
    private $default_domain;

    function __construct($api_key, $defaultDomain = "") {
        $this->api_key = $api_key;
        $this->default_domain = $defaultDomain;
    }

    public function send($from = "", $to = "", $subject = "", $mail_content = "", $cc = "", $bcc = "", $attachment = false, $domain = "") {

        // ATTACHMENT ARRAY EXAMPLE
        // $attachment = [
        //     ["name" => $_FILES["field"]['name'], "type" => $_FILES["field"]['type'], "path" => $_FILES["field"]['tmp_name']],
        //     ["name" => $_FILES["field"]['name'], "type" => $_FILES["field"]['type'], "path" => $_FILES["field"]['tmp_name']],
        // ];

        $arrResponse = [];
        $arrResponse["status"] = 0;
        $arrResponse["msg"] = "";

        $domainSend = ($domain !== "") ? $domain : $this->default_domain;

        if ($domainSend !== "" && $this->api_key !== "") {

            $url = "https://api.mailgun.net/v3/" . $domainSend . "/messages";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->api_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $plain = strip_tags(preg_replace('/\<br(\s*)?\/?\>/i', "\n", $mail_content));

            $postFields = array(
                'from' => $from . '@' . $domainSend,
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
                'subject' => $subject,
                'html' => $mail_content,
                'text' => $plain,
            );
            if(empty($cc)){
                if(isset($postFields['cc'])){
                    unset($postFields['cc']);
                }
            }
            if (is_array($attachment)) {
                $attachmentNumber = 1;
                foreach ($attachment as $attachmentTmp) {
                    $postFields["attachment[{$attachmentNumber}]"] = curl_file_create($attachmentTmp["path"], $attachmentTmp["type"], $attachmentTmp["name"]);
                    $attachmentNumber++;
                }
            }

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if ($response === false) {
                $arrResponse["msg"] = curl_error($ch);
            }
            else {
                $responseDecode = @json_decode($response);
                $info = curl_getinfo($ch);
                curl_close($ch);

                if ($info['http_code'] == 200) {
                    if (isset($responseDecode->id)) {
                        $arrResponse["status"] = 1;
                        $arrResponse["msg"] = "Send OK";
                    }
                    else {
                        if (isset($responseDecode->message)) {
                            $arrResponse["msg"] = $responseDecode->message;
                        }
                        else {
                            $arrResponse["msg"] = "Error in send, no error description";
                        }
                    }
                }
                else {
                    $arrResponse["msg"] = "Error in send, http code {$info['http_code']}";
                }
            }
        }
        else{
            $arrResponse["msg"] = "Mailgun configuration error";
        }

        return $arrResponse;
    }
}