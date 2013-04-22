<?php


require_once 'lib/vod/vod.php';

abstract class AbstractVod implements Vod
{
    private $short_movie_by_id;

    private $movie_by_id;
    private $failed_movie_ids;

    private $favorites_supported;
    private $movie_page_supported;
    private $playback_url_is_stream_url;

    private $fav_movie_ids;
    private $fav_movie_ids_set;

    private $genres;

    protected function __construct(
        $favorites_supported,
        $movie_page_supported,
        $playback_url_is_stream_url)
    {
        $this->favorites_supported = $favorites_supported;
        $this->movie_page_supported = $movie_page_supported;
        $this->playback_url_is_stream_url = $playback_url_is_stream_url;

        $this->short_movie_by_id = array();
        $this->movie_by_id = array();
        $this->failed_movie_ids = array();
    }

    

    public function set_cached_short_movie(ShortMovie $short_movie)
    {
        $this->short_movie_by_id[$short_movie->id] = $short_movie;
    }

    public function set_cached_movie(Movie $movie)
    {
        $this->movie_by_id[$movie->id] = $movie;
        
        if ($this->has_cached_short_movie($movie->id) === false)
        {
            $this->set_cached_short_movie(
                new ShortMovie($movie->id, $movie->name, $movie->poster_url));
        }
    }

    public function set_failed_movie_id($movie_id)
    {
        $this->failed_movie_ids[$movie_id] = true;
    }

    public function set_fav_movie_ids($fav_movie_ids)
    {
        $this->fav_movie_ids = $fav_movie_ids;
        $this->fav_movie_ids_set = array();
        foreach ($this->fav_movie_ids as $id)
            $this->fav_movie_ids_set[$id] = true;
    }

    
    
    public function is_favorites_supported()
    {
        return $this->favorites_supported;
    }

    public function is_movie_page_supported()
    {
        return $this->movie_page_supported;
    }

    

    public function is_failed_movie_id($movie_id)
    {
        return isset($this->failed_movie_ids[$movie_id]);
    }

    public function has_cached_movie($movie_id)
    {
        return isset($this->movie_by_id[$movie_id]);
    }

    public function get_cached_movie($movie_id)
    {
        return isset($this->movie_by_id[$movie_id]) ?
            $this->movie_by_id[$movie_id] : null;
    }

    public function has_cached_short_movie($movie_id)
    {
        return isset($this->short_movie_by_id[$movie_id]);
    }

    public function get_cached_short_movie($movie_id)
    {
        return isset($this->short_movie_by_id[$movie_id]) ?
            $this->short_movie_by_id[$movie_id] : null;
    }

    public function clear_movie_cache()
    {
        $this->short_movie_by_id = array();
        $this->movie_by_id = array();
        $this->failed_movie_ids = array();

        $this->fav_movie_ids = null;
        $this->fav_movie_ids_set = array();
    }

    public function clear_genre_cache()
    {
        $this->genres = null;
    }

    
    
    public function ensure_movie_loaded($movie_id, &$plugin_cookies)
    {
        if (!isset($movie_id))
            throw new Exception('Movie ID is not set');

        if ($this->is_failed_movie_id($movie_id))
            return null;

        $movie = $this->get_cached_movie($movie_id);
        if ($movie === null)
            $this->try_load_movie($movie_id, $plugin_cookies);
    }

    public function get_loaded_movie($movie_id, &$plugin_cookies)
    {
        $this->ensure_movie_loaded($movie_id, $plugin_cookies);

        return $this->get_cached_movie($movie_id);
    }

    

    public function get_vod_info(MediaURL $media_url, &$plugin_cookies)
    {
        hd_print(__METHOD__ . ': ' . print_r($media_url, true));
        $movie = $this->get_loaded_movie($media_url->movie_id, $plugin_cookies);
        if ($movie === null)
        {
            // TODO: dialog?
            return null;
        }

        $sel_id = isset($media_url->series_id) ?
            $media_url->series_id : null;

        return $movie->get_vod_info($sel_id, $this->get_buffering_ms());
    }

    

    public function get_buffering_ms()
    {
        return 3000;
    }

    

