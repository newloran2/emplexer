<?php


require_once 'lib/vod/vod.php';
require_once 'lib/abstract_preloaded_regular_screen.php';

class VodSeriesListScreen extends AbstractPreloadedRegularScreen
{
    const ID = 'vod_series';

    public static function get_media_url_str($movie_id)
    {
        return MediaURL::encode(
            array(
                'screen_id' => self::ID,
                'movie_id' => $movie_id));
    }

    

    protected $vod;
    
    public function get_id()
    {
        return self::ID;
    }

    public function __construct(Vod $vod)
    {
        $this->vod = $vod;

        parent::__construct($this->get_id(), $this->get_folder_views());
    }

    

    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        return array
        (
            GUI_EVENT_KEY_ENTER => ActionFactory::vod_play(),
            GUI_EVENT_KEY_PLAY  => ActionFactory::vod_play(),
        );
    }

    

    public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
    {
        $this->vod->folder_entered($media_url, $plugin_cookies);

        $movie = $this->vod->get_loaded_movie($media_url->movie_id, $plugin_cookies);
        if ($movie === null)
        {
            // TODO: dialog?
            return array();
        }

        $items = array();

        foreach ($movie->series_list as $series)
        {
            $items[] = array
            (
                PluginRegularFolderItem::media_url =>
                    MediaURL::encode(
                        array
                        (
                            'screen_id' => self::ID,
                            'movie_id' => $movie->id,
                            'series_id'  => $series->id,
                        )),
                PluginRegularFolderItem::caption => $series->name,
                PluginRegularFolderItem::view_item_params => array
                (
                    ViewItemParams::icon_path => 'gui_skin://small_icons/movie.aai',
                ),
            );
        }

        return $items;
    }

    

    protected function get_folder_views()
    {
        return array(
            array
            (
                PluginRegularFolderView::view_params => array
                (
                    ViewParams::num_cols => 1,
                    ViewParams::num_rows => Config::EPISODES_PER_PAGE,
                ),

                PluginRegularFolderView::base_view_item_params => array
                (
                    ViewItemParams::item_layout => HALIGN_LEFT,
                    ViewItemParams::icon_valign => VALIGN_CENTER,
                    ViewItemParams::icon_dx => 10,
                    ViewItemParams::icon_dy => -5,
                    ViewItemParams::item_caption_dx => 60,
                    ViewItemParams::icon_path => 'gui_skin://small_icons/movie.aai'
                ),

                PluginRegularFolderView::not_loaded_view_item_params => array (),
            ),
        );
    }

    public function get_archive(MediaURL $media_url)
    {
        return $this->vod->get_archive($media_url);
    }
}


?>
