<?php

// class GuiTextFieldDef
// {
//     const /* (char *)                         */ initial_value                    = 'initial_value';
//     const /* MY_Bool                          */ numeric                          = 'numeric'; // default fals e
//     const /* MY_Bool                          */ password                         = 'password'; // default false
//     const /* MY_Bool                          */ has_osk                          = 'has_osk'; //default false
//     const /* MY_Bool                          */ always_active                    = 'always_active'; //default false
//     const /* int                              */ width                            = 'width'; default -1
//     const /* (GuiAction *)                    */ apply_action                     = 'apply_action'; //default null
//     const /* (GuiAction *)                    */ confirm_action                   = 'confirm_action'; //default null
// }

class GuiControlText extends AbstractGuiControl
{

    private $initialValue;
    private $action;

    function __construct($name, $title, $initialValue = "", $width = -1,  $action=null)
    {
        parent::__construct($name, $title);
        $this->initialValue = $initialValue;
        $this->action =  $action;
        $this->width = $width;
        return $this;
    }


    public function getControlType(){
        return GUI_CONTROL_TEXT_FIELD;
    }

    public function getSpecificDef(){
        return array
                (
                    GuiTextFieldDef::initial_value => $this->initialValue,
                    GuiTextFieldDef::width => $this->width,
                    GuiTextFieldDef::numeric => 0 ,
                    GuiTextFieldDef::password =>  0,
                    GuiTextFieldDef::has_osk =>  0,
                    GuiTextFieldDef::always_active=>  0,
                    GuiTextFieldDef::apply_action =>  null,
                    GuiTextFieldDef::confirm_action =>  null,
                );
    }

    public function setAction($action){
        $this->action = $action;
        return $this;
    }
}

?>