<?php

abstract class GcompAbstractGuiControl{

    protected $id;
    protected $role;
    protected $width;
    protected $height;
    protected $x;
    protected $y;

    
    
    public function __construct($x, $y, $w=-1, $h=-1, $role="unset"){
        $this->x     = $x;
        $this->y     = $y;
        $this->w     = $w;
        $this->h     = $h;
        $this->$role = $role;
    }

    public abstract function getControlType();
    public abstract function getSpecificDef();

    public function generate(){
        $ret[] = array(
            "geom_def"=> array(
                "role" => $this->role,
                "w"    => $this->w,
                "h"    => $this->h,
                "align_def"=>array(
                    "x" => $this->x,
                    "y" => y
                )
            ),
            "kind" => $this->getControlType(),
            "specific_def"=> $this->getSpecificDef()
        );
        return $ret;
    }
}
