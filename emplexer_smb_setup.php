<?php

class EmplexerSMBSetup extends AbstractPreloadedRegularScreen implements UserInputHandler {

    const ID = "emplexer_smb_setup";

    private $smbServers;


    function __construct($id=null,$folder_views=null)
        {
            if (!is_null($id) && !is_null($folder_views) ){
                parent::__construct($id, $folder_views);
            }else {
                parent::__construct(self::ID, $this->get_folder_views());
            }
        }

    public function get_handler_id(){
        // hd_print(__METHOD__);
        return self::ID;
    }

    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        // hd_print(__METHOD__);
        UserInputHandlerRegistry::get_instance()->register_handler($this);
        $show_resume_menu_action = UserInputHandlerRegistry::create_action($this, 'show_resume_menu');
        $info_action = UserInputHandlerRegistry::create_action($this, 'info');
        $pop_up_action = UserInputHandlerRegistry::create_action($this, 'pop_up', null,'Filtos');

        $a = array
        (
            GUI_EVENT_KEY_ENTER => $show_resume_menu_action,
            GUI_EVENT_KEY_PLAY => $show_resume_menu_action,
            GUI_EVENT_KEY_POPUP_MENU => $pop_up_action,
            GUI_EVENT_KEY_INFO => $info_action

            );

        // hd_print(print_r($a, true));
        return $a;
    }


    public static function get_media_url_str($serverName=null)
    {
        hd_print(__METHOD__);
        return MediaURL::encode(
            array
            (
                'screen_id' => self::ID,
                'serverName'    => $serverName
                )
            );
    }

        public function handle_user_input(&$user_input, &$plugin_cookies){
        }


    public function get_all_folder_items(MediaURL $media_url , &$plugin_cookies){
        hd_print(__METHOD__);
        if (!$this->smbServers){
            $this->smbServers = new SMBLookUp();
        }
        $items = array();
        if (!$media_url->serverName){
            foreach ($this->smbServers->getServers() as $serverName => $server) {
                $items[] = array
                (
                    PluginRegularFolderItem::media_url        => EmplexerSMBSetup::get_media_url_str($serverName) ,
                    PluginRegularFolderItem::caption          => $serverName
                );
            }
        } else {
            $servers = $this->smbServers->getServers();
            foreach ($servers[$media_url->serverName]->shares as $shareName) {
                $items[] = array
                (
                    PluginRegularFolderItem::media_url        => EmplexerSMBSetup::get_media_url_str($shareName) ,
                    PluginRegularFolderItem::caption          => $shareName
                );
            }
        }
        return $items;
    }

    public  function get_folder_views()
    {
        // hd_print(__METHOD__);
        return EmplexerConfig::getInstance()->GET_EPISODES_LIST_VIEW();
    }
}

?>