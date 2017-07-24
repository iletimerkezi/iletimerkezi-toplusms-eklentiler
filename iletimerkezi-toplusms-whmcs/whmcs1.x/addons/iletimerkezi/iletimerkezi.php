<?php
/* Iletimerkezi SMS Eklentisi
 * whmcsSMS - http://www.whmcssms.com
 */
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function iletimerkezi_config() {
    $configarray = array(
        "name" => "Iletimerkezi Sms",
        "description" => "Iletimerkezi sms eklentisi, <a href='http://whmcssms.com'>whmcssms.com</a> dan detaylara bakabilirsiniz.",
        "version" => "1.0",
        "author" => "Emarka Bilgi ve Iletisim Teknolojileri",
		"language" => "turkish",
    );
    return $configarray;
}

function iletimerkezi_activate() {

    $query = "CREATE TABLE IF NOT EXISTS `mod_iletimerkezi_messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`sender` varchar(40) NOT NULL,`to` varchar(15) NULL,`text` text NULL,`msgid` varchar(50) NULL,`status` varchar(10) NULL,`errors` TEXT NULL,`logs` TEXT NULL,`user` int(11) NULL,`datetime` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$result = mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_iletimerkezi_settings` ( `id` int(11) NOT NULL AUTO_INCREMENT,`apiparams` varchar(500) NOT NULL,`wantsmsfield` int(11) NULL,`gsmnumberfield` int(11) NULL,`version` varchar(6) NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$result = mysql_query($query);

    $query = "INSERT INTO `mod_iletimerkezi_settings` ( `apiparams`, `wantsmsfield`, `gsmnumberfield`, `version`) VALUES ( '', 0, 0,'1.0');";
	$result = mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_iletimerkezi_templates` (`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(50) NOT NULL,`type` enum('client','admin') NOT NULL,`admingsm` varchar(255) NOT NULL,`template` varchar(240) NOT NULL,`variables` varchar(500) NOT NULL,`active` tinyint(1) NOT NULL,`extra` varchar(3) NOT NULL,`description` TEXT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13;";
	$result = mysql_query($query);

    //Creating hooks
	require_once("smsclass.php");
    $class = new iletimerkezi();
    $class->checkHooks();

    return array('status'=>'success','description'=>'iletimerkezi whmcsSMS aktifleştirildi.');
}

function iletimerkezi_deactivate() {

    $query = "DROP TABLE `mod_iletimerkezi_templates`";
	$result = mysql_query($query);
    $query = "DROP TABLE `mod_iletimerkezi_settings`";
    $result = mysql_query($query);
    $query = "DROP TABLE `mod_iletimerkezi_messages`";
    $result = mysql_query($query);

    return array('status'=>'success','description'=>'iletimerkezi whmcsSMS pasifleştirildi.');
}

function iletimerkezi_upgrade($vars) {
    $version = $vars['version'];

    switch($version){
        case "1.0":
            $sql = "ALTER TABLE `mod_iletimerkezi_messages` ADD `errors` TEXT NULL AFTER `status` ;ALTER TABLE `mod_iletimerkezi_templates` ADD `description` TEXT NULL ;ALTER TABLE `mod_iletimerkezi_messages` ADD `logs` TEXT NULL AFTER `errors` ;";
            mysql_query($sql);

            $sql = "ALTER TABLE `mod_iletimerkezi_settings` CHANGE `apiparams` `apiparams` VARCHAR( 500 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;";
            mysql_query($sql);
    }

    $class = new iletimerkezi();
    $class->checkHooks();
}

