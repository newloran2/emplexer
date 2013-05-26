<?php


// require_once 'lib/abstract_controls_screen.php';

namespace lib\dune\vod;

use lib\dune\AbstractControlsScreen;
use lib\dune\ActionFactory;
use lib\dune\MediaURL;



class VodSearchScreen extends AbstractControlsScreen
{
    const ID = 'vod_search';

    public static function get_media_url_str()
    {
        return MediaURL::encode(array('screen_id' => self::ID));
    }

    

    protected $vod;

    public function __construct(Vod $vod)
    {
        parent::__construct(self::ID);

        $this->vod = $vod;
    }

    private function do_get_control_defs(&$plugin_cookies)
    {
        $pattern = '';
        if (isset($plugin_cookies->vod_search_pattern))
            $pattern = $plugin_cookies->vod_search_pattern;

        $defs = array();

        $this->add_label($defs, null,
            "Enter part of movie name:");

        $this->add_text_field($defs, 'pattern', null,
            $pattern, false, false, true, false,
            500, true, true);

        return $defs;
    }

    public function get_control_defs(MediaURL $media_url, &$plugin_cookies)
    {
        $this->vod->folder_entered($media_url, $plugin_cookies);

        return $this->do_get_control_defs($plugin_cookies);
    }

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        hd_print('Vod search: handle_user_input:');
        foreach ($user_input as $key => $value)
            hd_print("  $key => $value");

        if ($user_input->action_type === 'apply')
        {
            $control_id = $user_input->control_id;

            if ($control_id === 'pattern')
            {
                $pattern = $user_input->pattern;

                $plugin_cookies->vod_search_pattern = $pattern;

                hd_print("Vod search: applying pattern '$pattern'");

                $defs = $this->do_get_control_defs($plugin_cookies);

                return ActionFactory::reset_controls($defs,
                    ActionFactory::open_folder(
                        $this->vod->get_search_media_url_str($pattern),
                        $pattern));
            }
        }
        else if ($user_input->action_type === 'confirm')
        {
            $control_id = $user_input->control_id;
            $new_value = $user_input->{$control_id};

            if ($control_id === 'pattern')
            {
                $pattern = $user_input->pattern;
                if (preg_match('/^\s*$/', $pattern))
                {
                    return ActionFactory::show_error(false,
                        'Pattern should not be empty');
                }
            }
        }

        return null;
    }
}


?>
