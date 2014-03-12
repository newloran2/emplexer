<?php


class PopupMenu
{


    private $itens;

    function __construct(){
        $this->itens = array();
    }

    public function addItem($item){
        foreach($this->itens as $_item){

            hd_print($_item['caption']);
            hd_print($item->caption);
            if ($_item['caption'] == $item->caption) return;
        }
        $this->itens[] = $item->generate();
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
             GuiAction::handler_string_id => SHOW_POPUP_MENU_ACTION_ID,
             GuiAction::data =>
             array
             (
              ShowPopupMenuActionData::menu_items => $this->itens,
              ShowPopupMenuActionData::selected_menu_item_index => 0,
             ),
            );
    }

}

?>
