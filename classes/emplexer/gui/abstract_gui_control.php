<?php

abstract class AbstractGuiControl
{
    protected $name;
    protected $title;
    function __construct($name, $title)
    {
        $this->name = $name;
        $this->title= $title;
    }

    public abstract function getControlType();
    public abstract function getSpecificDef();

    public function generate()
    {
        return array
        (
            GuiControlDef::name => $this->name,
            GuiControlDef::title => $this->title,
            GuiControlDef::kind => $this->getControlType(),
            GuiControlDef::specific_def => $this->getSpecificDef(),
        );
    }
}
?>