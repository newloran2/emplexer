<?php

/**
*
*/
class NfsScreen  implements ScreenInterface
{

    private $ip;
    private $nfs;
    private $path;
    function __construct($ip, $path = null) {
        $this->nfs = new NFS($ip);
        $this->path = $path;
    }

    public function generateScreen(){
        $itens = array();
        $folderItems = array();

        foreach ($this->nfs->getAllNfsPaths() as $key => $value) {
             $folderItems[] = array(
                PluginRegularFolderItem::media_url          => "nfsSetup_$value",
                PluginRegularFolderItem::caption            => $key ,
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