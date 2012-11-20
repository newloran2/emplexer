<?php 
/**
* 
*/
class EmplexerConfig
{
	const DEFAULT_PLEX_PORT = 32400; 
	const DEFAULT_PLEX_IP = "http://192.168.2.9:"; 
	const DEFAULT_PLEX = "http://192.168.2.9:32400";

	const USE_NFS = false; 
	const USE_SMB = false; 

    const USE_CACHE = false;



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
            PluginRegularFolderView::async_icon_loading => false,

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
                ViewParams::paint_item_info_in_details => true
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
    PluginRegularFolderView::async_icon_loading => false,

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
    PluginRegularFolderView::async_icon_loading => false,

    PluginRegularFolderView::view_params => array
    (
        ViewParams::num_cols => 1,
        ViewParams::num_rows => 12,
        ViewParams::paint_details => true,
        ViewParams::zoom_detailed_icon => true,
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
            PluginRegularFolderView::async_icon_loading => false,

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
                ViewItemParams::icon_path => 'plugin_file://icons/no-picture.png',
                ViewItemParams::item_detailed_icon_path => 'missing://',
            ),
        ),

        array
        (
            PluginRegularFolderView::async_icon_loading => false,

            PluginRegularFolderView::view_params => array
            (
                ViewParams::num_cols => 1,
                ViewParams::num_rows => 1,
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
                ViewParams::paint_path_box => false,
                ViewParams::paint_help_line => true,
                ViewParams::paint_scrollbar => false,
                ViewParams::content_box_x =>0,
                ViewParams::content_box_y =>816,
                ViewParams::content_box_width =>1920,
                ViewParams::content_box_height =>118,
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
                ViewItemParams::icon_path => 'plugin_file://icons/no-picture.png',
                ViewItemParams::item_detailed_icon_path => 'missing://',
            ),
        ),



                // list view
        array
        (
            PluginRegularFolderView::async_icon_loading => false,

            PluginRegularFolderView::view_params => array
            (
                ViewParams::num_cols => 1,
                ViewParams::num_rows => 12,
                ViewParams::paint_details => true,
                ViewParams::zoom_detailed_icon => true,
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



}
?>