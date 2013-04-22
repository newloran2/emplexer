<?php

interface Vod
{
    public function try_load_movie($movie_id, &$plugin_cookies);

    public function is_failed_movie_id($movie_id);
    public function get_cached_movie($movie_id);
    public function clear_movie_cache();

    public function get_vod_info(MediaURL $media_url, &$plugin_cookies);
    public function get_vod_stream_url($playback_url, &$plugin_cookies);

    public function get_buffering_ms();

    public function get_genre_ids();
    public function get_genre_caption($genre_id);
    public function get_genre_icon_url($genre_id);
    public function get_genre_media_url_str($genre_id);
    public function clear_genre_cache();

    public function get_search_media_url_str($pattern);

    public function get_vod_list_folder_views();
    public function get_vod_genres_folder_views();

    public function get_archive(MediaURL $media_url);

    // Hook.
    public function folder_entered(MediaURL $media_url, &$plugin_cookies);
}

?>
