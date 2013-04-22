®<?php 

define('HTTP_CONNECTION_TYPE', 'http');
define('NFS_CONNECTION_TYPE' , 'nfs');
define('SMB_CONNECTION_TYPE' , 'smb');
define('DEFAULT_NOT_SEEN_CAPTION_COLOR', 'FFFFFF');
define('DEFAULT_HAS_SEEN_CAPTION_COLOR', 'FFFFFF');
define('DEFAULT_TIME_TO_MARK', 40);

define('TYPE_DIRECTORY', 'directory');
define('TYPE_VIDEO', 'video');
define('TYPE_TRACK', 'track');
define('TYPE_PHOTO', 'photo');
define('TYPE_SEARCH', 'search');
define('TYPE_CONF', 'conf');

//section types
define('SECTION_MOVIE', 'movie');
define('SECTION_SHOW', 'show');



define('CREATE_LOG_FOLDER', true);
define('DEFAULT_PLEX_PORT', '32400');

define('THUMB_WIDTH', '200');
define('THUMB_HEIGHT', '294');



define('CACHE_DIR', '/persistfs/plugins_archive/emplexer/emplexer_default_archive');
define('PERSISTFS_DIR', '/persistfs/plugins_archive/emplexer');



class EmplexerConfig 
{


    public $cacheDirExists;
    public $hasPersistFs;
    public $useCache;

    g
    private static $instance;


    private function __construct()
    {
        hd_print(__METHOD__);
        $this->cacheDirExists = file_exists(CACHE_DIR);
        $this->useCache = false;
        $this->hasPersistFs = file_exists(PERSISTFS_DIR);
    }


    public static function getInstance()
    {
        if (!isset(self::$instance)){
            self::$instance = new EmplexerConfig();
        }
        return self::$instance;
    }

    //static $currentPlexBaseUR='';

    public function getPlexBaseUrl(&$plugin_cookies, $handler){
//    hd_print(__METHOD__ . ':' . print_r($plugin_cookies, true));
    // if ($currentPlexBaseUR) {
    //     return $currentPlexBaseUR;
    // }else {
        // hd_print(__METHOD__);
        $plexIp   = $plugin_cookies->plexIp;
        $plexPort = $plugin_cookies->plexPort;

        //quick fix.
        //caso o usuário tenha instalado por cima de uma versão antiga que ainda não tenha esses items esses não são setados.
        //caso esses não sejam setados o plugin não consegue achar o conection type e com isso não sabe qual url usar.
        $connectionMethod       = $plugin_cookies->connectionMethod ? $plugin_cookies->connectionMethod : HTTP_CONNECTION_TYPE ;
        $hasSeenCaptionColor    = $plugin_cookies->hasSeenCaptionColor ? $plugin_cookies->hasSeenCaptionColor : DEFAULT_HAS_SEEN_CAPTION_COLOR ;
        $notSeenCaptionColor    = $plugin_cookies->notSeenCaptionColor ? $plugin_cookies->notSeenCaptionColor : DEFAULT_NOT_SEEN_CAPTION_COLOR;


        if (!$plexIp || !$plexPort){

            $btnSaveAction = UserInputHandlerRegistry::create_action($handler, 'savePref');

            throw new DuneException(
                'Error: emplexer not configured, please go to setup and set ip and port' ,
                0,
                // ActionFactory::show_title_dialog('Configure your emplexer')
                ActionFactory::show_configuration_modal('configure your emplexer.', $plugin_cookies, $btnSaveAction)
                // ActionFactory::show_error(false, "emplexer not configured, please go to setup and set ip and port")
                );
        }

        //checa se precisa criar o cache_dir;
        EmplexerConfig::getInstance()->createCacheDirIfNeeded($plugin_cookies);

        return "http://$plexIp:$plexPort";
    // }
    }


    public function createCacheDirIfNeeded(&$plugin_cookies){
    //se não existir o diretorio de cache devo criar
    hd_print(__METHOD__ . ': ' . print_r($plugin_cookies, true) );
        $this->useCache  = $plugin_cookies->useCache;
        hd_print( __METHOD__ . ': useCache = ' . $this->useCache . ' cacheDirExists =' . $this->cacheDirExists);
        // hd_print(__METHOD__ . ': ' . EmplexerConfig::getInstance()->useCache);
        if ($this->useCache && !$this->cacheDirExists){
            $result = mkdir(CACHE_DIR);
            hd_print("criação de diretório de cache em " .CACHE_DIR .  "[" . $result ? 'OK' : 'FAIL' . "]" );
        }
    }


