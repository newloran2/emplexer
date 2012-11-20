<?php
///////////////////////////////////////////////////////////////////////////

interface DunePlugin
{
    // PluginFolderView
    public function get_folder_view(
        /* [in]     String                      */  $media_url,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // PluginFolderView
    public function get_next_folder_view(
        /* [in]     String                      */  $media_url,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // PluginTvInfo
    public function get_tv_info(
        /* [in]     String                      */  $media_url,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // String
    public function get_tv_stream_url(
        /* [in]     String                      */  $media_url,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // PluginVodInfo 
    public function get_vod_info(
        /* [in]     String                      */  $media_url,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // String
    public function get_vod_stream_url(
        /* [in]     String                      */  $media_url,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // PluginRegularFolderRange 
    public function get_regular_folder_items(
        /* [in]     String                      */  $media_url,
        /* [in]     int                         */  $from_ndx,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // List<PluginTvEpgProgram>
    public function get_day_epg(
        /* [in]     String                      */  $channel_id,
        /* [in]     int                         */  $day_start_tm_sec,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // String 
    public function get_tv_playback_url(
        /* [in]     String                      */  $channel_id,
        /* [in]     int                         */  $archive_tm_sec,
        /* [in]     String                      */  $protect_code,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // GuiAction
    public function change_tv_favorites(
        /* [in]     String                      */  $op_type,
        /* [in]     String                      */  $channel_id,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);

    // GuiAction
    public function handle_user_input(
        /* [in]     Map: Key -> Value           */  &$user_input,
        /* [inout]  Map: Key -> Value           */  &$plugin_cookies);
}

///////////////////////////////////////////////////////////////////////////
?>
