<?php

/**
 * Class GcompControlImage
 * @author Newloran2
 */
class GcompControlImage
{
    protected $url;
    public function __construct($url)
    {
        $this->url = $url; 
    }


    public function getControlType(){
       return "image";
    }

    public function getSpecificDef(){
        return array(
            "url" => $this->url
        );
    }
}

