<?php


// require_once 'lib/tv/default_group.php';



class FavoritesGroup extends DefaultGroup
{
    private $tv;

    public function __construct(Tv $tv, $id, $title, $icon_url)
    {
        parent::__construct($id, $title, $icon_url);

        $this->tv = $tv;
    }

    public function is_favorite_channels()
    { return true; }

    public function get_channels(&$plugin_cookies)
    {
        $channels = array();

        $fav_channel_ids = $this->tv->get_fav_channel_ids();
        foreach ($fav_channel_ids as $channel_id)
            $channels[] = $this->tv->get_channel($channel_id);

        return $channels;
    }
}


?>
