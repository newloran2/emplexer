<?php

class GuiControlButton extends AbstractGuiControl
{

    private $caption;
    private $width ;
    private $action;

    function __construct($name, $caption, $width = 200, $action=null, $vgap=0)
    {
        parent::__construct($name, null, $vgap);
        $this->caption = $caption;
        $this->action =  $action;
        $this->width = $width;
        return $this;
    }


    public function getControlType(){
        return GUI_CONTROL_BUTTON;
    }

    public function getSpecificDef(){
        return array
                (
                    GuiButtonDef::caption => $this->caption,
                    GuiButtonDef::width => $this->width,
                    GuiButtonDef::push_action => $this->action,
                );
    }

    public function setAction($action){
        $this->action = $action;
        return $this;
    }
}

?>