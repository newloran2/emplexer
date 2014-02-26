<?php

/**
*
*/
class NfsScreen  implements ScreenInterface
{

    private $path;
    private $decomposedPath;
    private $nfs;
    private $serverIps;
    function __construct($path=null) {
        $ipsString = Config::getInstance()->nfsIps;
        if ($ipsString){
            $this->serverIps =  json_decode(urldecode($ipsString));
        }

        $this->path = $path;
        $this->decomposedPath = parse_url($this->path);
        // var_dump($this->path);
        // var_dump($this->decomposedPath);
        if ($path){
            $this->nfs = new NFS($path);
            // $this->nfs->mountAll();
        };
        // var_dump($this->nfs);
    }

    public static function saveNfsIp($user_input){
        hd_print(__METHOD__);
        if (!filter_var($user_input->nfsIp, FILTER_VALIDATE_IP)){
            $b = new Modal(_("NFS server ip"), null ,-1);
            $b->addControl(new GuiControlLabel(_('Please enter a valid ip.')));
            $b->addControl(new GuiControlText("nfsIp", null, $user_input->nfsIp, 400));
            $b->addControl(new GuiControlButton(null, _("Save"), 100, Actions::closeAndRunThisStaticMethod('NfsScreen::saveNfsIp')), 10);

            $b->show();
        }

        if (!NFS::isANFSServer($user_input->nfsIp)){
            $b = new Modal(_("NFS server ip"));

            $b->addControl(new GuiControlLabel(_('This ip does not have an active nfs server.'),-25));
            $b->addControl(new GuiControlLabel(_('Please check and try again.'), -25));
            $b->addControl(new GuiControlText("nfsIp", null, $user_input->nfsIp, 400));
            $b->addControl(new GuiControlButton(null, _("Save"), 100, Actions::closeAndRunThisStaticMethod('NfsScreen::saveNfsIp'), 10));
            $b->show();
        }

        $nfsIps = Config::getInstance()->nfsIps;
        if (!isset($nfsIps)) {
            $nfsIps =array($user_input->nfsIp);
        } else {
            $nfsIps = explode(',', $nfsIps);
            $nfsIps[] = $user_input->nfsIp;
        }

        Config::getInstance()->nfsIps = implode(',', $nfsIps);


        return ActionFactory::open_folder("nfs");
    }

    public function generateScreen(){
        hd_print(__METHOD__ . ': ' . $this->path);
        // var_dump($this->decomposedPath);
        // var_dump(isset($this->decomposedPath['scheme']));
        // var_dump(isset($this->decomposedPath['path']));
        if (!isset($this->decomposedPath['path'])){
            return $this->showMountPoints();
        } else if (isset($this->decomposedPath['scheme']) && isset($this->decomposedPath['host'])){
            return $this->showMountedPath();
        } else {
            // $popup = new PopupMenu();
            // $popup->addItem(new GuiControlMenuItem('main', ActionFactory::open_folder('main')));
            // $popup->addItem(new GuiControlMenuItem('http://192.168.2.8:32400/library/sections/7/all', ActionFactory::open_folder('http://192.168.2.8:32400/library/sections/7/all')));
            // $popup->addItem(new GuiControlMenuItemSeparator());
            // $popup->addItem(new GuiControlMenuItem('refresh main', ActionFactory::invalidate_folders(array('main'))));

            // $popup->show();
            return $this->listIps();
        }
    }

    private function listIps(){
        $ips =  Config::getInstance()->nfsIps;
        if (!isset($ips)){
            $b = new Modal(_("NFS server ip"));
            $b->addControl(new GuiControlText("nfsIp", null, "192.168.2.9", 400));
            $b->addControl(new GuiControlButton(null, _("Save"), 100, Actions::closeAndRunThisStaticMethod('NfsScreen::saveNfsIp')),10);

            $b->show();
        }

        $ips =  explode(',', $ips);
        foreach ( $ips as $ip) {
             $folderItems[] = array(
                PluginRegularFolderItem::media_url          => "nfs|nfs://$ip",
                PluginRegularFolderItem::caption            => $ip ,
                PluginRegularFolderItem::view_item_params   => array()
            );
        }

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
                ViewParams::num_rows  => 20
            ),

            PluginRegularFolderView::base_view_item_params          => array(
                ViewItemParams::item_paint_caption => true,
                ViewItemParams::item_paint_icon => false,
                ViewItemParams::item_layout =>  HALIGN_LEFT
            ),
            PluginRegularFolderView::not_loaded_view_item_params    => array(),
            PluginRegularFolderView::actions => array(
                GUI_EVENT_KEY_ENTER => array(
                    GuiAction::handler_string_id => PLUGIN_OPEN_FOLDER_ACTION_ID
                ),
                GUI_EVENT_KEY_POPUP_MENU => array(
                    GuiAction::handler_string_id => PLUGIN_HANDLE_USER_INPUT_ACTION_ID,
                    GuiAction::params =>  array(
                            'type'=>"closeAndRunThisStaticMethod",
                            'method' => 'NfsScreen::saveNfsIp',
                        ),
                    GuiAction::data => null,

                )
            )
        );

        $a = array(
            PluginFolderView::view_kind                             =>  PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::data                                  => $availableTemplates
        );


        hd_print_r(__METHOD__, $a);
        return $a;

    }


    protected function showMountPoints(){
        hd_print("entrou");
         $itens = array();

        $folderItems = array();
        $a =  $this->nfs->getAllNfsPaths();
        // $a->mount();
        // var_dump($a);
        foreach ( $a as $key=>$value) {
            // var_dump($key);
            // var_dump($value);
             $folderItems[] = array(
                PluginRegularFolderItem::media_url          => "$value",
                PluginRegularFolderItem::caption            => basename($value) ,
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
                ViewParams::num_cols  => 2,
                ViewParams::num_rows  => 10
            ),

            PluginRegularFolderView::base_view_item_params          => array(
                ViewItemParams::item_paint_icon => false,
                ViewItemParams::item_layout =>  HALIGN_LEFT
            ),
            PluginRegularFolderView::not_loaded_view_item_params    => array(),
            PluginRegularFolderView::actions => array(
                GUI_EVENT_KEY_ENTER => array(
                    GuiAction::handler_string_id => PLUGIN_OPEN_FOLDER_ACTION_ID
                )
            )
        );

        $a = array(
            PluginFolderView::view_kind                             =>  PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::data                                  => $availableTemplates
        );
        return $a;
    }


    protected function showMountedPath(){
        $itens = array();

        $folderItems = array();
        $a =  $this->nfs->getIteratorForNfsPath($this->path);
        // var_dump($a);
        $a->mount();
        // var_dump($a);
        foreach ( $a as $key=>$value) {
            // var_dump($key);
            // var_dump($value);
             $folderItems[] = array(
                PluginRegularFolderItem::media_url          => "nfs|$value",
                PluginRegularFolderItem::caption            => basename($value) ,
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
                ViewParams::num_cols  => 2,
                ViewParams::num_rows  => 10
            ),

            PluginRegularFolderView::base_view_item_params          => array(
                ViewItemParams::item_paint_icon => false,
                ViewItemParams::item_layout =>  HALIGN_LEFT
            ),
            PluginRegularFolderView::not_loaded_view_item_params    => array(),
            PluginRegularFolderView::actions => array(
                GUI_EVENT_KEY_ENTER => array(
                    GuiAction::handler_string_id => PLUGIN_OPEN_FOLDER_ACTION_ID
                )
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