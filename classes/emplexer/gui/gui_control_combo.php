<?php

class GuiControlCombo extends AbstractGuiControl{

    private $action;
    private $with;
    private $itens;
    private $initialValue;

    public function __construct($name, $initialValue, array $items = null, $width = -1, $action=null, $vgap =0){
        parent::__construct($name, null, $vgap);
        $this->action       = $action;
        $this->width        = $with;
        $this->initialValue = $initialValue;
        $this->itens        = array();
        if (isset($items)){
            foreach ($items as $key => $value) {
                $this->itens[$key] = $value;
            }
        }
    }
}

