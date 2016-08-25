<?php
$hook = array(
    'hook'           => 'TicketOpen',
    'function'       => 'emarka_TicketOpen_admin',
    'description'    => array(
            'turkish'        => 'Bir ticket açıldığında mesaj gönderir',
            'english'        => 'Bir ticket açıldığında mesaj gönderir'
    ),
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'Yeni bir ticket acildi. ({subject})',
    'variables'      => '{subject}'
);

if(!function_exists('TicketOpen_admin')) {

    function emarka_TicketOpen_admin($args){

        // die('ARGS | '.var_export($args,1));

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
        $replaceto = array($args['subject']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $class->setGsmnumber(trim($gsm));
                $class->setUserid(0);
                $class->setMessage($message);
                $class->send();
            }
        }
    }
    
}

return $hook;
