<?php

class GcompControlContainer extends GuiControlContainer implements Countable
{

    public function count(){
        return parent::count();
    }

    public function addControl(AbstractGuiControl $control){
        $this->controls[] = $control;
    }
}

