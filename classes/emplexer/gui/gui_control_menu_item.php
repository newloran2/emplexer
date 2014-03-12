<?php

class GuiControlMenuItem
{
    public $caption;
    private $action;
    function __construct($caption, $action)
    {
        $this->caption = $caption;
        $this->action = $action;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function generate(){
    //     const /* MY_Bool                          */ is_separator                     = 'is_separator';
    // const /* (char *)                         */ caption                          = 'caption';
    // const /* (char *)                         */ icon_url                         = 'icon_url';
    // const /* (GuiAction *)                    */ action                           = 'action';
    //
        return array(
            GuiMenuItemDef::caption => $this->caption,
            GuiMenuItemDef::action  => $this->action,
            GuiMenuItemDef::icon_url => null,
            GuiMenuItemDef::is_separator => false
        );
    }
}

?>
