

# This is an autogenerated file! DO NOT change it manually.

# Hard-coded constants.
define('VALIGN_TOP',       0);
define('VALIGN_CENTER',    1);
define('VALIGN_BOTTOM',    2);

define('HALIGN_LEFT',      0);
define('HALIGN_CENTER',    1);
define('HALIGN_RIGHT',     2);

define('FONT_SIZE_NORMAL', 0);
define('FONT_SIZE_SMALL',  1);

# enum GuiControlKind
define ('GUI_CONTROL_LABEL',                             'label');
define ('GUI_CONTROL_COMBOBOX',                          'combobox');
define ('GUI_CONTROL_TEXT_FIELD',                        'text_field');
define ('GUI_CONTROL_BUTTON',                            'button');
define ('GUI_CONTROL_VGAP',                              'vgap');

# enum GuiEventKind
define ('GUI_EVENT_KEY_ENTER',                           'key_enter');
define ('GUI_EVENT_KEY_PLAY',                            'key_play');
define ('GUI_EVENT_KEY_A_RED',                           'key_a_red');
define ('GUI_EVENT_KEY_B_GREEN',                         'key_b_green');
define ('GUI_EVENT_KEY_C_YELLOW',                        'key_c_yellow');
define ('GUI_EVENT_KEY_D_BLUE',                          'key_d_blue');
define ('GUI_EVENT_KEY_POPUP_MENU',                      'key_popup_menu');
define ('GUI_EVENT_KEY_INFO',                            'key_info');
define ('GUI_EVENT_TIMER',                               'timer');
define ('GUI_EVENT_PLAYBACK_STOP',                       'playback_stop');

# enum PluginEpgMode
define ('PLUGIN_EPG_DISABLED',                           'disabled');
define ('PLUGIN_EPG_USE_DAY_REQUEST',                    'use_day_request');
define ('PLUGIN_EPG_GET_FROM_STREAM',                    'get_from_stream');

# enum PluginFavoritesOpType
define ('PLUGIN_FAVORITES_OP_ADD',                       'add');
define ('PLUGIN_FAVORITES_OP_REMOVE',                    'remove');
define ('PLUGIN_FAVORITES_OP_MOVE_UP',                   'move_up');
define ('PLUGIN_FAVORITES_OP_MOVE_DOWN',                 'move_down');

# enum PluginFolderViewKind
define ('PLUGIN_FOLDER_VIEW_REGULAR',                    'view_regular');
define ('PLUGIN_FOLDER_VIEW_CONTROLS',                   'view_controls');
define ('PLUGIN_FOLDER_VIEW_MOVIE',                      'view_movie');

# enum PluginFontSize
define ('PLUGIN_FONT_NORMAL',                            'normal');
define ('PLUGIN_FONT_SMALL',                             'small');

# enum PluginOperationType
define ('PLUGIN_OP_GET_FOLDER_VIEW',                     'get_folder_view');
define ('PLUGIN_OP_GET_NEXT_FOLDER_VIEW',                'get_next_folder_view');
define ('PLUGIN_OP_GET_REGULAR_FOLDER_ITEMS',            'get_regular_folder_items');
define ('PLUGIN_OP_HANDLE_USER_INPUT',                   'handle_user_input');
define ('PLUGIN_OP_GET_TV_INFO',                         'get_tv_info');
define ('PLUGIN_OP_GET_DAY_EPG',                         'get_day_epg');
define ('PLUGIN_OP_GET_TV_PLAYBACK_URL',                 'get_tv_playback_url');
define ('PLUGIN_OP_GET_TV_STREAM_URL',                   'get_tv_stream_url');
define ('PLUGIN_OP_CHANGE_TV_FAVORITES',                 'change_tv_favorites');
define ('PLUGIN_OP_GET_VOD_INFO',                        'get_vod_info');
define ('PLUGIN_OP_GET_VOD_STREAM_URL',                  'get_vod_stream_url');