function iletimerkezi_output($vars){
    $modulelink = $vars['modulelink'];
    $version    = $vars['version'];
    $LANG       = $vars['_lang'];
    putenv("TZ=Europe/Istanbul");

    $class  = new iletimerkezi();
    $tab    = '';
    $tab    = $_GET['tab'];
    $credit =  $class->getBalance();
    if($credit) {
        $settings  = $class->getSettings();
        $apiparams = json_decode($settings['apiparams']);
        $balance = $LANG['credit'].': <b>'.$credit.'</b> <a style=" background: none;
    border: none;color: red;display: inline;margin: 0;padding: 0 10px;text-decoration: none;" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm='.$apiparams->iletimerkezi_username.'&password='.$apiparams->iletimerkezi_password.'">SMS Satin Al</a>';
    }
    $getdomain = $class->getDomain();

    echo '
    <div id="clienttabs">
        <ul>
            <li class="' . (($tab == "settings")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&tab=settings">'.$LANG['settings'].'</a></li>
            <li class="' . ((@$_GET['type'] == "client")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&tab=templates&type=client">'.$LANG['clientsmstemplates'].'</a></li>
            <li class="' . ((@$_GET['type'] == "admin")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&tab=templates&type=admin">'.$LANG['adminsmstemplates'].'</a></li>
            <li class="' . (($tab == "sendcustomer")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&tab=sendcustomer">'.$LANG['sendcustomer'].'</a></li>
            <li class="' . (($tab == "sendbulk")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&tab=sendbulk">'.$LANG['sendsms'].'</a></li>
            <li class="' . (($tab == "messages")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&amp;tab=messages">'.$LANG['messages'].'</a></li>
            <li class="' . (($tab == "notifications")?"tabselected":"tab") . '"><a href="addonmodules.php?module=iletimerkezi&amp;tab=notifications">'.$LANG['notifications'].'</a></li>
            <li style="float:right;">'.$balance.'</li>
        </ul>
    </div>';

    echo '<div id="tab_content">';
    if (empty($tab) || $tab == "settings")
    {
        /* UPDATE SETTINGS */
        if ($_POST['params']) {
            $update = array(
                "apiparams"            => json_encode($_POST['params']),
                'wantsmsfield'         => $_POST['wantsmsfield'],
                'gsmnumberfield'       => $_POST['gsmnumberfield'],
            );
            update_query("mod_iletimerkezi_settings", $update, "");
        }
        /* UPDATE SETTINGS */

        $settings = $class->getSettings();
        $apiparams = json_decode($settings['apiparams']);

        /* CUSTOM FIELDS */
        $where = array(
            "fieldtype" => array("sqltype" => "LIKE", "value" => "tickbox"),
            "showorder" => array("sqltype" => "LIKE", "value" => "on")
        );
        $result = select_query("tblcustomfields", "id,fieldname", $where);
        $wantsms = '';
        while ($data = mysql_fetch_array($result)) {
            if ($data['id'] == $settings['wantsmsfield']) {
                $selected = 'selected="selected"';
            } else {
                $selected = "";
            }
            $wantsms .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['fieldname'] . '</option>';
        }

        $where = array(
            "fieldtype" => array("sqltype" => "LIKE", "value" => "text"),
            "showorder" => array("sqltype" => "LIKE", "value" => "on")
        );
        $result = select_query("tblcustomfields", "id,fieldname", $where);
        $gsmnumber = '';
        while ($data = mysql_fetch_array($result)) {
            if ($data['id'] == $settings['gsmnumberfield']) {
                $selected = 'selected="selected"';
            } else {
                $selected = "";
            }
            $gsmnumber .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['fieldname'] . '</option>';
        }
        /* CUSTOM FIELDS */


        echo '
        <script type="text/javascript">
            $(document).ready(function(){
                $("#api").change(function(){
                    $("#form").submit();
                });
            });
        </script>
        <form action="" method="post" id="form">
        <input type="hidden" name="action" value="save" />
            <div >
                <table class="form datatable" width="100%" border="0" cellspacing="2" cellpadding="3">
                    <tbody>

                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['senderid'].'</td>
                            <td class="fieldarea"><input type="text" name="params[senderid]" size="40" value="' . $apiparams->senderid . '"> </td>
                        </tr>

                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['iletimerkezi_username'].'</td>
                            <td class="fieldarea"><input type="text" name="params[iletimerkezi_username]" size="40" value="' . $apiparams->iletimerkezi_username . '"> '.$LANG['iletimerkezi_username_desc'].'</td>
                        </tr>

                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['iletimerkezi_password'].'</td>
                            <td class="fieldarea"><input type="password" name="params[iletimerkezi_password]" size="40" value="' . $apiparams->iletimerkezi_password . '"> '.$LANG['iletimerkezi_password_desc'].'</td>
                        </tr>

                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['wantsmsfield'].'</td>
                            <td class="fieldarea">
                                <select name="wantsmsfield">
                                    ' . $wantsms . '
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['gsmnumberfield'].'</td>
                            <td class="fieldarea">
                                <select name="gsmnumberfield">
                                    ' . $gsmnumber . '
                                </select>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <p align="center"><input type="submit" value="'.$LANG['save'].'" class="btn-success" /></p>
            <p align="right">İletimerkezi SMS 1.0.2</p>
        </form>
        ';
    }
    elseif ($tab == "templates")
    {
        if ($_POST['submit']) {
            $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
            $result = select_query("mod_iletimerkezi_templates", "*", $where);
            while ($data = mysql_fetch_array($result)) {
                if ($_POST[$data['id'] . '_active'] == "on") {
                    $tmp_active = 1;
                } else {
                    $tmp_active = 0;
                }
                $update = array(
                    "template" => $_POST[$data['id'] . '_template'],
                    "active" => $tmp_active
                );

                if(isset($_POST[$data['id'] . '_extra'])){
                    $update['extra']= trim($_POST[$data['id'] . '_extra']);
                }
                if(isset($_POST[$data['id'] . '_admingsm'])){
                    $update['admingsm']= $_POST[$data['id'] . '_admingsm'];
                    $update['admingsm'] = str_replace(" ","",$update['admingsm']);
                }
                update_query("mod_iletimerkezi_templates", $update, "id = " . $data['id']);
            }
        }

        echo '<form action="" method="post">
        <input type="hidden" name="action" value="save" />
            <div >
                <table class="datatable form" width="100%" border="0" cellspacing="2" cellpadding="3">
                    <thead>
                        <tr>
                            <th class="fieldlabel" width="30%">Açıklama</th>
                            <th>Şablon</th>
                            <th>Durum</th>
                            <th>Değişkenler</th>';
                            if($_GET['type'] == "admin") {
                                echo '<th>Yönetici GSM</th>';
                            }
                    echo '</tr>
                    </thead>
                    <tbody>
                    ';

                    $where  = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
                    $result = select_query("mod_iletimerkezi_templates", "*", $where);

                    while ($data = mysql_fetch_array($result)) {
                        if ($data['active'] == 1) {
                            $active = 'checked = "checked"';
                        } else {
                            $active = '';
                        }

                        $desc = json_decode($data['description']);
                        if(isset($desc->$LANG['lang'])){
                            $name = $desc->$LANG['lang'];
                        }else{
                            $name = $data['name'];
                        }

                        echo '
                            <tr>
                                <td class="fieldlabel" width="30%">' . $name . '</td>
                                <td>';

                                if(!empty($data['extra'])){
                                    echo '<div style="margin-left:2px;text-align:left;"><input placeholder="Gün sayısını yazınız" type="text" name="'.$data['id'].'_extra" value="'.$data['extra'].'"></div>';
                                }

                                echo '
                                    <textarea style="padding:5px;" rows="5" cols="50" name="' . $data['id'] . '_template">' . $data['template'] . '</textarea>
                                </td>
                                <td><input type="checkbox" value="on" name="' . $data['id'] . '_active" ' . $active . '></td>
                                <td>' . $data['variables'] . '</td>';

                                if($_GET['type'] == "admin"){
                                    echo '
                                        <td>
                                            <input style="width:80%;" type="text" name="'.$data['id'].'_admingsm" value="'.$data['admingsm'].'">
                                            '.$LANG['admingsmornek'].'
                                        </td>
                                    ';
                                }

                        echo '</tr>';
                    }
                    echo '
                    </tbody>
                </table>
            </div>
            <p align="center"><input type="submit" name="submit" value="Kaydet" class="btn-success" /></p>
        </form>';

    }
    elseif ($tab == "messages")
    {
        if(!empty($_GET['deletesms'])){
            $smsid = (int) $_GET['deletesms'];
            $sql = "DELETE FROM mod_iletimerkezi_messages WHERE id = '$smsid'";
            mysql_query($sql);
        }

        echo  '
        <!--<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" type="text/css">
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css" type="text/css">
        <script type="text/javascript">
            $(document).ready(function(){
                $(".datatable").dataTable();
            });
        </script>-->

        <div>';

        if(isset($_GET['send_details'])) {
            $sql     = "SELECT `logs` FROM `mod_iletimerkezi_messages` WHERE id='".$_GET['send_details']."'";
            $result  = mysql_query($sql);
            $details = mysql_fetch_row($result);
            echo $details[0];
            // die('Details');
        }

        echo '
        <table class="datatable" border="0" cellspacing="1" cellpadding="3" style="width:100%" >
        <thead>
            <tr>
                <th>#</th>
                <th>'.$LANG['client'].'</th>
                <th>'.$LANG['gsmnumber'].'</th>
                <th>'.$LANG['message'].'</th>
                <th>'.$LANG['datetime'].'</th>
                <th>'.$LANG['status'].'</th>
                <th width="60"></th>
            </tr>
        </thead>
        <tbody>
        ';


        $sql = "SELECT `m`.*,`user`.`firstname`,`user`.`lastname` FROM `mod_iletimerkezi_messages` as `m` JOIN `tblclients` as `user` ON `m`.`user` = `user`.`id` ORDER BY `m`.`datetime` DESC";
        $result = mysql_query($sql);
        $i = 0;
        while ($data = mysql_fetch_array($result)) {

            if($data['msgid'] && $data['status'] == "") {
                $status = $class->getReport($data['msgid']);
                mysql_query("UPDATE mod_iletimerkezi_messages SET status = '$status' WHERE id = ".$data['id']."");
            } else {
                $status = $data['status'];
            }

            $i++;

            echo  '<tr>
            <td>'.$i.'</td>
            <td><a href="clientssummary.php?userid='.$data['user'].'">'.$data['firstname'].' '.$data['lastname'].'</a></td>
            <td>'.$data['to'].'</td>
            <td>'.$data['text'].'</td>
            <td>'.$data['datetime'].'</td>
            <td>'.$LANG[$status].'</td>
            <td>
                <a style="text-decoration: none;" href="addonmodules.php?module=iletimerkezi&tab=messages&deletesms='.$data['id'].'" title="'.$LANG['delete'].'">
                    <img src="images/delete.gif" width="16" height="16" border="0" alt="Sil">
                </a>

                &bull;

                <a style="text-decoration: none;" href="addonmodules.php?module=iletimerkezi&tab=messages&send_details='.$data['id'].'" title="'.$LANG['delete'].'">
                    <img src="images/info.gif" width="16" height="16" border="0" alt="Detaylar">
                </a>
            </td>
            </tr>';

        }

        echo '
        </tbody>
        </table>
        </div>
        ';
    }
    elseif($tab=="sendcustomer")
    {
        $settings = $class->getSettings();
        $sms_response = '';
        if(!empty($_POST['client'])){
            $userinf   = explode("_",$_POST['client']);
            $userid    = $userinf[0];
            $gsmnumber = $userinf[1];

            $val = array('{firstname}', '{lastname}');
            $change = array($userinf[2], $userinf[3]);
            $message = str_replace($val, $change, $_POST['message']);

            $class->setGsmnumber($gsmnumber);
            $class->setMessage($message);
            $class->setUserid($userid);

            $result = $class->send();
            if($result == false){
                $sms_response = $class->getErrors();
            }else{
                $sms_response = $LANG['smssent'].' '.$gsmnumber;
            }

            if($_POST["debug"] == "ON"){
                $debug = 1;
            }
        }

        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `b`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `c`.`value` = 'on'";
        $clients = '';
        $result = mysql_query($userSql);
        while ($data = mysql_fetch_array($result)) {
            $clients .= '<option value="'.$data['id'].'_'.$data['gsmnumber'].'_'.$data['firstname'].'_'.$data['lastname'].'">'.$data['firstname'].' '.$data['lastname'].' (#'.$data['id'].')</option>';
        }

        echo '<form action="" method="post">
        <input type="hidden" name="action" value="save" />
            <div >
            ';

            if(!empty($sms_response)) {
                echo $sms_response;
            }

            echo '
                <table class="datatable form" width="100%" border="0" cellspacing="2" cellpadding="3">
                    <tbody>
                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['client'].'</td>
                            <td class="fieldarea">
                                <select name="client">
                                    <option value="">'.$LANG['selectcustomer'].'</option>
                                    ' . $clients . '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['message'].'</td>
                            <td class="fieldarea">
                                <textarea rows="10" cols="50" name="message"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p align="center"><input type="submit" value="'.$LANG['send'].'" class="btn-success" /></p>
        </form>';
    }
    elseif($tab=="sendbulk")
    {  //die("send bulk");
        $settings = $class->getSettings();
        $sms_response = '';
        if(!empty($_POST['client'])){
            //die(var_export($_POST));

            if($_POST['client']==-1){

                $userSql = "SELECT `id`, `firstname`, `lastname`, `phonenumber`
                FROM `tblclients`";

                $result = mysql_query($userSql);

                while ($data = mysql_fetch_array($result)) {
                    $val = array('{firstname}', '{lastname}');
                    $change = array($data['firstname'], $data['lastname']);
                    $message = str_replace($val, $change, $_POST['message']);

                    $class2  = new iletimerkezi();
                    $class2->setGsmnumber($data['phonenumber']);
                    $class2->setMessage($message);
                    $class2->setUserid($data['id']);

                    $res = $class2->send();

                    if($res == false){
                        $sms_response = $class2->getErrors();
                    }else{
                        $sms_response = $LANG['smssent'].' '.$gsmnumber;
                    }

                    if($_POST["debug"] == "ON"){
                        $debug = 1;
                    }
                    unset($class2);
                }
            } else { //die("sdsad");
                $userSql = "SELECT `id`, `firstname`, `lastname`, `phonenumber`
                FROM `tblclients` WHERE groupid='".$_POST['client']."'";

                //die(var_export($data));
                $clients = '';
                $result = mysql_query($userSql);
                while ($data = mysql_fetch_array($result)) {

                    $val = array('{firstname}', '{lastname}');
                    $change = array($data['firstname'], $data['lastname']);
                    $message = str_replace($val, $change, $_POST['message']);

                    $class2  = new iletimerkezi();
                    $class2->setGsmnumber($data['phonenumber']);
                    $class2->setMessage($message);
                    $class2->setUserid($data['id']);

                    $res2 = $class2->send();
                    if($res2 == false){
                        $sms_response = $class2->getErrors();
                    }else{
                        $sms_response = $LANG['smssent'].' '.$gsmnumber;
                    }

                    if($_POST["debug"] == "ON"){
                        $debug = 1;
                    }
                    unset($class2);
                }
            }

        }
        /*
        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `b`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `c`.`value` = 'on'";
        $clients = '';
        $result = mysql_query($userSql);
        while ($data = mysql_fetch_array($result)) {
            $clients .= '<option value="'.$data['id'].'_'.$data['gsmnumber'].'">'.$data['firstname'].' '.$data['lastname'].' (#'.$data['id'].')</option>';
        }*/

        /* part of client groups */
        $command = "getclientgroups";
        $adminuser = "admin";
        $results = localAPI($command,$values,$adminuser);
        //die("<pre>".var_export($results,1)."</pre>");
        $clients = '';
        foreach ($results['groups']['group'] as $client) {
            $clients .= '<option value="'.$client['id'].'">'.$client['groupname'].'</option>';
        }
        //die(var_export($clients));
        echo '<form action="" method="post">
        <input type="hidden" name="action" value="save" />
            <div >
            ';

            if(!empty($sms_response)) {
                echo $sms_response.$i;
            }

            echo '
                <table class="datatable form" width="100%" border="0" cellspacing="2" cellpadding="3">
                    <tbody>
                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['client'].'</td>
                            <td class="fieldarea">
                                <select name="client">
                                    <option value="-1">'.$LANG['selectclient'].'</option>
                                    ' . $clients . '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['message'].'</td>
                            <td class="fieldarea">
                                <textarea rows="10" cols="50" name="message"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p align="center"><input type="submit" value="'.$LANG['send'].'" class="btn-success" /></p>
        </form>';

    } elseif($tab == "notifications") {
        $settings = $class->getSettings();
        echo '<div >';
        echo file_get_contents('https://dev.iletimerkezi.com/programs/whmcs/info.php?version='.urlencode($settings['version']));
        echo '</div>';
    }

    echo '</div>';
}
