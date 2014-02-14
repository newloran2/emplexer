<?php

/**
*
*/
class NfsScreen  implements ScreenInterface
{

    private $ip;
    private $nfs;
    function __construct($ip) {
        $this->nfs = new NFS($ip);
        $this->nfs->mountAll();
    }

    public function generateScreen(){
        $itens = array();
        $folderItems = array();
        $a =  $this->nfs->getIteratorForNfsPath('nfs://192.168.2.9:/volume1/Series');
        $a->mount();
        // var_dump($a);
        foreach ( $a as $key=>$value) {
            // var_dump($key);
            // var_dump($value);
             $folderItems[] = array(
                PluginRegularFolderItem::media_url          => "$key",
                PluginRegularFolderItem::caption            => basename($key) ,
                PluginRegularFolderItem::view_item_params   => array()
            );
        }



        // var_dump($folderItems);

        $availableTemplates = array(
            PluginRegularFolderView::async_icon_loading             => true,
            PluginRegularFolderView::initial_range                  =>
            array(
                PluginRegularFolderRange::items                         =>  $folderItems,
                PluginRegularFolderRange::total                         =>  count($folderItems),
                PluginRegularFolderRange::count                         =>  count($folderItems),
                PluginRegularFolderRange::more_items_available          =>  false,
                PluginRegularFolderRange::from_ndx                      =>  0
                ),
            PluginRegularFolderView::view_params                    => array (
                ViewParams::num_cols  => 1,
                ViewParams::num_rows  => 10
            ),

            PluginRegularFolderView::base_view_item_params          => array(
            ),
            PluginRegularFolderView::not_loaded_view_item_params    => array(),
            PluginRegularFolderView::actions => array(
                GUI_EVENT_KEY_ENTER => array(
                    GuiAction::handler_string_id => PLUGIN_OPEN_FOLDER_ACTION_ID)
            )
        );

        $a = array(
            PluginFolderView::view_kind                             =>  PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::data                                  => $availableTemplates
        );
        return $a;
    }
}

?>