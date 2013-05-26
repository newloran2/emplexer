<?php


// require_once 'lib/abstract_regular_screen.php';
namespace lib\dune\vod;

use GuiMenuItemDef;
use lib\dune\AbstractRegularScreen;
use lib\dune\ActionFactory;
use lib\dune\HD;
use lib\dune\MediaURL;
use lib\dune\UserInputHandler;
use lib\dune\UserInputHandlerRegistry;
use PluginRegularFolderItem;
use ViewItemParams;



abstract class VodListScreen extends AbstractRegularScreen
    implements UserInputHandler
{
    const ID = 'vod_list';

    private $vod;

    protected function __construct(Vod $vod)
    {
        parent::__construct(self::ID, $vod->get_vod_list_folder_views());

        $this->vod = $vod;

        UserInputHandlerRegistry::get_instance()->
            register_handler($this);
    }

    

    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        $actions = array();

        if ($this->vod->is_movie_page_supported())
            $actions[GUI_EVENT_KEY_ENTER] = ActionFactory::open_folder();
        else
            $actions[GUI_EVENT_KEY_ENTER] = ActionFactory::vod_play();

        if ($this->vod->is_favorites_supported())
        {
            $add_favorite_action =
                UserInputHandlerRegistry::create_action(
                    $this, 'add_favorite');
            $add_favorite_action['caption'] = 'Favorite';

            $popup_menu_action =
                UserInputHandlerRegistry::create_action(
                    $this, 'popup_menu');

            $actions[GUI_EVENT_KEY_D_BLUE] = $add_favorite_action;
            $actions[GUI_EVENT_KEY_POPUP_MENU] = $popup_menu_action;
        }

        return $actions;
    }

    public function get_handler_id()
    { return self::ID; }

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        hd_print('Vod favorites: handle_user_input:');
        foreach ($user_input as $key => $value)
            hd_print("  $key => $value");

        if ($user_input->control_id == 'popup_menu')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $movie_id = $media_url->movie_id;

            $is_favorite = $this->vod->is_favorite_movie_id($movie_id);
            $add_favorite_action =
                UserInputHandlerRegistry::create_action(
                    $this, 'add_favorite');
            $caption = 'Add to Favorites';
            $menu_items[] = array(
                GuiMenuItemDef::caption => $caption,
                GuiMenuItemDef::action => $add_favorite_action);

            return ActionFactory::show_popup_menu($menu_items);
        }
        else if ($user_input->control_id == 'add_favorite')
        {
            if (!isset($user_input->selected_media_url))
                return null;

            $media_url = MediaURL::decode($user_input->selected_media_url);
            $movie_id = $media_url->movie_id;

            $is_favorite = $this->vod->is_favorite_movie_id($movie_id);
            if ($is_favorite)
            {
                return ActionFactory::show_title_dialog(
                    'Movie already resides in Favorites');
            }
            else
            {
                $this->vod->add_favorite_movie($movie_id, $plugin_cookies);

                return ActionFactory::show_title_dialog(
                    'Movie has been added to Favorites');
            }
        }

        return null;
    }

    

    // Returns ShortMovieRange.
    protected abstract function get_short_movie_range(
        MediaURL $media_url, $from_ndx, &$plugin_cookies);

    public function get_folder_range(MediaURL $media_url, $from_ndx, &$plugin_cookies)
    {
        $movie_range = $this->get_short_movie_range(
            $media_url, $from_ndx, $plugin_cookies);

        $total = intval($movie_range->total);
        if ($total <= 0)
            return HD::create_regular_folder_range(array());

        $items = array();
        foreach ($movie_range->short_movies as $movie)
        {
            $items[] = array
            (
                PluginRegularFolderItem::media_url =>
                    VodMovieScreen::get_media_url_str($movie->id),
                PluginRegularFolderItem::caption => $movie->name,
                PluginRegularFolderItem::view_item_params => array
                (
                    ViewItemParams::icon_path => $movie->poster_url,
                )
            );
            
            $this->vod->set_cached_short_movie(
                new ShortMovie($movie->id, $movie->name, $movie->poster_url));
        }

        return HD::create_regular_folder_range(
            $items, $movie_range->from_ndx, $total);
    }

    public function get_archive(MediaURL $media_url)
    {
        return $this->vod->get_archive($media_url);
    }

    public function get_folder_view(MediaURL $media_url, &$plugin_cookies)
    {
        $this->vod->folder_entered($media_url, $plugin_cookies);

        return parent::get_folder_view($media_url, $plugin_cookies);
    }
}


?>
