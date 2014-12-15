<?php
class BaseConfigScreen extends GuiControlContainer implements ScreenInterface

{
    public function __construct()
    {
    }

    public function generateScreen(){
        $folder_view = array(
            PluginControlsFolderView::defs            => $this->generate(),
            PluginControlsFolderView::initial_sel_ndx => -1,
            PluginControlsFolderView::params          => null
        );

        return array(
            PluginFolderView::multiple_views_supported => false,
            PluginFolderView::archive                  => null,
            PluginFolderView::view_kind                => PLUGIN_FOLDER_VIEW_CONTROLS,
            PluginFolderView::data                     => $folder_view
        );
    }

}