    // This method should be overridden if and only if the
    // $playback_url_is_stream_url is false.
    public function get_vod_stream_url($playback_url, &$plugin_cookies)
    { throw new Exception('Not implemented.'); }

    
    
    // Favorites.
    
    /*
    // This function is responsible for the following:
    //  - $this->fav_movie_ids should be initialized with array of movie
    //  ids
    //  - each of these movie ids should have loaded ShortMovie in
    //  short_movie_by_id map.
    //  - exception should be thrown if error occured.
    protected function load_favorites(&$plugin_cookies)
    { throw new Exception('Not implemented'); }

    // This function should throw an exception if failed.
    protected function do_add_favorite_movie($movie_id, &$plugin_cookies)
    {}

    // This function should throw an exception if failed.
    protected function do_remove_favorite_movie($movie_id, &$plugin_cookies)
    {}

    // This function should not fail.
    protected function do_save_favorite_movies(&$fav_movie_ids, &$plugin_cookies)
    {}

    public function ensure_favorites_loaded(&$plugin_cookies)
    {
        if ($this->fav_movie_ids !== null)
            return;

        $this->load_favorites($plugin_cookies);

        if ($this->fav_movie_ids === null)
            throw new Exception('Illegal state: favorites not loaded.');
    }

    public function add_favorite_movie($movie_id, &$plugin_cookies)
    {
        if (isset($this->fav_movie_ids_set[$movie_id]))
            return false;

        $this->do_add_favorite_movie($movie_id, $plugin_cookies);

        hd_print('Added favorite movie ' . $movie_id);

        array_unshift($this->fav_movie_ids, $movie_id);
        $this->fav_movie_ids_set[$movie_id] = true;

        $this->do_save_favorite_movies($this->fav_movie_ids, $plugin_cookies);

        return true;
    }

    public function remove_favorite_movie($movie_id, &$plugin_cookies)
    {
        if (!isset($this->fav_movie_ids_set[$movie_id]))
            return false;

        $this->do_remove_favorite_movie($movie_id, $plugin_cookies);

        hd_print('Removed favorite movie ' . $movie_id);

        $k = array_search($movie_id, $this->fav_movie_ids);
        if ($k !== false)
            unset ($this->fav_movie_ids[$k]);
        unset ($this->fav_movie_ids_set[$movie_id]);

        $this->do_save_favorite_movies($this->fav_movie_ids, $plugin_cookies);

        return true;
    }
    
    public function get_favorite_movie_ids()
    {
        return $this->fav_movie_ids;
    }
    
    public function is_favorite_movie_id($movie_id)
    {
        return isset($this->fav_movie_ids_set[$movie_id]);
    }
    */
    
    // Genres.

    protected function load_genres(&$plugin_cookies)
    { throw new Exception("Not implemented."); }

    public function get_genre_icon_url($genre_id)
    { throw new Exception('Not implemented'); }

    public function get_genre_media_url_str($genre_id)
    { throw new Exception('Not implemented'); }

    public function ensure_genres_loaded(&$plugin_cookies)
    {
        if ($this->genres !== null)
            return;

        $this->genres = $this->load_genres($plugin_cookies);

        if ($this->genres === null)
            throw new Exception('Invalid VOD genres loaded');
    }

    public function get_genre_ids()
    {
        if ($this->genres === null)
            throw new Exception('VOD genres not loaded');

        return array_keys($this->genres);
    }

    public function get_genre_caption($genre_id)
    {
        if ($this->genres === null)
            throw new Exception('VOD genres not loaded');

        return $this->genres[$genre_id];
    }

    
    // Search.

    public function get_search_media_url_str($pattern)
    { throw new Exception('Not implemented'); }

    
    // Folder views.

    public function get_vod_list_folder_views()
    { throw new Exception('Not implemented'); }

    public function get_vod_genres_folder_views()
    { throw new Exception('Not implemented'); }

    
    // Archive.

    public function get_archive(MediaURL $media_url)
    { return null; }

    
    // Hook.

    public function folder_entered(MediaURL $media_url, &$plugin_cookies)
    { /* Nop */ }
}


?>