    public function getUseCache(){
        hd_print(__METHOD__ . ': useCache = ' . $this->useCache);
        return $this->useCache;
    }

    public function __set($name, $value)
    {
        hd_print("Setting '$name' to '$value'");
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        hd_print("Getting '$name'\n");
        return $this->{$name};
    }



    public function getAllAvailableChannels(&$plugin_cookies, $handler)
    {    
        hd_print(__METHOD__);
        $url = EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $handler) ;
        hd_print("BASE_URL=$url" );
        $xml = HD::getAndParseXmlFromUrl($url);

        hd_print(print_r($xml, true));

    // $validChannels = array('video', 'music', 'photos');
        $validChannels = EmplexerConfig::getInstance()->getValidChannelsNames();
        $items = array();
        foreach ($xml as $d) {
            $key = (string)$d->attributes()->key;
            if (in_array($key, $validChannels)){
                $channelName = ucwords("$key Channels");
                $items[] = array
                (
                    PluginRegularFolderItem::media_url =>  EmplexerBaseChannel::get_media_url_str($key),
                    PluginRegularFolderItem::caption => $channelName,
                    PluginRegularFolderItem::view_item_params =>
                    array
                    (
                        ViewItemParams::icon_path => 'plugin_file://icons/sudoku.png',
                        )
                    );
            }
        }

        hd_print(__METHOD__ . ':' . print_r($items, true));
        return $items;
    }


    public function getValidChannelsNames(){
        hd_print(__METHOD__);
        return  array('video', 'music', 'photos');
    }

    public function GET_SECTIONS_LIST_VIEW($art=null)
    {
        return array(
            array
            (
                PluginRegularFolderView::async_icon_loading => false,

                PluginRegularFolderView::view_params => array
                (
                    ViewParams::num_cols => 1,
                    ViewParams::num_rows => 12,
                    ViewParams::background_path => $art,
                    ViewParams::optimize_full_screen_background => false,
                    ViewParams::background_order => 'before_all'
                    ),

                PluginRegularFolderView::base_view_item_params => array
                (
                    ViewItemParams::item_paint_icon => FALSE,
                    ViewItemParams::icon_scale_factor => 0.75,
                    ViewItemParams::icon_sel_scale_factor => 1,
                    ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
                    ViewItemParams::item_layout => HALIGN_LEFT,
                    ViewItemParams::icon_valign => VALIGN_CENTER,
                    ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
                    ),

                PluginRegularFolderView::not_loaded_view_item_params => array(),
                )
            );
    }

    public function GET_VIDEOS_LIST_VIEW($art=null){
        
        return array(

            // large icons view
            array
            (
                PluginRegularFolderView::async_icon_loading => true,

                PluginRegularFolderView::view_params => array
                (
                    ViewParams::num_cols => 5,
                    ViewParams::num_rows => 2,
                    ViewParams::paint_sandwich => true,
                    ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
                    ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
                    ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
                    ViewParams::sandwich_width => 200, 
                    ViewParams::sandwich_height => 300, 
                    ViewParams::sandwich_icon_upscale_enabled => true,
                    ViewParams::sandwich_icon_keep_aspect_ratio => true,
                    //ViewParams::icon_selection_box_width => 180, //150,
                    //ViewParams::icon_selection_box_height => 333, //222,
                    ViewParams::paint_details => true,
                    ViewParams::zoom_detailed_icon => true,
                    ViewParams::paint_item_info_in_details => true,
                    ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
                    ViewParams::background_path => $art,
                    ViewParams::optimize_full_screen_background => false,
                    ViewParams::background_order => 'before_all'
                    ),
PluginRegularFolderView::base_view_item_params => array
(
    ViewItemParams::item_padding_top => 0,
    ViewItemParams::item_padding_bottom => 0,
    ViewItemParams::icon_valign => VALIGN_CENTER,
    ViewItemParams::item_paint_caption => false,
                    //ViewItemParams::icon_width => 180, //120,
                    //ViewItemParams::icon_height => 245, //180,
    ViewItemParams::icon_scale_factor => 1.0,
                    ViewItemParams::icon_sel_scale_factor => 1.1, //1.2,
                    ),

PluginRegularFolderView::not_loaded_view_item_params => array
(
    ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
    ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
    ViewItemParams::item_paint_caption_within_icon => true,
    ViewItemParams::item_caption_within_icon_color => 'white',
    ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL
    ),
),

    // large icons view without details
array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 7,
        ViewParams::num_rows => 2,
        ViewParams::paint_sandwich => true,
        ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
        ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
        ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
        ViewParams::sandwich_width => 200, 
        ViewParams::sandwich_height => 300, 
        ViewParams::sandwich_icon_upscale_enabled => true,
        ViewParams::sandwich_icon_keep_aspect_ratio => true,
                    //ViewParams::icon_selection_box_width => 180, //150,
                    //ViewParams::icon_selection_box_height => 333, //222,
        ViewParams::paint_details => false,
        ViewParams::zoom_detailed_icon => false,
        ViewParams::paint_item_info_in_details => true,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::background_order => 'before_all'
        ),
    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_padding_top => 0,
        ViewItemParams::item_padding_bottom => 0,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_paint_caption => false,
                    //ViewItemParams::icon_width => 180, //120,
                    //ViewItemParams::icon_height => 245, //180,
        ViewItemParams::icon_scale_factor => 1.0,
                ViewItemParams::icon_sel_scale_factor => 1.1, //1.2,
                ),

    PluginRegularFolderView::not_loaded_view_item_params => array
    (
        ViewItemParams::icon_path => 'plugin_file://icons/no-picture.png',
        ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
        ),
    ),

            // normal icons view
