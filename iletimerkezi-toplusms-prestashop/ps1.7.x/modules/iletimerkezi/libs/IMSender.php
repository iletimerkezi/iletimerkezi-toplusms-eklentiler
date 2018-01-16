<?php
class IMSender {
    protected $debug = true;

    public function isUserExists($username, $password) {

        $xml = '
        <request>
            <authentication>
                <username>'.$username.'</username>
                <password>'.$password.'</password>
            </authentication>
        </request>
        ';

        $res    = $this->sendRequest('get-balance', $xml);
        $status = $this->parseStatus($res);

        if($status == '200')
            return [true, $this->parseBalance($res)];

        return [false, $this->parseMessage($res)];
    }

    public function isSenderExists($username, $password, $sender) {

        $xml = '
        <request>
            <authentication>
                <username>'.$username.'</username>
                <password>'.$password.'</password>
            </authentication>
        </request>
        ';

        $res    = $this->sendRequest('get-sender', $xml);
        $status = $this->parseStatus($res);

        if($status == '200') {
            $sender_list = $this->parseSender($res);
            return [in_array($sender, $sender_list)?true:false, $sender_list];
        }

        return [false, null];
    }

    public function sendOneSms($username, $password, $to, $text, $sender) {

        $xml = '
        <request>
            <authentication>
                <username>'.$username.'</username>
                <password>'.$password.'</password>
            </authentication>
            <order>
                <sender>'.$sender.'</sender>
                <sendDateTime></sendDateTime>
                <message>
                    <text><![CDATA['.$text.']]></text>
                    <receipents>
                        <number>'.$to.'</number>
                    </receipents>
                </message>
            </order>
        </request>';

        if($this->debug)
            error_log('Send One SMS: '.$xml);

        $result = $this->sendRequest('send-sms', $xml);
        $status = $this->parseStatus($result);

        if($status == '200') {
            return [true, $this->parseSms($result)];
        }

        return [false, $this->parseMessage($result)];
    }

    public function sendMultiSms() {

    }

    public function checkReport($report_id, $uname, $password) {

        $xml = '<request>
                    <authentication>
                        <username>'.$uname.'</username>
                        <password>'.$password.'</password>
                    </authentication>
                    <order>
                        <id>'.$report_id.'</id>
                        <page>1</page>
                        <rowCount>10</rowCount>
                    </order>
                </request>';

        $result = $this->sendRequest('get-report', $xml);
        $status = $this->parseStatus($result);
        if($status == 200) {

            $report = $this->parseReport($result);

            return [true, $report];
        }

        return [false];
    }

    private function sendRequest($req_path, $xml) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.iletimerkezi.com/v1/'.$req_path);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);

        if($this->debug)
            error_log(var_export($result, 1));

        return $result;
    }

    public function parseStatus($str) {

        $re = '/\<code\>(.*)\<\/code\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        if($this->debug)
            error_log('parseStatus ::: '.var_export($matches, 1));

        return $matches[0][1];
    }

    public function parseMessage($str) {

        $re = '/\<message\>(.*)\<\/message\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        if($this->debug)
            error_log('parseMessage ::: '.var_export($matches, 1));

        return $matches[0][1];
    }

    public function parseBalance($str) {

        $re = '/\<sms\>(.*)\<\/sms\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        if($this->debug)
            error_log('parseBalance ::: '.var_export($matches, 1));

        return $matches[0][1];
    }

    public function parseSender($str) {

        $re = '/\<sender\>(.*)\<\/sender\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $sender_list = [];
        foreach ($matches as $match) {
            $sender_list[] = $match[1];
        }

        if($this->debug)
            error_log('parseSender ::: '.var_export($sender_list, 1));

        return $sender_list;
    }

    public function parseSms($str) {

        $re = '/\<id\>(.*)\<\/id\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        if($this->debug)
            error_log('parseSms ::: '.var_export($matches, 1));

        return $matches[0][1];
    }

    public function parseReport($str) {

        $re = '/\<total\>(.*)\<\/total\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $total = $matches[0][1];

        $re = '/\<delivered\>(.*)\<\/delivered\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $delivered = $matches[0][1];

        $re = '/\<undelivered\>(.*)\<\/undelivered\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $undelivered = $matches[0][1];

        $re = '/\<waiting\>(.*)\<\/waiting\>/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $waiting = $matches[0][1];

        if($this->debug)
            error_log('report ::: '.var_export($str,1));

        return [
            'total'       => $total,
            'delivered'   => $delivered,
            'undelivered' => $undelivered,
            'waiting'     => $waiting
        ];
    }
}