<?php

class GuiControlLabel extends AbstractGuiControl
{

    public $text;

    function __construct($text, $vgap = 0, $title=null)
    {
        parent::__construct('', $title, $vgap);
        $this->text = $text;
    }


    public function getControlType(){
        return GUI_CONTROL_LABEL;
    }

    public function getSpecificDef(){
        return array
                (
                    GuiLabelDef::caption => $this->text
                );
    }
}

?>