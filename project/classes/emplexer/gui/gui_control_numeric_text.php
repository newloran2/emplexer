<?php

class GuiControlNumericText extends GuiControlText
{
    public function getSpecificDef(){
        $specificDef = parent::getSpecificDef();
        $specificDef['numeric'] = 1;

        return $specificDef;
    }
}
?>