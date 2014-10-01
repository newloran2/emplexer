<?php

class BaseGcompsScreen implements ScreenInterface {
    protected $window;
    
    function __construct($backgroundUrl=null)
    {
        $w = new GcompTemplateManager();
        $this->window= $w->generate();
    }


    public function generateScreen() {
        return $this->window->generate();
    }
}
