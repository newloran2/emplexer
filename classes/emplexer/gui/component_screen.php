<?php


class ComponentScreen {
    
    private $components;
    private $async_icon_loading = false;
    private $king = PLUGIN_FOLDER_VIEW_REGULAR;
    private $templateName= 'base';

    function __construct($templaName){
        $this->components = array();
        $this->templateName =  $templateName;
    }

    public function addComponent($component){
        $this->components[] = $component;
    }



}
