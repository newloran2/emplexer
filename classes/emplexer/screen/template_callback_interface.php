<?php

interface TemplateCallbackInterface{

    public function getField($name, $item, $field=null);
    public function getData();
    public function getMediaUrl($data);

}

?>
