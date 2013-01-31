<?php


class ActionFactory
{
    public static function open_folder($media_url = null, $caption = null)
    {
        return array
        (
            GuiAction::handler_string_id => PLUGIN_OPEN_FOLDER_ACTION_ID,
            GuiAction::data =>
                array
                (
                    PluginOpenFolderActionData::media_url => $media_url,
                    PluginOpenFolderActionData::caption => $caption,
                ),
        );
    }

    public static function tv_play()
    {
        return array
        (
            GuiAction::handler_string_id => PLUGIN_TV_PLAY_ACTION_ID,
        );
    }

    public static function vod_play()
    {
        return array
        (
            GuiAction::handler_string_id => PLUGIN_VOD_PLAY_ACTION_ID,
        );
    }

    public static function vod_play_with_vod_info($vod_info)
    {
        $a= array
        (
            GuiAction::handler_string_id => PLUGIN_VOD_PLAY_ACTION_ID,
            GuiAction::data => 
            array (
                PluginVodPlayActionData::vod_info =>$vod_info
            )
        );

        return $a;
    }


    public static function dvd_play($url)
    {
        return array
        (
            GuiAction::handler_string_id => DVD_PLAY_ACTION_ID,            
            GuiAction::data =>
             array
             (
                DvdPlayActionData::url => $url,
             ),
        );
    }

	 public static function launch_media_url($url,$post_action=null)
    {
        return array
        (
             GuiAction::handler_string_id => LAUNCH_MEDIA_URL_ACTION_ID,
             GuiAction::data =>
             array
             (
                LaunchMediaUrlActionData::url => $url,
                LaunchMediaUrlActionData::post_action => $post_action
             ),
        );
    }


    public static function show_error($fatal, $title, $msg_lines = null)
    {
        return array
        (
            GuiAction::handler_string_id => PLUGIN_SHOW_ERROR_ACTION_ID,
            GuiAction::caption => null,
            GuiAction::data =>
                array
                (
                    PluginShowErrorActionData::fatal => $fatal,
                    PluginShowErrorActionData::title => $title,
                    PluginShowErrorActionData::msg_lines => $msg_lines,
                ),
            GuiAction::params => null,
        );
    }

    public static function show_dialog($title, $defs,
        $close_by_return = false, $preferred_width = 0)
    {
        return array
        (
            GuiAction::handler_string_id => SHOW_DIALOG_ACTION_ID,
            GuiAction::caption => null,
            GuiAction::data =>
                array
                (
                    ShowDialogActionData::title => $title,
                    ShowDialogActionData::defs => $defs,
                    ShowDialogActionData::close_by_return => $close_by_return,
                    ShowDialogActionData::preferred_width => $preferred_width,
                ),
            GuiAction::params => null,
        );
    }

    public static function close_dialog_and_run($post_action)
    {
        return array
        (
            GuiAction::handler_string_id => CLOSE_DIALOG_AND_RUN_ACTION_ID,
            GuiAction::caption => null,
            GuiAction::data =>
                array
                (
                    CloseDialogAndRunActionData::post_action => $post_action,
                ),
            GuiAction::params => null,
        );
    }

    public static function close_dialog()
    {
        return self::close_dialog_and_run(null);
    }


    public static function show_title_dialog($title, $post_action = null)
    {
        $defs = array();

//        ControlFactory::add_vgap($defs, 50);

        ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
            'apply_subscription', 'OK', 300, $post_action);

