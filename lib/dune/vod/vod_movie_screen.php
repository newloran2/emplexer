<?php


// require_once 'lib/screen.php';
// require_once 'lib/vod/vod.php';
// require_once 'lib/vod/vod_series_list_screen.php';

class VodMovieScreen implements Screen, UserInputHandler
{
    const ID = 'vod_movie';

    public static function get_media_url_str($movie_id)
    {
        return MediaURL::encode(
            array(
                'screen_id' => self::ID,
                'movie_id' => $movie_id));
    }

    

    protected $vod;

    public function __construct(Vod $vod)
    {
        $this->vod = $vod;

        UserInputHandlerRegistry::get_instance()->
            register_handler($this);
    }

    

    public function get_id()
    { return self::ID; }

    public function get_handler_id()
    { return self::ID; }

    

    public function get_folder_view(MediaURL $media_url, &$plugin_cookies)
    {
        $this->vod->folder_entered($media_url, $plugin_cookies);

        $movie = $this->vod->get_loaded_movie($media_url->movie_id, $plugin_cookies);
        if ($movie === null)
        {
            // TODO: dialog?
            return null;
        }

        $has_right_button = $this->vod->is_favorites_supported();
        $right_button_caption = null;
        $right_button_action = null;
        if ($has_right_button)
        {
            $this->vod->ensure_favorites_loaded($plugin_cookies);

            $is_favorite = $this->vod->is_favorite_movie_id($movie->id);
            $right_button_caption = $is_favorite ?
                'Remove from Favorites' : 'Add to Favorites';
            $right_button_action = UserInputHandlerRegistry::create_action(
                $this, 'favorites',
                array('movie_id' => $movie->id));
        }

        $movie_folder_view = array
        (
            PluginMovieFolderView::movie => $movie->get_movie_array(),
            PluginMovieFolderView::has_right_button => $has_right_button,
            PluginMovieFolderView::right_button_caption => $right_button_caption,
            PluginMovieFolderView::right_button_action => $right_button_action,
            PluginMovieFolderView::has_multiple_series => (count($movie->series_list) > 1),
            PluginMovieFolderView::series_media_url => VodSeriesListScreen::get_media_url_str($movie->id),
            // PluginMovieFolderView::params => array(
            //         PluginFolderViewParams::background_url => 'http://192.168.2.9:32400/library/metadata/351/art/1343089927'
            //     )

        );

        $a = 
        array
        (
            PluginFolderView::multiple_views_supported  => false,
            PluginFolderView::archive                   => null,
            PluginFolderView::view_kind                 => PLUGIN_FOLDER_VIEW_MOVIE,
            PluginFolderView::data                      => $movie_folder_view,
        );
        HD::print_backtrace();
        hd_print(__METHOD__ . ':' . print_r($a, true));

        return $a;

    }

    

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        hd_print('Movie: handle_user_input:');
        foreach ($user_input as $key => $value)
            hd_print("  $key => $value");

        if ($user_input->control_id == 'favorites')
        {
            $movie_id = $user_input->movie_id;

            $is_favorite = $this->vod->is_favorite_movie_id($movie_id);
            if ($is_favorite)
                $this->vod->remove_favorite_movie($movie_id, $plugin_cookies);
            else
                $this->vod->add_favorite_movie($movie_id, $plugin_cookies);

            $message = $is_favorite ?
                'Movie has been removed from Favorites' :
                'Movie has been added to Favorites';

            return ActionFactory::show_title_dialog($message,
                ActionFactory::invalidate_folders(
                    array(
                        self::get_media_url_str($movie_id),
                        VodFavoritesScreen::get_media_url_str(),
                    )));
        }

        return null;
    }
}


?>
