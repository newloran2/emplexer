<?php

class GuiControlContainer implements Countable
{
    private $controls;
    function __construct()
    {
        $this->controls = array();
    }

    public function addControl(GeneratableInterface $control){
        
        // hd_print_r('adicionando control', $control);
        $this->controls[] = $control;
    }

    public function generate(){
        $controls =  array();
        // hd_print_r('controls', $this->controls);
        foreach ($this->controls as $control) {
            // foreach ($control->generate() as $c) {
                // $controls[] = $c;
            $controls[] = $control->generate();
            // }

        }
        return $controls;
    }

    public function count()
    {
        return count($this->controls);
    }
}

?>
