<?php
$hook = array(
    'hook' => 'AfterRegistrarRegistrationFailed',
    'function' => 'emarka_AfterRegistrarRegistrationFailed_admin',
    'description' => array(
        'turkish' => 'Domain kayıdı başarısız olduğunda gerçekleştiğinde sms gönderir',
        'english' => 'Domain kayıdı başarısız olduğunda gerçekleştiğinde sms gönderir'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'Domain kayit edilirken hata olustu. {domain}',
    'variables' => '{domain}'
);
if(!function_exists('AfterRegistrarRegistrationFailed_admin')){
    function emarka_AfterRegistrarRegistrationFailed_admin($args){
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

        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $class->setGsmnumber( trim($gsm));
                $class->setUserid(0);
                $class->setMessage($message);
                $class->send();
            }
        }
    }
}

return $hook;
