<?php

class GuiControlLabel extends AbstractGuiControl
{

    public $text;

    function __construct($title, $text)
    {
        parent::__construct('', $title);
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