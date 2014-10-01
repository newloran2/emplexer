<?php


class Modal
{

    private $title;
    private $width;
    private $components;

    function __construct($title, GuiControlContainer $container = null, $width = -1){
        $this->title =  $title;
        $this->width = $width;
        $this->components = isset($container)? $container : new GuiControlContainer();
    }

    public function addControl(AbstractGuiControl $control){
        $this->components->addControl($control);
    }

    /**
     * Force the Dune to show my modal with a exception.
     * This behavior are very bad, but are the only way to respond a modal to get_folder_view
     * @return A dune exception with the configured modal.
     */
    public function show(){
        throw new DuneException('ignore this exception',0,$this->generate());
    }
    public function generate(){
        return array
        (
            GuiAction::handler_string_id => SHOW_DIALOG_ACTION_ID,
            GuiAction::caption => null,
            GuiAction::data =>
                array
                (
                    ShowDialogActionData::title => $this->title,
                    ShowDialogActionData::defs => $this->components->generate(),
                    ShowDialogActionData::close_by_return => true,
                    ShowDialogActionData::preferred_width => $this->width,
                ),
            GuiAction::params => null,
        );
    }

}

?>