<?php

class Emarka_Storesms_Helper_Xml extends Mage_Core_Helper_Abstract
{

    public function getStatusesFromXml($xml) {

        $xmlResource = xml_parser_create();
        xml_parse_into_struct($xmlResource, $xml, $vals, $index);
        xml_parser_free($xmlResource);

        $attributes = array();


        foreach ($index['MESSAGE'] as $key) {
            $attributes[] = $vals[$key]['attributes'];
        }

        return $attributes;

    }



    public function getStatusCode($xml) {

        $vals = $index = array();
        $xmlResource = xml_parser_create();
        xml_parse_into_struct($xmlResource, $xml, $vals, $index);
        xml_parser_free($xmlResource);

        return $vals[$index['ID'][0]]['value'];


    }



}