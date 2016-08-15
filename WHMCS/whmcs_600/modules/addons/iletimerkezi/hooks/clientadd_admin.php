<?php
$hook = array(
    'hook' => 'ClientAdd',
    'function' => 'emarka_ClientAdd_admin',
    'description' => array(
        'turkish' => 'Müşteri kayıt olduktan sonra mesaj gönderir',
        'english' => 'Müşteri kayıt olduktan sonra mesaj gönderir'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'Sitenize yeni musteri kayit oldu.',
    'variables' => ''
);
if(!function_exists('ClientAdd_admin')){
    function emarka_ClientAdd_admin($args){
        $class = new iletimerkezi();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
        $admingsm = explode(",",$template['admingsm']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $class->sender = $settings['api'];
                $class->params = $settings['apiparams'];
                $class->gsmnumber = trim($gsm);
                $class->message = $template['template'];
                $class->userid = 0;
                $class->send();
            }
        }
    }
}
return $hook;