        return self::show_dialog($title, $defs);
    }

    public static function status($status)
    {
        return array
        (
            GuiAction::handler_string_id => STATUS_ACTION_ID,
            GuiAction::caption => null,
            GuiAction::data =>
                array
                (
                    StatusActionData::status => $status,
                ),
            GuiAction::params => null,
        );
    }

    public static function invalidate_folders($media_urls,
        $post_action = null)
    {
        return array
        (
            GuiAction::handler_string_id => PLUGIN_INVALIDATE_FOLDERS_ACTION_ID,
            GuiAction::data =>
                array
                (
                    PluginInvalidateFoldersActionData::media_urls => $media_urls,
                    PluginInvalidateFoldersActionData::post_action => $post_action,
                ),
        );
    }

    public static function show_popup_menu($menu_items, $sel_ndx = 0)
    {
        return array
        (
            GuiAction::handler_string_id => SHOW_POPUP_MENU_ACTION_ID,
            GuiAction::data =>
                array
                (
                    ShowPopupMenuActionData::menu_items => $menu_items,
                    ShowPopupMenuActionData::selected_menu_item_index => $sel_ndx,
                ),
        );
    }

    public static function update_regular_folder($range,
        $need_refresh = false, $sel_ndx = -1)
    {
        return array
        (
            GuiAction::handler_string_id => PLUGIN_UPDATE_FOLDER_ACTION_ID,
            GuiAction::data =>
                array
                (
                    PluginUpdateFolderActionData::range => $range,
                    PluginUpdateFolderActionData::need_refresh => $need_refresh,
                    PluginUpdateFolderActionData::sel_ndx => intval($sel_ndx),
                ),
        );
    }

    public static function reset_controls($defs, $post_action = null, $initial_sel_ndx = -1)
    {
        return array
        (
             GuiAction::handler_string_id => RESET_CONTROLS_ACTION_ID,
             GuiAction::data =>
             array
             (
                ResetControlsActionData::defs => $defs,
                ResetControlsActionData::initial_sel_ndx => $initial_sel_ndx,
                ResetControlsActionData::post_action => $post_action,
             ),
        );
    }

    public static function clear_archive_cache($archive_id=null, $post_action=null)
    {
        return array
        (
             GuiAction::handler_string_id => PLUGIN_CLEAR_ARCHIVE_CACHE_ACTION_ID,
             GuiAction::data =>
             array
             (
                PluginClearArchiveCacheActionData::archive_id => $archive_id,
                PluginClearArchiveCacheActionData::post_action => $post_action,
             ),
        );
    }

    public static function notify($value)
    {
        hd_print("notify=$value");
    }

    public static function show_configuration_modal($title, &$plugin_cookies, $post_action= null){
      
        $defs = array();

        ControlFactory::add_text_field(
            $defs, 
            null, 
            null,
            $name            = 'plexIp', 
            $title           = 'Plex IP',  
            $initial_value   = $plugin_cookies->plexIp,
            $numeric         = false, 
            $password        = false, 
            $has_osk         = false, 
            $always_active   = 0, 
            $width           = 500
        );

        ControlFactory::add_text_field(
            $defs, 
            null, 
            null,
            $name            = 'plexPort', 
            $title           = 'Plex Port',  
            $initial_value   = $plugin_cookies->plexIp ? $plugin_cookies->plexIp : 32400,
            $numeric         = false, 
            $password        = false, 
            $has_osk         = false, 
            $always_active   = 0, 
            $width           = 500
        );

            ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
            'btnSalvar', 'save', 200, $post_action);


            return ActionFactory::show_dialog($title, $defs);
    }

     public static function show_nfs_advanced_configuration_modal($modalTitle, &$plugin_cookies,$post_action= null){
      
        $defs = array();
        $plexLocation = 'http://' . $plugin_cookies->plexIp . ':' . $plugin_cookies->plexPort . '/library/sections';
        $xml = HD::getAndParseXmlFromUrl($plexLocation);
        // hd_print(__METHOD__ . ';' . print_r($xml, true));
        foreach ($xml->Directory as $directory) {
            foreach ($directory->Location as $location) {
                ControlFactory::add_text_field(
                    $defs, 
                    null, 
                    null,
                    $name            = str_replace(array('/', ' '), '_', (string)$location->attributes()->path), 
                    $title           = (string)$location->attributes()->path,  
                    $initial_value   = 'nfs://'. $plugin_cookies->plexIp . ':' . (string)$location->attributes()->path,  
                    $numeric         = false, 
                    $password        = false, 
                    $has_osk         = false, 
                    $always_active   = 0, 
                    $width           = 500
                );
            }
        }
        ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
        'btnSalvar', 'save', 200, $post_action);


        return ActionFactory::show_dialog($modalTitle, $defs);
    }

    public static function show_smb_advanced_configuration_modal($modalTitle, &$plugin_cookies,$post_action= null){
      
        $defs = array();
        $plexLocation = 'http://' . $plugin_cookies->plexIp . ':' . $plugin_cookies->plexPort . '/library/sections';
        $xml = HD::getAndParseXmlFromUrl($plexLocation);
        // hd_print(__METHOD__ . ';' . print_r($xml, true));
        foreach ($xml->Directory as $directory) {
            foreach ($directory->Location as $location) {
                ControlFactory::add_text_field(
                    $defs, 
                    null, 
                    null,
                    $name            = str_replace(array('/', ' '), '_', (string)$location->attributes()->path), 
                    $title           = (string)$location->attributes()->path,  
                    $initial_value   = 'smb://user:password@'. $plugin_cookies->plexIp . ':' . (string)$location->attributes()->path,  
                    $numeric         = false, 
                    $password        = false, 
                    $has_osk         = false, 
                    $always_active   = 0, 
                    $width           = 500
                );
            }
        }
        ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
        'btnSalvar', 'save', 200, $post_action);


        return ActionFactory::show_dialog($modalTitle, $defs);
    }



}


?>
