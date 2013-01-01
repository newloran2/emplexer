<?php 

define('HTTP_CONNECTION_TYPE', 'HTTP');
define('NFS_CONNECTION_TYPE' , 'NFS');
define('SMB_CONNECTION_TYPE' , 'SMB');
define('DEFAULT_NOT_SEEN_CAPTION_COLOR', 'FFFFFF');
define('DEFAULT_HAS_SEEN_CAPTION_COLOR', 'FFFFFF');
define('DEFAULT_TIME_TO_MARK', 40);

define('TYPE_DIRECTORY', 'directory');
define('TYPE_VIDEO', 'video');
define('TYPE_TRACK', 'track');
define('TYPE_PHOTO', 'photo');
define('TYPE_SEARCH', 'search');
define('TYPE_CONF', 'conf');




class EmplexerConfig 
{


    const DEFAULT_PLEX_PORT              = 32400; 
    const USE_NFS                        = true; 
    const USE_SMB                        = false; 
    const USE_CACHE                      = true;
    const CREATE_LOG_FOLDER              = true;
    const CREATE_CACHE_FOLDER_ON_MAIN_HD = false;
    



    

    //static $currentPlexBaseUR='';

    public static function getPlexBaseUrl(&$plugin_cookies, $handler){
//    hd_print(__METHOD__ . ':' . print_r($plugin_cookies, true));
    // if ($currentPlexBaseUR) {
    //     return $currentPlexBaseUR;
    // }else {

        $plexIp   = $plugin_cookies->plexIp;
        $plexPort = $plugin_cookies->plexPort;
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
        return "http://$plexIp:$plexPort";
    // }
    }



    public static function getAllAvailableChannels(&$plugin_cookies, $handler)
    {    
        $url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $handler) ;
        hd_print("BASE_URL=$url" );
        $xml = HD::getAndParseXmlFromUrl($url);

        hd_print(print_r($xml, true));

    // $validChannels = array('video', 'music', 'photos');
        $validChannels = EmplexerConfig::getValidChannelsNames();
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


    public static function getValidChannelsNames(){
        return  array('video', 'music', 'photos');
    }

    public static function GET_SECTIONS_LIST_VIEW()
    {
        return array(
            array
            (
                PluginRegularFolderView::async_icon_loading => false,

                PluginRegularFolderView::view_params => array
                (
                    ViewParams::num_cols => 1,
                    ViewParams::num_rows => 12,
                    ),

                PluginRegularFolderView::base_view_item_params => array
                (
                    ViewItemParams::item_paint_icon => FALSE,
                    ViewItemParams::icon_scale_factor => 0.75,
                    ViewItemParams::icon_sel_scale_factor => 1,
                    ViewItemParams::icon_path => 'missing://',
                    ViewItemParams::item_layout => HALIGN_LEFT,
                    ViewItemParams::icon_valign => VALIGN_CENTER,
                    ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
                    ),

                PluginRegularFolderView::not_loaded_view_item_params => array(),
                )
            );
    }

    public static function GET_VIDEOS_LIST_VIEW(){
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
                    ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL
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
    ViewItemParams::icon_path => 'missing://',
    ViewItemParams::item_detailed_icon_path => 'missing://',
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
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL
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
        ViewItemParams::item_detailed_icon_path => 'missing://',
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
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL
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
        ViewItemParams::item_detailed_icon_path => 'missing://',
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
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL
        ),

    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_paint_icon => FALSE,
        ViewItemParams::icon_scale_factor => 0.75,
        ViewItemParams::icon_sel_scale_factor => 1,
        ViewItemParams::icon_path => 'missing://',
        ViewItemParams::item_layout => HALIGN_LEFT,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array(),
    ),
);
}

public static function GET_EPISODES_LIST_VIEW(){
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
                ViewParams::item_detailed_info_title_color => 6 #FFE040 ,
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
                ViewItemParams::icon_path => 'missing://',
                ViewItemParams::item_detailed_icon_path => 'missing://',
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
        ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL
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
    ViewItemParams::icon_path => 'missing://',
            ViewItemParams::item_detailed_icon_path => 'missing://',
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
        ),

    PluginRegularFolderView::base_view_item_params => array
    (
        ViewItemParams::item_paint_icon => FALSE,
        ViewItemParams::icon_scale_factor => 0.75,
        ViewItemParams::icon_sel_scale_factor => 1,
        ViewItemParams::icon_path => 'missing://',
        ViewItemParams::item_layout => HALIGN_LEFT,
        ViewItemParams::icon_valign => VALIGN_CENTER,
        ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
        ),

    PluginRegularFolderView::not_loaded_view_item_params => array(
        ViewItemParams::icon_path => 'missing://',
        ViewItemParams::item_detailed_icon_path => 'missing://',
        ViewItemParams::item_paint_caption_within_icon => true,
        ViewItemParams::item_caption_within_icon_color => 'white',
        ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL
        ),
    )
);

}



}
?>
