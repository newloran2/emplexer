<?php

/**
*
*/
class NfsScreen  implements ScreenInterface
{

    private $path;
    private $nfs;
    private $serverIps;
    function __construct($path=null) {
        $ipsString = Config::getInstance()->nfsIps;
        if ($ipsString){
            $$this->serverIps =  json_decode(urldecode($ipsString));
        }

        $this->path = $path;
        if ($path){
            $this->nfs = new NFS($path);
            $this->nfs->mountAll();
        };
    }

    public static function teste($user_input){
        hd_print_r('entrou aqui porra!!!!!', $user_input);
        return ActionFactory::open_folder("nfs");
    }

    public function generateScreen(){
        hd_print($this->path);
        if (filter_var($this->path, FILTER_VALIDATE_IP )){
            return $this->showMountPoints();
        } else if (filter_var($this->path, FILTER_VALIDATE_URL)){
            return $this->showMountPoints();
        } else {
            $b = new Modal(_("Continuar a partir de"), null ,100);

            // $b->addControl(new GuiControlButton('button3', 'nfs ip', 100, Actions::closeAndRunThisStaticMethod('NfsScreen::teste')));
            // $b->addControl(new GuiControlLabel(null, 'O próximo campo é um text filed aberto:'));
            // $b->addControl(new GuiControlText('text1', null, 'manda ai mano', 600));
            // $b->addControl(new GuiControlLabel(null, 'O próximo campo é um text filed numérico:'));
            // $b->addControl(new GuiControlNumericText('text2', null, 0, 600));
            // $b->addControl(new GuiControlLabel(null, 'O próximo campo é um text filed de password:'));
            // $b->addControl(new GuiControlPasswordText('text3', null, 0, 600));

            // $b->addControl(new GuiControlText('text1', 'esse é o teste 1', 'manda ai mano'));

            $b->addControl(new GuiControlButton('button3', '10:00', 500, Actions::closeAndRunThisStaticMethod('NfsScreen::teste', array("erik", 'clemente'))));
            $b->addControl(new GuiControlButton('button3', 'inicio', 500, Actions::closeAndRunThisStaticMethod('NfsScreen::teste', array('clemente', 'erik'))));

            $b->show();
        }
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


    protected function showMountedPath(){

    }
}


?>