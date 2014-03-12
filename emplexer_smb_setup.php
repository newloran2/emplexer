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

times in msec
 clock   self+sourced   self:  sourced script
 clock   elapsed:              other lines

000.009  000.009: --- VIM STARTING ---
000.080  000.071: Allocated generic buffers
000.091  000.011: locale set
000.094  000.003: clipboard setup
000.100  000.006: window checked
006.243  006.143: inits 1
006.252  000.009: parsing arguments
006.253  000.001: expanding arguments
008.789  002.536: shell init
009.002  000.213: Termcap init
009.027  000.025: inits 2
009.153  000.126: init highlight
010.353  000.868  000.868: sourcing /Users/newloran2/.vim/vim-addons/vim-addon-manager/autoload/vam.vim
023.625  000.176  000.176: sourcing /usr/local/share/vim/vim74/syntax/syncolor.vim
023.724  000.413  000.237: sourcing /usr/local/share/vim/vim74/syntax/synload.vim
038.846  014.990  014.990: sourcing /usr/local/share/vim/vim74/filetype.vim
038.902  015.737  000.334: sourcing /usr/local/share/vim/vim74/syntax/syntax.vim
039.479  000.162  000.162: sourcing /usr/local/share/vim/vim74/syntax/syncolor.vim
040.045  000.161  000.161: sourcing /usr/local/share/vim/vim74/syntax/syncolor.vim
040.478  000.155  000.155: sourcing /usr/local/share/vim/vim74/syntax/syncolor.vim
040.889  000.153  000.153: sourcing /usr/local/share/vim/vim74/syntax/syncolor.vim
041.276  000.156  000.156: sourcing /usr/local/share/vim/vim74/syntax/syncolor.vim
041.689  002.042  001.417: sourcing /Users/newloran2/.vim/vim-addons/molokai/colors/molokai.vim
041.699  032.402  013.593: sourcing $HOME/.vimrc
041.703  000.148: sourcing vimrc file(s)
045.089  003.217  003.217: sourcing /Users/newloran2/.vim/vim-addons/fugitive/plugin/fugitive.vim
045.588  000.080  000.080: sourcing /Users/newloran2/.vim/vim-addons/vim-gitgutter/autoload/highlight.vim
046.190  000.982  000.902: sourcing /Users/newloran2/.vim/vim-addons/vim-gitgutter/plugin/gitgutter.vim
046.906  000.590  000.590: sourcing /Users/newloran2/.vim/vim-addons/matchit.zip/plugin/matchit.vim
047.484  000.441  000.441: sourcing /Users/newloran2/.vim/vim-addons/PIV/plugin/phpfolding.vim
047.562  000.047  000.047: sourcing /Users/newloran2/.vim/vim-addons/PIV/plugin/piv.vim
047.731  000.060  000.060: sourcing /Users/newloran2/.vim/vim-addons/trailing-whitespace/plugin/trailing-whitespace.vim
048.155  000.318  000.318: sourcing /Users/newloran2/.vim/vim-addons/colors/plugin/colors.vim
048.397  000.016  000.016: sourcing /Users/newloran2/.vim/vim-addons/vim-misc/autoload/xolox/misc.vim
048.469  000.195  000.179: sourcing /Users/newloran2/.vim/vim-addons/lua%3625/plugin/lua-ftplugin.vim
052.199  003.628  003.628: sourcing /Users/newloran2/.vim/vim-addons/EasyMotion/plugin/EasyMotion.vim
053.628  000.914  000.914: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/autoload/nerdtree.vim
054.808  000.441  000.441: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/path.vim
055.055  000.131  000.131: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/menu_controller.vim
055.251  000.087  000.087: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/menu_item.vim
055.461  000.104  000.104: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/key_map.vim
055.787  000.217  000.217: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/bookmark.vim
056.210  000.312  000.312: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/tree_file_node.vim
056.680  000.358  000.358: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/tree_dir_node.vim
056.961  000.175  000.175: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/opener.vim
057.286  000.219  000.219: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/lib/nerdtree/creator.vim
066.433  000.102  000.102: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/nerdtree_plugin/exec_menuitem.vim
066.849  000.384  000.384: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/nerdtree_plugin/fs_menu.vim
067.075  014.763  011.319: sourcing /Users/newloran2/.vim/vim-addons/The_NERD_tree/plugin/NERD_tree.vim
067.685  000.501  000.501: sourcing /Users/newloran2/.vim/vim-addons/taglist/plugin/taglist.vim
069.159  000.364  000.364: sourcing /Users/newloran2/.vim/vim-addons/ctrlp/autoload/ctrlp/mrufiles.vim
069.451  001.124  000.760: sourcing /Users/newloran2/.vim/vim-addons/ctrlp/plugin/ctrlp.vim
070.300  000.039  000.039: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/autoloclist.vim
070.384  000.035  000.035: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/balloons.vim
070.468  000.037  000.037: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/checker.vim
070.550  000.036  000.036: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/cursor.vim
070.633  000.038  000.038: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/highlighting.vim
070.715  000.037  000.037: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/loclist.vim
070.799  000.040  000.040: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/modemap.vim
070.883  000.039  000.039: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/notifiers.vim
070.969  000.040  000.040: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/registry.vim
071.066  000.051  000.051: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/signs.vim
071.825  000.407  000.407: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/autoload/syntastic/util.vim
084.394  000.061  000.061: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/autoloclist.vim
084.500  000.072  000.072: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/balloons.vim
084.688  000.147  000.147: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/checker.vim
084.785  000.065  000.065: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/cursor.vim
084.904  000.087  000.087: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/highlighting.vim
085.129  000.193  000.193: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/loclist.vim
085.237  000.075  000.075: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/modemap.vim
085.329  000.062  000.062: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/notifiers.vim
085.620  000.260  000.260: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/registry.vim
085.764  000.115  000.115: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic/signs.vim
086.636  015.521  013.977: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/plugin/syntastic.vim
087.039  000.275  000.275: sourcing /Users/newloran2/.vim/vim-addons/SuperTab%182/plugin/supertab.vim
087.293  000.144  000.144: sourcing /Users/newloran2/.vim/vim-addons/ZoomWin/plugin/ZoomWinPlugin.vim
113.860  026.232  026.232: sourcing /Users/newloran2/.vim/vim-addons/taggatron/autoload/taggatron.vim
114.222  026.795  000.563: sourcing /Users/newloran2/.vim/vim-addons/taggatron/plugin/taggatron.vim
114.688  000.091  000.091: sourcing /Users/newloran2/.vim/vim-addons/neocomplete/plugin/neocomplete/buffer.vim
114.795  000.071  000.071: sourcing /Users/newloran2/.vim/vim-addons/neocomplete/plugin/neocomplete/dictionary.vim
114.900  000.071  000.071: sourcing /Users/newloran2/.vim/vim-addons/neocomplete/plugin/neocomplete/include.vim
115.003  000.069  000.069: sourcing /Users/newloran2/.vim/vim-addons/neocomplete/plugin/neocomplete/syntax.vim
115.102  000.067  000.067: sourcing /Users/newloran2/.vim/vim-addons/neocomplete/plugin/neocomplete/tag.vim
115.298  000.163  000.163: sourcing /Users/newloran2/.vim/vim-addons/neocomplete/plugin/neocomplete.vim
115.795  000.075  000.075: sourcing /usr/local/share/vim/vim74/plugin/getscriptPlugin.vim
116.039  000.214  000.214: sourcing /usr/local/share/vim/vim74/plugin/gzip.vim
116.287  000.218  000.218: sourcing /usr/local/share/vim/vim74/plugin/matchparen.vim
116.882  000.563  000.563: sourcing /usr/local/share/vim/vim74/plugin/netrwPlugin.vim
116.950  000.027  000.027: sourcing /usr/local/share/vim/vim74/plugin/rrhelper.vim
117.050  000.039  000.039: sourcing /usr/local/share/vim/vim74/plugin/spellfile.vim
117.252  000.165  000.165: sourcing /usr/local/share/vim/vim74/plugin/tarPlugin.vim
117.402  000.111  000.111: sourcing /usr/local/share/vim/vim74/plugin/tohtml.vim
117.589  000.152  000.152: sourcing /usr/local/share/vim/vim74/plugin/vimballPlugin.vim
117.832  000.200  000.200: sourcing /usr/local/share/vim/vim74/plugin/zipPlugin.vim
117.901  004.909: loading plugins
117.909  000.008: inits 3
118.158  000.249: reading viminfo
118.182  000.024: setting raw mode
118.190  000.008: start termcap
118.247  000.057: clearing screen
118.971  000.415  000.415: sourcing /Users/newloran2/.vim/vim-addons/Syntastic/autoload/syntastic/log.vim
119.512  000.850: opening buffers
119.929  000.315  000.315: sourcing /Users/newloran2/.vim/vim-addons/vim-gitgutter/autoload/utility.vim
120.207  000.113  000.113: sourcing /Users/newloran2/.vim/vim-addons/vim-gitgutter/autoload/hunk.vim
120.341  000.401: BufEnter autocommands
120.343  000.002: editing files in windows
129.685  009.342: VimEnter autocommands
129.691  000.006: before starting main loop
130.454  000.763: first screen update
130.456  000.002: --- VIM STARTED ---
