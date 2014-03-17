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

    public function getControlType() {
        return GUI_CONTROL_COMBOBOX;
    }

    public function getSpecificDef(){
        return array(
            GuiComboboxDef::initial_value       => $this->initialValue,
            GuiComboboxDef::value_caption_pairs => $this->itens,
            GuiComboboxDef::width               => $this->width,
            GuiComboboxDef::apply_action        => null,
            GuiComboboxDef::confirm_action      => $this->action
        );
    }
}

