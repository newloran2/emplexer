<?php

interface TemplateCallbackInterface{

    public function getField($name, $item);
    public function getData();
    public function getMediaUrl($data);

}

?>
