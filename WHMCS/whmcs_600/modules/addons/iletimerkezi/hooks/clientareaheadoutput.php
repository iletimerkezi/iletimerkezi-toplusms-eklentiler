<?php
//ClientAreaHeadOutput
$hook = array(
    'hook' => 'ClientAreaHeadOutput',
    'function' => 'emarka_ClientAreaHeadOutput',
);
if(!function_exists('ClientAreaHeadOutput')){
    function emarka_ClientAreaHeadOutput($args){
        $class = new iletimerkezi();
        $settings = $class->getSettings();
        $field = $settings['wantsmsfield'];

        $html = '<script type="text/javascript">
        var field = document.getElementById("customfield'.$field.'");
        field.checked = true;
        var elem = document.getElementById("customfield1");
        elem.value = "My default value";
        </script>';

        return $html;
    }
}

return $hook;