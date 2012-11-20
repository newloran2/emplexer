<?php

require_once 'lib/vod/vod.php';
require_once 'lib/abstract_preloaded_regular_screen.php';

class VodFavoritesScreen extends AbstractPreloadedRegularScreen
    implements UserInputHandler
{
    const ID = 'vod_favorites';

    public static function get_media_url_str()
    {
        return MediaURL::encode(array('screen_id' => self::ID));
    }

    

    private $vod;

    public function __construct(Vod $vod)
    {
        $this->vod = $vod;

        parent::__construct(self::ID,
            $vod->get_vod_list_folder_views());

        UserInputHandlerRegistry::get_instance()->register_handler($this);
    }

    

    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        $actions = array();

        if ($this->vod->is_movie_page_supported())
            $actions[GUI_EVENT_KEY_ENTER] = ActionFactory::open_folder();
        else
            $actions[GUI_EVENT_KEY_ENTER] = ActionFactory::vod_play();

        $remove_favorite_action =
            UserInputHandlerRegistry::create_action(
                $this, 'remove_favorite');
        $remove_favorite_action['caption'] = 'Favorite';

        $menu_items[] = array(
            GuiMenuItemDef::caption => 'Remove from Favorites',
            GuiMenuItemDef::action => $remove_favorite_action);

        $popup_menu_action = ActionFactory::show_popup_menu($menu_items);

        $actions[GUI_EVENT_KEY_D_BLUE] = $remove_favorite_action;
        $actions[GUI_EVENT_KEY_POPUP_MENU] = $popup_menu_action;

        return $actions;
    }

    public function get_handler_id()
    { return self::ID; }

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        hd_print('Vod favorites: handle_user_input:');
        foreach ($user_input as $key => $value)
            hd_print("  $key => $value");

        if ($user_input->control_id == 'remove_favorite')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $movie_id = $media_url->movie_id;

            $this->vod->remove_favorite_movie($movie_id, $plugin_cookies);

            return ActionFactory::invalidate_folders(
                array(
                    self::get_media_url_str($movie_id)));
        }

        return null;
    }

    

    public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
    {
        $this->vod->folder_entered($media_url, $plugin_cookies);

        $this->vod->ensure_favorites_loaded($plugin_cookies);

        $movie_ids = $this->vod->get_favorite_movie_ids();

        $items = array();

        foreach ($movie_ids as $movie_id)
        {
            $short_movie = $this->vod->get_cached_short_movie($movie_id);
            if (is_null($short_movie))
            {
                $caption = "#$movie_id";
                $poster_url = "missing://";
            }
            else
            {
                $caption = $short_movie->name;
                $poster_url = $short_movie->poster_url;
            }

            $items[] = array
            (
                PluginRegularFolderItem::media_url =>
                    VodMovieScreen::get_media_url_str($movie_id),
                PluginRegularFolderItem::caption => $caption,
                PluginRegularFolderItem::view_item_params => array
                (
                    ViewItemParams::icon_path => $poster_url,
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
