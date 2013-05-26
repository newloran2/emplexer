<?php
namespace lib\dune\vod;

use lib\dune\AbstractPreloadedRegularScreen;
use lib\dune\ActionFactory;
use lib\dune\MediaURL;
use lib\dune\UserInputHandler;
use lib\dune\UserInputHandlerRegistry;
use PluginRegularFolderItem;
use ViewItemParams;


class VodGenresScreen extends AbstractPreloadedRegularScreen
    implements UserInputHandler
{
    const ID = 'vod_genres';

    public static function get_media_url_str()
    {
        return MediaURL::encode(array('screen_id' => self::ID));
    }

    

    private $vod;

    public function __construct(Vod $vod)
    {
        $this->vod = $vod;

        parent::__construct(self::ID,
            $vod->get_vod_genres_folder_views());

        UserInputHandlerRegistry::get_instance()->register_handler($this);
    }

    

    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        $select_genre_action =
            UserInputHandlerRegistry::create_action(
                $this, 'select_genre');

        return array
        (
            GUI_EVENT_KEY_ENTER => $select_genre_action,
        );
    }

    public function get_handler_id()
    { return self::ID; }

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        hd_print('Vod genres: handle_user_input:');
        foreach ($user_input as $key => $value)
            hd_print("  $key => $value");

        if ($user_input->control_id == 'select_genre')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $genre_id = $media_url->genre_id;
            $caption = $this->vod->get_genre_caption($genre_id);
            $media_url_str = $this->vod->get_genre_media_url_str($genre_id);

            return ActionFactory::open_folder($media_url_str, $caption);
        }

        return null;
    }

    

    public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
    {
        $this->vod->folder_entered($media_url, $plugin_cookies);

        $this->vod->ensure_genres_loaded($plugin_cookies);

        $genre_ids = $this->vod->get_genre_ids();

        $items = array();

        foreach ($genre_ids as $genre_id)
        {
            $caption = $this->vod->get_genre_caption($genre_id);
            $media_url_str = $this->vod->get_genre_media_url_str($genre_id);
            $icon_url = $this->vod->get_genre_icon_url($genre_id);

            $items[] = array
            (
                PluginRegularFolderItem::media_url => $media_url_str,
                PluginRegularFolderItem::caption => $caption,
                PluginRegularFolderItem::view_item_params => array
                (
                    ViewItemParams::icon_path => $icon_url,
                )
            );
        }

        return $items;
    }

    public function get_archive(MediaURL $media_url)
    {
        return $this->vod->get_archive($media_url);
    }
}

?>
