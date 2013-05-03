<?php

// require_once 'lib/tv/tv.php';
// require_once 'lib/abstract_preloaded_regular_screen.php';

class TvFavoritesScreen extends AbstractPreloadedRegularScreen
    implements UserInputHandler
{
    const ID = 'tv_favorites';

    public static function get_media_url_str()
    {
        return MediaURL::encode(
            array(
                'screen_id' => self::ID,
                'is_favorites' => true));
    }

    

    private $tv;

    public function __construct(Tv $tv, $folder_views)
    {
        $this->tv = $tv;

        parent::__construct(self::ID, $folder_views);

        UserInputHandlerRegistry::get_instance()->register_handler($this);
    }

    

    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        $move_backward_favorite_action =
            UserInputHandlerRegistry::create_action(
                $this, 'move_backward_favorite');
        $move_backward_favorite_action['caption'] = 'Backward';

        $move_forward_favorite_action =
            UserInputHandlerRegistry::create_action(
                $this, 'move_forward_favorite');
        $move_forward_favorite_action['caption'] = 'Forward';

        $remove_favorite_action =
            UserInputHandlerRegistry::create_action(
                $this, 'remove_favorite');
        $remove_favorite_action['caption'] = 'Favorite';

        $menu_items[] = array(
            GuiMenuItemDef::caption => 'Remove from Favorites',
            GuiMenuItemDef::action => $remove_favorite_action);

        $popup_menu_action = ActionFactory::show_popup_menu($menu_items);

        return array
        (
            GUI_EVENT_KEY_ENTER => ActionFactory::tv_play(),
            GUI_EVENT_KEY_PLAY  => ActionFactory::tv_play(),
            GUI_EVENT_KEY_B_GREEN => $move_backward_favorite_action,
            GUI_EVENT_KEY_C_YELLOW => $move_forward_favorite_action,
            GUI_EVENT_KEY_D_BLUE => $remove_favorite_action,
            GUI_EVENT_KEY_POPUP_MENU => $popup_menu_action,
        );
    }

    public function get_handler_id()
    { return self::ID; }

    private function get_update_action($sel_increment,
        &$user_input, &$plugin_cookies)
    {
        $parent_media_url = MediaURL::decode($user_input->parent_media_url);

        $num_favorites = 
            count($this->tv->get_fav_channel_ids($plugin_cookies));

        $sel_ndx = $user_input->sel_ndx + $sel_increment;
        if ($sel_ndx < 0)
            $sel_ndx = 0;
        if ($sel_ndx >= $num_favorites)
            $sel_ndx = $num_favorites - 1;

        $range = HD::create_regular_folder_range(
            $this->get_all_folder_items(
                $parent_media_url, $plugin_cookies));
        return ActionFactory::update_regular_folder(
            $range, true, $sel_ndx);
    }

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        hd_print('Tv favorites: handle_user_input:');
        foreach ($user_input as $key => $value)
            hd_print("  $key => $value");

        if ($user_input->control_id == 'move_backward_favorite')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $channel_id = $media_url->channel_id;

            $this->tv->change_tv_favorites(PLUGIN_FAVORITES_OP_MOVE_UP,
                $channel_id, $plugin_cookies);

            return $this->get_update_action(-1, $user_input, $plugin_cookies);
        }
        else if ($user_input->control_id == 'move_forward_favorite')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $channel_id = $media_url->channel_id;

            $this->tv->change_tv_favorites(PLUGIN_FAVORITES_OP_MOVE_DOWN,
                $channel_id, $plugin_cookies);

            return $this->get_update_action(1, $user_input, $plugin_cookies);
        }
        else if ($user_input->control_id == 'remove_favorite')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $channel_id = $media_url->channel_id;

            $this->tv->change_tv_favorites(PLUGIN_FAVORITES_OP_REMOVE,
                $channel_id, $plugin_cookies);

            return $this->get_update_action(0, $user_input, $plugin_cookies);
        }

        return null;
    }

    

    public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
    {
        $this->tv->folder_entered($media_url, $plugin_cookies);

        $fav_channel_ids = $this->tv->get_fav_channel_ids($plugin_cookies);

        $items = array();

        foreach ($fav_channel_ids as $channel_id)
        {
            if (preg_match('/^\s*$/', $channel_id))
                continue;

            try
            {
                $c = $this->tv->get_channel($channel_id);
            }
            catch (Exception $e)
            {
                hd_print("Warning: channel '$channel_id' not found.");
                continue;
            }

            array_push($items,
                array
                (
                    PluginRegularFolderItem::media_url =>
                        MediaURL::encode(
                            array(
                                'channel_id' => $c->get_id(),
                                'group_id' => '__favorites')),
                    PluginRegularFolderItem::caption => $c->get_title(),
                    PluginRegularFolderItem::view_item_params => array
                    (
                        ViewItemParams::icon_path => $c->get_icon_url(),
                        ViewItemParams::item_detailed_icon_path => $c->get_icon_url(),
                    ),
                    PluginRegularFolderItem::starred => false,
                ));
        }

        return $items;
    }

    public function get_archive(MediaURL $media_url)
    {
        return $this->tv->get_archive($media_url);
    }
}

?>
