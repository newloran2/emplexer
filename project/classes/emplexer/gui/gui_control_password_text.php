<?php

class GuiControlPasswordText extends GuiControlText
{
    public function getSpecificDef(){
        $specificDef = parent::getSpecificDef();
        $specificDef['password'] = 1;

        return $specificDef;
    }
}
?>