# enum PluginOutputDataType
define ('PLUGIN_OUT_DATA_PLUGIN_FOLDER_VIEW',            'plugin_folder_view');
define ('PLUGIN_OUT_DATA_PLUGIN_REGULAR_FOLDER_RANGE',   'plugin_regular_folder_range');
define ('PLUGIN_OUT_DATA_GUI_ACTION',                    'gui_action');
define ('PLUGIN_OUT_DATA_PLUGIN_TV_INFO',                'plugin_tv_info');
define ('PLUGIN_OUT_DATA_PLUGIN_TV_EPG_PROGRAM_LIST',    'plugin_tv_epg_program_list');
define ('PLUGIN_OUT_DATA_URL',                           'url');
define ('PLUGIN_OUT_DATA_PLUGIN_VOD_INFO',               'plugin_vod_info');

# GUI-action ids.
define ('BLURAY_PLAY_ACTION_ID',                         'bluray_play');
define ('CHANGE_BEHAVIOUR_ACTION_ID',                    'change_behaviour');
define ('CLOSE_DIALOG_AND_RUN_ACTION_ID',                'close_dialog_and_run');
define ('DVD_PLAY_ACTION_ID',                            'dvd_play');
define ('FILE_PLAY_ACTION_ID',                           'file_play');
define ('LAUNCH_MEDIA_URL_ACTION_ID',                    'launch_media_url');
define ('PLAYLIST_PLAY_ACTION_ID',                       'playlist_play');
define ('PLUGIN_CLEAR_ARCHIVE_CACHE_ACTION_ID',          'plugin_clear_archive_cache');
define ('PLUGIN_HANDLE_USER_INPUT_ACTION_ID',            'plugin_handle_user_input');
define ('PLUGIN_INVALIDATE_FOLDERS_ACTION_ID',           'plugin_invalidate_folders');
define ('PLUGIN_OPEN_FOLDER_ACTION_ID',                  'plugin_open_folder');
define ('PLUGIN_RUN_NATIVE_CODE_ACTION_ID',              'plugin_run_native_code');
define ('PLUGIN_SHOW_ERROR_ACTION_ID',                   'plugin_show_error');
define ('PLUGIN_TV_PLAY_ACTION_ID',                      'plugin_tv_play');
define ('PLUGIN_UPDATE_FOLDER_ACTION_ID',                'plugin_update_folder');
define ('PLUGIN_VOD_PLAY_ACTION_ID',                     'plugin_vod_play');
define ('RESET_CONTROLS_ACTION_ID',                      'reset_controls');
define ('SHOW_BLACK_SCREEN_ACTION_ID',                   'show_black_screen');
define ('SHOW_DIALOG_ACTION_ID',                         'show_dialog');
define ('SHOW_MAIN_SCREEN_ACTION_ID',                    'show_main_screen');
define ('SHOW_POPUP_MENU_ACTION_ID',                     'show_popup_menu');
define ('STATUS_ACTION_ID',                              'status');

class BlurayPlayActionData
{
    const /* (char *)                         */ url                              = 'url';
}

