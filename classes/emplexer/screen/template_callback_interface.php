<?php

interface TemplateCallbackInterface{

    public function getField($name, $data);
    public function getData();
    public function getMediaUrl($data);


}

?>