array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 6,
        ViewParams::num_rows => 3,
        ViewParams::paint_sandwich => true,
        ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
        ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
        ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
        ViewParams::sandwich_width => 150,
        ViewParams::sandwich_height => 190,
        ViewParams::icon_selection_box_width => 150,
        ViewParams::icon_selection_box_height => 222,
        ViewParams::paint_details => true,
        ViewParams::zoom_detailed_icon => true,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::background_order => 'before_all'
        ),
    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_padding_top => 0,
        ViewItemParams::item_padding_bottom => 0,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_paint_caption => false,
        ViewItemParams::icon_width => 120,
        ViewItemParams::icon_height => 180,
        ViewItemParams::icon_scale_factor => 1.0,
        ViewItemParams::icon_sel_scale_factor => 1.2,
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array
    (
        ViewItemParams::icon_path => 'plugin_file://icons/no-picture.png',
        ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
        ),
    ),
// small icons view without path with details
array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 6,
        ViewParams::num_rows => 4,
        ViewParams::paint_sandwich => true,
        ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
        ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
        ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
        ViewParams::sandwich_width => 150,
        ViewParams::sandwich_height => 190,
        ViewParams::icon_selection_box_width => 150,
        ViewParams::icon_selection_box_height => 222,
        ViewParams::paint_details => true,
        ViewParams::zoom_detailed_icon => true,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::paint_path_box => false,
        ViewParams::background_order => 'before_all'
        ),
    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_padding_top => 0,
        ViewItemParams::item_padding_bottom => 0,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_paint_caption => false,
        ViewItemParams::icon_width => 120,
        ViewItemParams::icon_height => 180,
        ViewItemParams::icon_scale_factor => 1.0,
        ViewItemParams::icon_sel_scale_factor => 1.2,
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array
    (
        ViewItemParams::icon_path => 'plugin_file://icons/no-picture.png',
        ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
        ),
    ),

// small icons view without path without details
array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 10,
        ViewParams::num_rows => 1,
        ViewParams::paint_sandwich => true,
        ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
        ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
        ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
        ViewParams::sandwich_width => 150,
        ViewParams::sandwich_height => 190,
        ViewParams::icon_selection_box_width => 150,
        ViewParams::icon_selection_box_height => 222,
        ViewParams::paint_details => false,
        ViewParams::zoom_detailed_icon => true,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::paint_path_box => false,
        ViewParams::paint_help_line => true,
        ViewParams::paint_scrollbar => false,
        ViewParams::content_box_y => 1500,
        ViewParams::content_box_height=>200,
        ViewParams::content_box_padding_left => 200,
        ViewParams::background_order => 'before_all'
        ),
    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_padding_top => 0,
        ViewItemParams::item_padding_bottom => 0,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_paint_caption => false,
        ViewItemParams::icon_width => 120,
        ViewItemParams::icon_height => 180,
        ViewItemParams::icon_scale_factor => 1.0,
        ViewItemParams::icon_sel_scale_factor => 1.2,
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array
    (
        ViewItemParams::icon_path => 'plugin_file://icons/no-picture.png',
        ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
        ),
    ),
            // list view