class ChangeBehaviourActionData
{
    const /* (GuiActionMap *)                 */ actions                          = 'actions';
    const /* (GuiTimerDef *)                  */ timer                            = 'timer';
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class CloseDialogAndRunActionData
{
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class DvdPlayActionData
{
    const /* (char *)                         */ url                              = 'url';
}

class FilePlayActionData
{
    const /* (char *)                         */ url                              = 'url';
}

class GuiAction
{
    const /* (char *)                         */ handler_string_id                = 'handler_string_id';
    const /* (void *)                         */ data                             = 'data';
    const /* (char *)                         */ caption                          = 'caption';
    const /* (MY_Properties *)                */ params                           = 'params';
}

class GuiButtonDef
{
    const /* (char *)                         */ caption                          = 'caption';
    const /* int                              */ width                            = 'width';
    const /* (GuiAction *)                    */ push_action                      = 'push_action';
}

class GuiComboboxDef
{
    const /* (char *)                         */ initial_value                    = 'initial_value';
    const /* (MY_Properties *)                */ value_caption_pairs              = 'value_caption_pairs';
    const /* int                              */ width                            = 'width';
    const /* (GuiAction *)                    */ apply_action                     = 'apply_action';
    const /* (GuiAction *)                    */ confirm_action                   = 'confirm_action';
}

class GuiControlDef
{
    const /* (char *)                         */ name                             = 'name';
    const /* (char *)                         */ title                            = 'title';
    const /* GuiControlKind                   */ kind                             = 'kind';
    const /* (void *)                         */ specific_def                     = 'specific_def';
    const /* (MY_Properties *)                */ params                           = 'params';
}

class GuiLabelDef
{
    const /* (char *)                         */ caption                          = 'caption';
}

class GuiMenuItemDef
{
    const /* MY_Bool                          */ is_separator                     = 'is_separator';
    const /* (char *)                         */ caption                          = 'caption';
    const /* (char *)                         */ icon_url                         = 'icon_url';
    const /* (GuiAction *)                    */ action                           = 'action';
}

class GuiTextFieldDef
{
    const /* (char *)                         */ initial_value                    = 'initial_value';
    const /* MY_Bool                          */ numeric                          = 'numeric';
    const /* MY_Bool                          */ password                         = 'password';
    const /* MY_Bool                          */ has_osk                          = 'has_osk';
    const /* MY_Bool                          */ always_active                    = 'always_active';
    const /* int                              */ width                            = 'width';
    const /* (GuiAction *)                    */ apply_action                     = 'apply_action';
    const /* (GuiAction *)                    */ confirm_action                   = 'confirm_action';
}

class GuiTimerDef
{
    const /* int                              */ delay_ms                         = 'delay_ms';
}

class GuiVGapDef
{
    const /* int                              */ vgap                             = 'vgap';
}

class LaunchMediaUrlActionData
{
    const /* (char *)                         */ url                              = 'url';
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class PlaylistPlayActionData
{
    const /* (char *)                         */ url                              = 'url';
    const /* int                              */ start_index                      = 'start_index';
}

class PluginArchiveDef
{
    const /* (char *)                         */ id                               = 'id';
    const /* (MY_Properties *)                */ urls_with_keys                   = 'urls_with_keys';
    const /* (char *)                         */ all_tgz_url                      = 'all_tgz_url';
    const /* long long                        */ total_size                       = 'total_size';
}

class PluginClearArchiveCacheActionData
{
    const /* (char *)                         */ archive_id                       = 'archive_id';
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class PluginControlsFolderView
{
    const /* (GuiControlDefList *)            */ defs                             = 'defs';
    const /* int                              */ initial_sel_ndx                  = 'initial_sel_ndx';
    const /* (PluginFolderViewParams *)       */ params                           = 'params';
}

class PluginFolderView
{
    const /* PluginFolderViewKind             */ view_kind                        = 'view_kind';
    const /* (void *)                         */ data                             = 'data';
    const /* MY_Bool                          */ multiple_views_supported         = 'multiple_views_supported';
    const /* (PluginArchiveDef *)             */ archive                          = 'archive';
}

class PluginFolderViewParams
{
    const /* MY_Bool                          */ paint_path_box                   = 'paint_path_box';
    const /* MY_Bool                          */ paint_content_box_background     = 'paint_content_box_background';
    const /* (char *)                         */ background_url                   = 'background_url';
}

class PluginInvalidateFoldersActionData
{
    const /* (MY_StringArray *)               */ media_urls                       = 'media_urls';
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class PluginMovie
{
    const /* (char *)                         */ name                             = 'name';
    const /* (char *)                         */ name_original                    = 'name_original';
    const /* (char *)                         */ description                      = 'description';
    const /* (char *)                         */ poster_url                       = 'poster_url';
    const /* int                              */ length_min                       = 'length_min';
    const /* int                              */ year                             = 'year';
    const /* (char *)                         */ directors_str                    = 'directors_str';
    const /* (char *)                         */ scenarios_str                    = 'scenarios_str';
    const /* (char *)                         */ actors_str                       = 'actors_str';
    const /* (char *)                         */ genres_str                       = 'genres_str';
          $a['data'] = PluginMovieFolderView:const /* (char *)                         */ rate_imdb                        = 'rate_imdb';
    const /* (char *)                         */ rate_kinopoisk                   = 'rate_kinopoisk';
    const /* (char *)                         */ rate_mpaa                        = 'rate_mpaa';
    const /* (char *)                         */ country                          = 'country';
    const /* (char *)                         */ budget                           = 'budget';
    const /* (MY_Properties *)                */ details                          = 'details';
    const /* (MY_Properties *)                */ rate_details                     = 'rate_details';
}

class PluginMovieFolderView
{
    const /* (PluginMovie *)                  */ movie                            = 'movie';
    const /* (char *)                         */ left_button_caption              = 'left_button_caption';
    const /* (GuiAction *)                    */ left_button_action               = 'left_button_action';
    const /* MY_Bool                          */ has_right_button                 = 'has_right_button';
    const /* (char *)                         */ right_button_caption             = 'right_button_caption';
    const /* (GuiAction *)                    */ right_button_action              = 'right_button_action';
    const /* MY_Bool                          */ has_multiple_series              = 'has_multiple_series';
    const /* (char *)                         */ series_media_url                 = 'series_media_url';
    const /* (PluginFolderViewParams *)       */ params                           = 'params';
}

class PluginOpenFolderActionData
{
    const /* (char *)                         */ caption                          = 'caption';
    const /* (char *)                         */ media_url                        = 'media_url';
}

class PluginOutputData
{
    const /* MY_Bool                          */ has_data                         = 'has_data';
    const /* PluginOutputDataType             */ data_type                        = 'data_type';
    const /* (void *)                         */ data                             = 'data';
    const /* (MY_Properties *)                */ plugin_cookies                   = 'plugin_cookies';
    const /* MY_Bool                          */ is_error                         = 'is_error';
    const /* (GuiAction *)                    */ error_action                     = 'error_action';
}

class PluginRegularFolderItem
{
    const /* (char *)                         */ media_url                        = 'media_url';
    const /* (char *)                         */ caption                          = 'caption';
    const /* MY_Bool                          */ starred                          = 'starred';
    const /* (ViewItemParams *)               */ view_item_params                 = 'view_item_params';
}

class PluginRegularFolderRange
{
    const /* int                              */ total                            = 'total';
    const /* MY_Bool                          */ more_items_available             = 'more_items_available';
    const /* int                              */ from_ndx                         = 'from_ndx';
    const /* int                              */ count                            = 'count';
    const /* (PluginRegularFolderItemList *)  */ items                            = 'items';
}

class PluginRegularFolderView
{
    const /* (ViewParams *)                   */ view_params                      = 'view_params';
    const /* (ViewItemParams *)               */ base_view_item_params            = 'base_view_item_params';
    const /* (ViewItemParams *)               */ not_loaded_view_item_params      = 'not_loaded_view_item_params';
    const /* (GuiActionMap *)                 */ actions                          = 'actions';
    const /* (GuiTimerDef *)                  */ timer                            = 'timer';
    const /* MY_Bool                          */ async_icon_loading               = 'async_icon_loading';
    const /* (PluginRegularFolderRange *)     */ initial_range                    = 'initial_range';
}

class PluginShowErrorActionData
{
    const /* MY_Bool                          */ fatal                            = 'fatal';
    const /* (char *)                         */ title                            = 'title';
    const /* (MY_StringArray *)               */ msg_lines                        = 'msg_lines';
}

class PluginTvChannel
{
    const /* (char *)                         */ id                               = 'id';
    const /* (char *)                         */ caption                          = 'caption';
    const /* (MY_StringArray *)               */ group_ids                        = 'group_ids';
    const /* (char *)                         */ icon_url                         = 'icon_url';
    const /* int                              */ number                           = 'number';
    const /* MY_Bool                          */ have_archive                     = 'have_archive';
    const /* MY_Bool                          */ is_protected                     = 'is_protected';
    const /* MY_Bool                          */ recording_enabled                = 'recording_enabled';
    const /* int                              */ buffering_ms                     = 'buffering_ms';
    const /* int                              */ timeshift_hours                  = 'timeshift_hours';
    const /* int                              */ past_epg_days                    = 'past_epg_days';
    const /* int                              */ future_epg_days                  = 'future_epg_days';
    const /* int                              */ archive_past_sec                 = 'archive_past_sec';
    const /* int                              */ archive_delay_sec                = 'archive_delay_sec';
    const /* MY_Bool                          */ playback_url_is_stream_url       = 'playback_url_is_stream_url';
}

class PluginTvEpgProgram
{
    const /* time_t                           */ start_tm_sec                     = 'start_tm_sec';
    const /* time_t                           */ end_tm_sec                       = 'end_tm_sec';
    const /* (char *)                         */ name                             = 'name';
    const /* (char *)                         */ description                      = 'description';
}

class PluginTvGroup
{
    const /* (char *)                         */ id                               = 'id';
    const /* (char *)                         */ caption                          = 'caption';
    const /* (char *)                         */ icon_url                         = 'icon_url';
}

class PluginTvInfo
{
    const /* time_t                           */ server_time                      = 'server_time';
    const /* (PluginTvGroupList *)            */ groups                           = 'groups';
    const /* (PluginTvChannelList *)          */ channels                         = 'channels';
    const /* MY_Bool                          */ show_group_channels_only         = 'show_group_channels_only';
    const /* MY_Bool                          */ favorites_supported              = 'favorites_supported';
    const /* (MY_StringArray *)               */ favorite_channel_ids             = 'favorite_channel_ids';
    const /* (char *)                         */ favorites_icon_url               = 'favorites_icon_url';
    const /* (char *)                         */ initial_group_id                 = 'initial_group_id';
    const /* (char *)                         */ initial_channel_id               = 'initial_channel_id';
    const /* MY_Bool                          */ initial_is_favorite              = 'initial_is_favorite';
    const /* int                              */ initial_archive_tm               = 'initial_archive_tm';
    const /* MY_Bool                          */ ip_address_required              = 'ip_address_required';
    const /* MY_Bool                          */ valid_time_required              = 'valid_time_required';
    const /* PluginEpgMode                    */ epg_mode                         = 'epg_mode';
    const /* PluginFontSize                   */ epg_font_size                    = 'epg_font_size';
    const /* (PluginArchiveDef *)             */ archive                          = 'archive';
}

class PluginTvPlayActionData
{
    const /* (char *)                         */ initial_group_id                 = 'initial_group_id';
    const /* (char *)                         */ initial_channel_id               = 'initial_channel_id';
    const /* MY_Bool                          */ initial_is_favorite              = 'initial_is_favorite';
    const /* int                              */ initial_archive_tm               = 'initial_archive_tm';
}

class PluginUpdateFolderActionData
{
    const /* (PluginRegularFolderRange *)     */ range                            = 'range';
    const /* MY_Bool                          */ need_refresh                     = 'need_refresh';
    const /* int                              */ sel_ndx                          = 'sel_ndx';
}

class PluginVodInfo
{
    const /* (char *)                         */ id                               = 'id';
    const /* (char *)                         */ name                             = 'name';
    const /* (char *)                         */ description                      = 'description';
    const /* (char *)                         */ poster_url                       = 'poster_url';
    const /* (PluginVodSeriesInfoList *)      */ series                           = 'series';
    const /* int                              */ initial_series_ndx               = 'initial_series_ndx';
    const /* int                              */ initial_position_ms              = 'initial_position_ms';
    const /* int                              */ buffering_ms                     = 'buffering_ms';
    const /* MY_Bool                          */ advert_mode                      = 'advert_mode';
    const /* (GuiActionMap *)                 */ actions                          = 'actions';
    const /* (GuiTimerDef *)                  */ timer                            = 'timer';
    const /* MY_Bool                          */ ip_address_required              = 'ip_address_required';
    const /* MY_Bool                          */ valid_time_required              = 'valid_time_required';
}

class PluginVodPlayActionData
{
    const /* (PluginVodInfo *)                */ vod_info                         = 'vod_info';
}

class PluginVodSeriesInfo
{
    const /* (char *)                         */ name                             = 'name';
    const /* (char *)                         */ playback_url                     = 'playback_url';
    const /* MY_Bool                          */ playback_url_is_stream_url       = 'playback_url_is_stream_url';
}

class ResetControlsActionData
{
    const /* (GuiControlDefList *)            */ defs                             = 'defs';
    const /* int                              */ initial_sel_ndx                  = 'initial_sel_ndx';
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class ShowBlackScreenActionData
{
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class ShowDialogActionData
{
    const /* (char *)                         */ title                            = 'title';
    const /* (GuiControlDefList *)            */ defs                             = 'defs';
    const /* (GuiActionMap *)                 */ actions                          = 'actions';
    const /* (GuiTimerDef *)                  */ timer                            = 'timer';
    const /* MY_Bool                          */ close_by_return                  = 'close_by_return';
    const /* int                              */ preferred_width                  = 'preferred_width';
    const /* int                              */ max_height                       = 'max_height';
    const /* int                              */ initial_sel_ndx                  = 'initial_sel_ndx';
}

class ShowMainScreenActionData
{
    const /* (GuiAction *)                    */ post_action                      = 'post_action';
}

class ShowPopupMenuActionData
{
    const /* (GuiMenuItemDefList *)           */ menu_items                       = 'menu_items';
    const /* int                              */ selected_menu_item_index         = 'selected_menu_item_index';
}

class StatusActionData
{
    const /* int                              */ status                           = 'status';
}

class ViewItemParams
{
    const /* MY_Bool                          */ predefined                       = 'predefined';
    const /* MY_Bool                          */ item_paint_icon                  = 'item_paint_icon';
    const /* MY_Bool                          */ item_paint_caption               = 'item_paint_caption';
    const /* MY_FontSize                      */ item_caption_font_size           = 'item_caption_font_size';
    const /* MY_Bool                          */ item_paint_caption_within_icon   = 'item_paint_caption_within_icon';
    const /* (char *)                         */ item_caption_within_icon_color   = 'item_caption_within_icon_color';
    const /* NonNegativeInt                   */ item_padding_top                 = 'item_padding_top';
    const /* NonNegativeInt                   */ item_padding_bottom              = 'item_padding_bottom';
    const /* ImagePath                        */ icon_path                        = 'icon_path';
    const /* NonNegativeInt                   */ icon_width                       = 'icon_width';
    const /* NonNegativeInt                   */ icon_height                      = 'icon_height';
    const /* ArbitraryInt                     */ icon_dx                          = 'icon_dx';
    const /* ArbitraryInt                     */ icon_dy                          = 'icon_dy';
    const /* PositiveDouble                   */ icon_scale_factor                = 'icon_scale_factor';
    const /* ArbitraryInt                     */ icon_margin_top                  = 'icon_margin_top';
    const /* ArbitraryInt                     */ icon_margin_bottom               = 'icon_margin_bottom';
    const /* ArbitraryInt                     */ icon_margin_left                 = 'icon_margin_left';
    const /* ArbitraryInt                     */ icon_margin_right                = 'icon_margin_right';
    const /* ImagePath                        */ icon_sel_path                    = 'icon_sel_path';
    const /* NonNegativeInt                   */ icon_sel_width                   = 'icon_sel_width';
    const /* NonNegativeInt                   */ icon_sel_height                  = 'icon_sel_height';
    const /* ArbitraryInt                     */ icon_sel_dx                      = 'icon_sel_dx';
    const /* ArbitraryInt                     */ icon_sel_dy                      = 'icon_sel_dy';
    const /* PositiveDouble                   */ icon_sel_scale_factor            = 'icon_sel_scale_factor';
    const /* ArbitraryInt                     */ icon_sel_margin_top              = 'icon_sel_margin_top';
    const /* ArbitraryInt                     */ icon_sel_margin_bottom           = 'icon_sel_margin_bottom';
    const /* ArbitraryInt                     */ icon_sel_margin_left             = 'icon_sel_margin_left';
    const /* ArbitraryInt                     */ icon_sel_margin_right            = 'icon_sel_margin_right';
    const /* MY_VAlign                        */ icon_valign                      = 'icon_valign';
    const /* MY_HAlign                        */ item_layout                      = 'item_layout';
    const /* MY_Bool                          */ icon_keep_aspect_ratio           = 'icon_keep_aspect_ratio';
    const /* NonNegativeInt                   */ item_caption_width               = 'item_caption_width';
    const /* MY_Bool                          */ item_caption_wrap_enabled        = 'item_caption_wrap_enabled';
    const /* ArbitraryInt                     */ item_caption_dy                  = 'item_caption_dy';
    const /* ArbitraryInt                     */ item_caption_sel_dy              = 'item_caption_sel_dy';
    const /* ArbitraryInt                     */ item_caption_dx                  = 'item_caption_dx';
    const /* NonNegativeInt                   */ item_caption_color               = 'item_caption_color';
    const /* (char *)                         */ item_detailed_info               = 'item_detailed_info';
    const /* ImagePath                        */ item_detailed_icon_path          = 'item_detailed_icon_path';
    const /* PositiveDouble                   */ item_sandwich_icon_scale_factor  = 'item_sandwich_icon_scale_factor';
}

class ViewParams
{
    const /* PositiveInt                      */ num_cols                         = 'num_cols';
    const /* PositiveInt                      */ num_rows                         = 'num_rows';
    const /* PositiveInt                      */ item_width                       = 'item_width';
    const /* PositiveInt                      */ item_height                      = 'item_height';
    const /* MY_Bool                          */ animation_enabled                = 'animation_enabled';
    const /* ImagePath                        */ background_path                  = 'background_path';
    const /* NonNegativeInt                   */ background_x                     = 'background_x';
    const /* NonNegativeInt                   */ background_y                     = 'background_y';
    const /* PositiveInt                      */ background_width                 = 'background_width';
    const /* PositiveInt                      */ background_height                = 'background_height';
    const /* MY_ViewBackgroundOrder           */ background_order                 = 'background_order';
    const /* ImagePath                        */ scroll_path                      = 'scroll_path';
    const /* NonNegativeInt                   */ scroll_x                         = 'scroll_x';
    const /* NonNegativeInt                   */ scroll_y                         = 'scroll_y';
    const /* PositiveInt                      */ scroll_height                    = 'scroll_height';
    const /* ImagePath                        */ mark_path                        = 'mark_path';
    const /* ArbitraryInt                     */ mark_dx                          = 'mark_dx';
    const /* ArbitraryInt                     */ mark_dy                          = 'mark_dy';
    const /* PositiveDouble                   */ mark_scale_factor                = 'mark_scale_factor';
    const /* MY_Bool                          */ optimize_full_screen_background  = 'optimize_full_screen_background';
    const /* MY_Bool                          */ paint_path_box                   = 'paint_path_box';
    const /* MY_Bool                          */ paint_content_box_background     = 'paint_content_box_background';
    const /* MY_Bool                          */ paint_scrollbar                  = 'paint_scrollbar';
    const /* MY_Bool                          */ paint_icon_selection_box         = 'paint_icon_selection_box';
    const /* MY_Bool                          */ paint_help_line                  = 'paint_help_line';
    const /* MY_Bool                          */ paint_details                    = 'paint_details';
    const /* MY_Bool                          */ paint_details_box_background     = 'paint_details_box_background';
    const /* NonNegativeInt                   */ help_line_text_color             = 'help_line_text_color';
    const /* ArbitraryInt                     */ icon_selection_box_dx            = 'icon_selection_box_dx';
    const /* ArbitraryInt                     */ icon_selection_box_dy            = 'icon_selection_box_dy';
    const /* NonNegativeInt                   */ icon_selection_box_width         = 'icon_selection_box_width';
    const /* NonNegativeInt                   */ icon_selection_box_height        = 'icon_selection_box_height';
    const /* MY_Bool                          */ paint_icon_badge_box             = 'paint_icon_badge_box';
    const /* ArbitraryInt                     */ icon_badge_box_dx                = 'icon_badge_box_dx';
    const /* ArbitraryInt                     */ icon_badge_box_dy                = 'icon_badge_box_dy';
    const /* NonNegativeInt                   */ icon_badge_box_width             = 'icon_badge_box_width';
    const /* NonNegativeInt                   */ icon_badge_box_height            = 'icon_badge_box_height';
    const /* ArbitraryInt                     */ icon_badge_box_sel_dx            = 'icon_badge_box_sel_dx';
    const /* ArbitraryInt                     */ icon_badge_box_sel_dy            = 'icon_badge_box_sel_dy';
    const /* NonNegativeInt                   */ icon_badge_box_sel_width         = 'icon_badge_box_sel_width';
    const /* NonNegativeInt                   */ icon_badge_box_sel_height        = 'icon_badge_box_sel_height';
    const /* NonNegativeInt                   */ content_box_x                    = 'content_box_x';
    const /* NonNegativeInt                   */ content_box_y                    = 'content_box_y';
    const /* PositiveInt                      */ content_box_width                = 'content_box_width';
    const /* PositiveInt                      */ content_box_height               = 'content_box_height';
    const /* NonNegativeInt                   */ content_box_padding_left         = 'content_box_padding_left';
    const /* NonNegativeInt                   */ content_box_padding_top          = 'content_box_padding_top';
    const /* NonNegativeInt                   */ content_box_padding_right        = 'content_box_padding_right';
    const /* NonNegativeInt                   */ content_box_padding_bottom       = 'content_box_padding_bottom';
    const /* MY_Bool                          */ paint_item_info_in_details       = 'paint_item_info_in_details';
    const /* MY_Bool                          */ zoom_detailed_icon               = 'zoom_detailed_icon';
    const /* PositiveDouble                   */ detailed_icon_scale_factor       = 'detailed_icon_scale_factor';
    const /* (char *)                         */ item_small_icon_name             = 'item_small_icon_name';
    const /* (char *)                         */ folder_small_icon_name           = 'folder_small_icon_name';
    const /* MY_FontSize                      */ item_detailed_info_font_size     = 'item_detailed_info_font_size';
    const /* NonNegativeInt                   */ item_detailed_info_rel_y         = 'item_detailed_info_rel_y';
    const /* NonNegativeInt                   */ item_detailed_info_title_color   = 'item_detailed_info_title_color';
    const /* NonNegativeInt                   */ item_detailed_info_text_color    = 'item_detailed_info_text_color';
    const /* MY_Bool                          */ paint_sandwich                   = 'paint_sandwich';
    const /* (char *)                         */ sandwich_base                    = 'sandwich_base';
    const /* (char *)                         */ sandwich_mask                    = 'sandwich_mask';
    const /* (char *)                         */ sandwich_cover                   = 'sandwich_cover';
    const /* NonNegativeInt                   */ sandwich_width                   = 'sandwich_width';
    const /* NonNegativeInt                   */ sandwich_height                  = 'sandwich_height';
    const /* NonNegativeInt                   */ sandwich_sel_width               = 'sandwich_sel_width';
    const /* NonNegativeInt                   */ sandwich_sel_height              = 'sandwich_sel_height';
    const /* PositiveDouble                   */ sandwich_icon_scale_factor       = 'sandwich_icon_scale_factor';
    const /* MY_Bool                          */ sandwich_icon_keep_aspect_ratio  = 'sandwich_icon_keep_aspect_ratio';
    const /* MY_Bool                          */ sandwich_icon_upscale_enabled    = 'sandwich_icon_upscale_enabled';
}

?>
