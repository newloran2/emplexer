<?php

interface TemplateCallbackInterface{

    public function getField($name, $item,$json);
    public function getData();
    public function getMediaUrl($data);


}

?>