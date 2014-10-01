<?php


require_once 'lib/tv/default_group.php';



class AllChannelsGroup extends DefaultGroup
{
    private $tv;

    public function __construct(Tv $tv, $title, $icon_url)
    {
        parent::__construct(
            $tv->get_all_channel_group_id(), $title, $icon_url);

        $this->tv = $tv;
    }

    public function is_all_channels()
    { return true; }

    public function get_channels(&$plugin_cookies)
    {
        return $this->tv->get_channels();
    }
}


?>
