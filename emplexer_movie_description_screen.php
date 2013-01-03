<?php 


// require_once 'lib/screen.php';
require_once 'lib/vod/vod_movie_screen.php';

class EmplexerMovieDescriptionScreen extends VodMovieScreen  {

    const ID='emplexer_movie_desction_screen';

    
    public function get_id(){
        hd_print(__METHOD__);
        return self::ID;
    }

 	public static function get_media_url_str()
    {
        hd_print(__METHOD__);
        return MediaURL::encode(
            array
            (
                'screen_id' => self::ID,
            )
        );
        
    }

    public function get_action_map(MediaURL $media_url, &$plugin_cookies){
        hd_print(__METHOD__);
        return null;
    }

    public function get_all_folder_items(MediaURL $media_url , &$plugin_cookies){
        hd_print(__METHOD__);
        return $this->get_folder_view($media_url, $plugin_cookies);
    }

	public function get_folder_view(MediaURL $media_url, &$plugin_cookies)
	{
        hd_print(__METHOD__);
		$movie =  array(
            PluginMovie::name => 'Air Gear',
            // PluginMovie::name_original => ,
            PluginMovie::description => 'This is where it all begins. Ikki gets into a fight with a group of Storm Riders who use ATs. After he loses, Ikki sees the mysterious Simca. After returning home, he curiously takes a pair of Mikans ATs. After accidentally challenging another Storm Rider group, Ikki is thrust into an Air Trek match.',
            PluginMovie::poster_url => 'http://192.168.2.9:32400/library/metadata/18684/thumb/1351251873',
            PluginMovie::length_min => 23,
            PluginMovie::year => 2006,
            PluginMovie::directors_str => 'Hajime Kamegaki',
            // PluginMovie::scenarios_str => $this->scenarios_str,
            // PluginMovie::actors_str => $this->actors_str,
            PluginMovie::genres_str => 'Animation,Drama,',
            PluginMovie::rate_imdb => 9,
            // PluginMovie::rate_kinopoisk => $this->rate_kinopoisk,
            PluginMovie::rate_mpaa => 'TV-G',
            // PluginMovie::country => $this->country,
            // PluginMovie::budget => $this->budget
        );


		$movie_folder_view = array
        (
            PluginMovieFolderView::movie => $movie,
            PluginMovieFolderView::has_right_button => true,
            PluginMovieFolderView::right_button_caption => 'teste',
            // PluginMovieFolderView::right_button_action => ,
            PluginMovieFolderView::has_multiple_series => false,
                //(count($movie->series_list) > 1),
            // PluginMovieFolderView::series_media_url =>
                // VideomoreVodSeriesListScreen::get_media_url_str($movie->id),
        );

        $folder_view = array
        (
            PluginFolderView::multiple_views_supported  => false,
            PluginFolderView::archive                   => null,
            PluginFolderView::view_kind                 => PLUGIN_FOLDER_VIEW_MOVIE,
            PluginFolderView::data                      => $movie_folder_view,
        );
        hd_print(__METHOD__ . ':'. print_r($folder_view, true));

        return $folder_view;


	}

    public function handle_user_input(&$user_input, &$plugin_cookies){
        hd_print(__METHOD__);
        hd_print(print_r($user_input, true));
        hd_print(print_r($plugin_cookies, true));

    }
}


?>