array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 1,
        ViewParams::num_rows => 12,
        ViewParams::paint_details => true,
        ViewParams::zoom_detailed_icon => true,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::background_order => 'before_all'
        ),

    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_paint_icon => FALSE,
        ViewItemParams::icon_scale_factor => 0.75,
        ViewItemParams::icon_sel_scale_factor => 1,
        ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
        ViewItemParams::item_layout => HALIGN_LEFT,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array(),
    ),
);
}

public  function GET_EPISODES_LIST_VIEW($art=null){
    return array(
        // normal icons view
        array
        (
            PluginRegularFolderView::async_icon_loading => true,

            PluginRegularFolderView::view_params => array
            (
                ViewParams::num_cols => 5,
                ViewParams::num_rows => 4,
                ViewParams::paint_sandwich => true,
                ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
                ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
                ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
                ViewParams::sandwich_width => 190,
                ViewParams::sandwich_height => 150,
                ViewParams::icon_selection_box_width => 222,
                ViewParams::icon_selection_box_height => 150,
                ViewParams::paint_details => true,
                ViewParams::zoom_detailed_icon => true,
                ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
                ViewParams::item_detailed_info_title_color => 6, #FFE040 ,
                ViewParams::background_path => $art,
                ViewParams::optimize_full_screen_background => true,
                ViewParams::background_order => 'before_all'
                ),

            PluginRegularFolderView::base_view_item_params => array
            (
                ViewItemParams::item_padding_top => 0,
                ViewItemParams::item_padding_bottom => 0,
                ViewItemParams::icon_valign => VALIGN_CENTER,
                ViewItemParams::item_paint_caption => false,
                ViewItemParams::icon_width => 190,
                ViewItemParams::icon_height => 110,
                ViewItemParams::icon_scale_factor => 1.0,
                ViewItemParams::icon_sel_scale_factor => 1.2,
                ),

            PluginRegularFolderView::not_loaded_view_item_params => array
            (
                ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
                ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
                ViewItemParams::item_paint_caption_within_icon => false,
                ViewItemParams::item_caption_within_icon_color => 'white',
                ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL

                ),
            ),

array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 7,
        ViewParams::num_rows => 4,
        ViewParams::paint_sandwich => true,
        ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
        ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
        ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
        ViewParams::sandwich_width => 190,
        ViewParams::sandwich_height => 150,
        ViewParams::icon_selection_box_width => 222,
        ViewParams::icon_selection_box_height => 150,
        ViewParams::paint_details => false,
        ViewParams::zoom_detailed_icon => false ,
        ViewParams::item_detailed_info_title_color => 6, #FFE040 ,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::background_order => 'before_all'
                // ViewParams::paint_path_box => false,
                // ViewParams::paint_help_line => true,
                // ViewParams::paint_scrollbar => false,
                // ViewParams::content_box_x =>0,
                // ViewParams::content_box_y =>816,
                // ViewParams::content_box_width =>1920,
                // ViewParams::content_box_height =>118,
                ),

PluginRegularFolderView::base_view_item_params => array
(
    ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL,
    ViewItemParams::item_padding_top => 0,
    ViewItemParams::item_padding_bottom => 0,
    ViewItemParams::icon_valign => VALIGN_CENTER,
    ViewItemParams::item_paint_caption => true,
    ViewItemParams::icon_width => 190,
    ViewItemParams::icon_height => 110,
    ViewItemParams::icon_scale_factor => 1.0,
    ViewItemParams::icon_sel_scale_factor => 1.2,
    ),

PluginRegularFolderView::not_loaded_view_item_params => array
(
    ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
            ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
            ViewItemParams::item_paint_caption_within_icon => false,
            ViewItemParams::item_caption_within_icon_color => 'white',
            ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL
    ),
),



                // list view
array
(
    PluginRegularFolderView::async_icon_loading => true,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 1,
        ViewParams::num_rows => 12,
        ViewParams::paint_details => true,
        ViewParams::zoom_detailed_icon => true,
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
        ViewParams::background_path => $art,
        ViewParams::optimize_full_screen_background => false,
        ViewParams::background_order => 'before_all'
        ),

    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_paint_icon => FALSE,
        ViewItemParams::icon_scale_factor => 0.75,
        ViewItemParams::icon_sel_scale_factor => 1,
        ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
        ViewItemParams::item_layout => HALIGN_LEFT,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array(
        ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
        ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
        ViewItemParams::item_paint_caption_within_icon => true,
        ViewItemParams::item_caption_within_icon_color => 'white',
        ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL
        ),
    )
);

}



}
?>
