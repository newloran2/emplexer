<?php

class GuiControlMenuItemSeparator extends GuiControlMenuItem
{
    function __construct()
    {
        parent::__construct(null, null);
    }

    public function generate(){
        $data = parent::generate();
        $data[GuiMenuItemDef::is_separator] = true;

        return $data;
    }
}

?>