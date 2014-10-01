<?php

/**
 * Class GcompControlImage
 * @author Newloran2
 */
class GcompControlImage extends GcompAbstractGuiControl
{
    protected $url;
    public function __construct($representation)
    {
        parent::__construct($representation);
        $this->url = $representation['url'];
    }

    public static function initWithRepresentation($representation){
        if (!GcompControlImage::isValid($representation)) return null;
        $ret = new GcompControlImage($representation);
        return $ret;

    } 
    public static function isValid($representation){
        if (!GcompAbstractGuiControl::isValid($representation)) return false;
        if (isset($representation['url'])) {
            return true;
        }
        hd_print('representação de imagem inválida');
        return false;
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

