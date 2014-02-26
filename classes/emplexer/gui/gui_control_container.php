<?php

class GuiControlContainer implements Countable
{
    private $controls;
    function __construct()
    {
        $this->controls = array();
    }

    public function addControl(AbstractGuiControl $control){
        $this->controls[] = $control;
    }

    public function generate(){
        $controls =  array();
        foreach ($this->controls as $control) {
            foreach ($control->generate() as $c) {
                $controls[] = $c;
            }

        }
        return $controls;
    }

    public function count()
    {
        return count($this->controls);
    }
}

?>