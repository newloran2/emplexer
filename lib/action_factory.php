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

    public static function bluray_play($url)
    {
        return array
        (
            GuiAction::handler_string_id => BLURAY_PLAY_ACTION_ID,            
            GuiAction::data =>
             array
             (
                BlurayPlayActionData::url => $url,
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
                //se já existir a chave e o valor for nfs usa a chave que já existe se não pega o valor do plex
                if ($plugin_cookies->{$location->attributes()->path} && strpos($plugin_cookies->{$location->attributes()->path}, 'nfs://') !== false){
                    $value = $plugin_cookies->{$location->attributes()->path};      
                } else {
                    $value = 'nfs://'. $plugin_cookies->plexIp . ':' . (string)$location->attributes()->path;
                }   
                
                ControlFactory::add_text_field(
                    $defs, 
                    null, 
                    null,
                    $name            = (string)$location->attributes()->path, 
                    $title           = (string)$location->attributes()->path,  
                    $initial_value   = $value,  
                    $numeric         = false, 
                    $password        = false, 
                    $has_osk         = false, 
                    $always_active   = 0, 
                    $width           = 500
                );
            }
        }
        ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
        'saveAdvanceNfs', 'save', 200, $post_action);


        return ActionFactory::show_dialog($modalTitle, $defs);
    }



    public static function show_smb_advanced_configuration_modal($modalTitle, &$plugin_cookies,$post_action= null){
      
        $defs = array();
        $plexLocation = 'http://' . $plugin_cookies->plexIp . ':' . $plugin_cookies->plexPort . '/library/sections';
        $xml = HD::getAndParseXmlFromUrl($plexLocation);
        // hd_print(__METHOD__ . ';' . print_r($xml, true));
        foreach ($xml->Directory as $directory) {
            foreach ($directory->Location as $location) {
                //se já existir a chave e o valor for smb usa a chave que já existe se não pega o valor do plex
                if ($plugin_cookies->{$location->attributes()->path} && strpos($plugin_cookies->{$location->attributes()->path}, 'smb://') !== false){
                    $value = $plugin_cookies->{$location->attributes()->path};      
                } else {
                    $value = 'smb://user:password@'. $plugin_cookies->plexIp .  (string)$location->attributes()->path;
                }   
                 
                ControlFactory::add_text_field(
                    $defs, 
                    null, 
                    null,
                    $name            = (string)$location->attributes()->path, 
                    $title           = (string)$location->attributes()->path,  
                    $initial_value   = $value,
                    $numeric         = false, 
                    $password        = false, 
                    $has_osk         = false, 
                    $always_active   = 0, 
                    $width           = 500
                );
            }
        }
        ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
        'saveAdvanceSmb', 'save', 200, $post_action);


        return ActionFactory::show_dialog($modalTitle, $defs);
    }

     public static function show_subtitle_config_modal($modalTitle, &$plugin_cookies,$post_action= null){
      
        $defs = array();

        $languageCodes = array(
             "aa" => "Afar",
             "ab" => "Abkhazian",
             "ae" => "Avestan",
             "af" => "Afrikaans",
             "ak" => "Akan",
             "am" => "Amharic",
             "an" => "Aragonese",
             "ar" => "Arabic",
             "as" => "Assamese",
             "av" => "Avaric",
             "ay" => "Aymara",
             "az" => "Azerbaijani",
             "ba" => "Bashkir",
             "be" => "Belarusian",
             "bg" => "Bulgarian",
             "bh" => "Bihari",
             "bi" => "Bislama",
             "bm" => "Bambara",
             "bn" => "Bengali",
             "bo" => "Tibetan",
             "br" => "Breton",
             "bs" => "Bosnian",
             "ca" => "Catalan",
             "ce" => "Chechen",
             "ch" => "Chamorro",
             "co" => "Corsican",
             "cr" => "Cree",
             "cs" => "Czech",
             "cu" => "Church Slavic",
             "cv" => "Chuvash",
             "cy" => "Welsh",
             "da" => "Danish",
             "de" => "German",
             "dv" => "Divehi",
             "dz" => "Dzongkha",
             "ee" => "Ewe",
             "el" => "Greek",
             "en" => "English",
             "eo" => "Esperanto",
             "es" => "Spanish",
             "et" => "Estonian",
             "eu" => "Basque",
             "fa" => "Persian",
             "ff" => "Fulah",
             "fi" => "Finnish",
             "fj" => "Fijian",
             "fo" => "Faroese",
             "fr" => "French",
             "fy" => "Western Frisian",
             "ga" => "Irish",
             "gd" => "Scottish Gaelic",
             "gl" => "Galician",
             "gn" => "Guarani",
             "gu" => "Gujarati",
             "gv" => "Manx",
             "ha" => "Hausa",
             "he" => "Hebrew",
             "hi" => "Hindi",
             "ho" => "Hiri Motu",
             "hr" => "Croatian",
             "ht" => "Haitian",
             "hu" => "Hungarian",
             "hy" => "Armenian",
             "hz" => "Herero",
             "ia" => "Interlingua (International Auxiliary Language Association)",
             "id" => "Indonesian",
             "ie" => "Interlingue",
             "ig" => "Igbo",
             "ii" => "Sichuan Yi",
             "ik" => "Inupiaq",
             "io" => "Ido",
             "is" => "Icelandic",
             "it" => "Italian",
             "iu" => "Inuktitut",
             "ja" => "Japanese",
             "jv" => "Javanese",
             "ka" => "Georgian",
             "kg" => "Kongo",
             "ki" => "Kikuyu",
             "kj" => "Kwanyama",
             "kk" => "Kazakh",
             "kl" => "Kalaallisut",
             "km" => "Khmer",
             "kn" => "Kannada",
             "ko" => "Korean",
             "kr" => "Kanuri",
             "ks" => "Kashmiri",
             "ku" => "Kurdish",
             "kv" => "Komi",
             "kw" => "Cornish",
             "ky" => "Kirghiz",
             "la" => "Latin",
             "lb" => "Luxembourgish",
             "lg" => "Ganda",
             "li" => "Limburgish",
             "ln" => "Lingala",
             "lo" => "Lao",
             "lt" => "Lithuanian",
             "lu" => "Luba-Katanga",
             "lv" => "Latvian",
             "mg" => "Malagasy",
             "mh" => "Marshallese",
             "mi" => "Maori",
             "mk" => "Macedonian",
             "ml" => "Malayalam",
             "mn" => "Mongolian",
             "mr" => "Marathi",
             "ms" => "Malay",
             "mt" => "Maltese",
             "my" => "Burmese",
             "na" => "Nauru",
             "nb" => "Norwegian Bokmal",
             "nd" => "North Ndebele",
             "ne" => "Nepali",
             "ng" => "Ndonga",
             "nl" => "Dutch",
             "nn" => "Norwegian Nynorsk",
             "no" => "Norwegian",
             "nr" => "South Ndebele",
             "nv" => "Navajo",
             "ny" => "Chichewa",
             "oc" => "Occitan",
             "oj" => "Ojibwa",
             "om" => "Oromo",
             "or" => "Oriya",
             "os" => "Ossetian",
             "pa" => "Panjabi",
             "pi" => "Pali",
             "pl" => "Polish",
             "ps" => "Pashto",
             "pt" => "Portuguese",
             "qu" => "Quechua",
             "rm" => "Raeto-Romance",
             "rn" => "Kirundi",
             "ro" => "Romanian",
             "ru" => "Russian",
             "rw" => "Kinyarwanda",
             "sa" => "Sanskrit",
             "sc" => "Sardinian",
             "sd" => "Sindhi",
             "se" => "Northern Sami",
             "sg" => "Sango",
             "si" => "Sinhala",
             "sk" => "Slovak",
             "sl" => "Slovenian",
             "sm" => "Samoan",
             "sn" => "Shona",
             "so" => "Somali",
             "sq" => "Albanian",
             "sr" => "Serbian",
             "ss" => "Swati",
             "st" => "Southern Sotho",
             "su" => "Sundanese",
             "sv" => "Swedish",
             "sw" => "Swahili",
             "ta" => "Tamil",
             "te" => "Telugu",
             "tg" => "Tajik",
             "th" => "Thai",
             "ti" => "Tigrinya",
             "tk" => "Turkmen",
             "tl" => "Tagalog",
             "tn" => "Tswana",
             "to" => "Tonga",
             "tr" => "Turkish",
             "ts" => "Tsonga",
             "tt" => "Tatar",
             "tw" => "Twi",
             "ty" => "Tahitian",
             "ug" => "Uighur",
             "uk" => "Ukrainian",
             "ur" => "Urdu",
             "uz" => "Uzbek",
             "ve" => "Venda",
             "vi" => "Vietnamese",
             "vo" => "Volapuk",
             "wa" => "Walloon",
             "wo" => "Wolof",
             "xh" => "Xhosa",
             "yi" => "Yiddish",
             "yo" => "Yoruba",
             "za" => "Zhuang",
             "zh" => "Chinese",
             "zu" => "Zulu"
            );
        ControlFactory::add_combobox(
            $defs,
            null,
            null,
            $name                   =  'preferredLanguage',
            $title                  =  'Preferred language',
            $initial_value          =  $value,
            $value_caption_pairs    =  $languageCodes,
            $width                  =  500,
            $need_confirm           =  false,
            $need_apply             =  false
        );  



        $plexLocation = 'http://' . $plugin_cookies->plexIp . ':' . $plugin_cookies->plexPort . '/library/sections';
        $xml = HD::getAndParseXmlFromUrl($plexLocation);
        // hd_print(__METHOD__ . ';' . print_r($xml, true));
        foreach ($xml->Directory as $directory) {
            foreach ($directory->Location as $location) {
                //se já existir a chave e o valor for nfs usa a chave que já existe se não pega o valor do plex
                if ($plugin_cookies->{$location->attributes()->path} && strpos($plugin_cookies->{$location->attributes()->path}, 'nfs://') !== false){
                    $value = $plugin_cookies->{$location->attributes()->path};      
                } else {
                    $value = 'nfs://'. $plugin_cookies->plexIp . ':' . (string)$location->attributes()->path;
                }   
                
                ControlFactory::add_text_field(
                    $defs, 
                    null, 
                    null,
                    $name            = (string)$location->attributes()->path, 
                    $title           = (string)$location->attributes()->path,  
                    $initial_value   = $value,  
                    $numeric         = false, 
                    $password        = false, 
                    $has_osk         = false, 
                    $always_active   = 0, 
                    $width           = 500
                );
            }
        }
        ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
        'saveAdvanceNfs', 'save', 200, $post_action);


        return ActionFactory::show_dialog($modalTitle, $defs);
    }



}


?>
