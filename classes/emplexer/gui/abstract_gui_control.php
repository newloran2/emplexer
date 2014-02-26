<?php

abstract class AbstractGuiControl
{
    protected $name;
    protected $title;
    protected $vgap;
    function __construct($name, $title, $vgap=0)
    {
        $this->name = $name;
        $this->title= $title;
        $this->vgap = $vgap;
    }

    public abstract function getControlType();
    public abstract function getSpecificDef();

    public function generate()
    {
        $ret= array();
        if ($this->vgap != 0){
            $ret[] = array(
                GuiControlDef::name => 'vgap',
                GuiControlDef::title => null,
                GuiControlDef::kind => GUI_CONTROL_VGAP,
                GuiControlDef::specific_def => array(
                    GuiVGapDef::vgap => $this->vgap
                ),
            );
        }

        $ret[] =array
        (
            GuiControlDef::name => $this->name,
            GuiControlDef::title => $this->title,
            GuiControlDef::kind => $this->getControlType(),
            GuiControlDef::specific_def => $this->getSpecificDef(),
        );

        return $ret;
    }
}
?>