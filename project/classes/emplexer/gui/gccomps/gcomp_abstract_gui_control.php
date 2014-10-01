<?php

abstract class GcompAbstractGuiControl implements GeneratableInterface{

    protected $id;
    protected $role;
    protected $w;
    protected $h;
    protected $x;
    protected $y;
    
    // public function __construct($representation, $x=0, $y=0, $w=-1, $h=-1, $role="unset"){
    public function __construct($representation){
        $this->x     = $representation['x'];
        $this->y     = $representation['y'];
        $this->w     = $representation['w'];
        $this->h     = $representation['h'];
        // $this->role = $representation['role'];
        $this->role ="unset";
    }

    public static function isValid($representation){
        if (validadeArrayTypes(array('x'=> "integer", "y"=> "integer", 'h'=> "integer", 'w'=> "integer"), $representation)) return true;
        return false;
    }

    public static function initWithRepresentation($representation){
        if (!GcompAbstractGuiControl::isValid($representation)) return null;
        $ret = new GcompAbstractGuiControl($representation);
        return $ret;
    } 

    public abstract function getControlType();
    public abstract function getSpecificDef();

    public function generate(){
        $ret = array(
            "geom_def"      => array(
                "role"      => $this->role,
                "w"         => $this->w,
                "h"         => $this->h,
                "align_def" => array(
                    "x"     => $this->x,
                    "y"     => $this->y
                )
            ),
            "kind"         => $this->getControlType(),
            "specific_def" => $this->getSpecificDef()
        );
        
        return $ret;
    }
}
