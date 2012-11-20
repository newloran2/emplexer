<?php 
require_once 'lib/abstract_controls_screen.php';


	/**
	 * 
	 */
	class EmplexerSetupScreen extends AbstractControlsScreen
	{
		const ID = 'emplexer_setup_screen';

		function __construct()
		{
			parent::__construct(self::ID);
			hd_print('teste setup screen');
		}

		public function do_get_control_defs(&$plugin_cookies){
			$defs = array();
			$plexIp     = isset($plugin_cookies->plexIp)   ? $plugin_cookies->plexIp   : '';
			$plexPort   = isset($plugin_cookies->plexPort) ? $plugin_cookies->plexPort : EmplexerConfig::DEFAULT_PLEX_PORT;
			
			$this->add_text_field(
				$defs,
				$name          = "plexIp",
				$title         = "Plex IP",
				$initial_value = $plexIp,
				$numeric       = false,
				$password      = false,
				$has_osk       = false,
				$always_active = 0,
				$width         = 500 
			);

			$this->add_text_field(
				$defs,
				$name          = "plexPort",
				$title         = "Plex Port",
				$initial_value = $plexPort,
				$numeric       = true,
				$password      = false,
				$has_osk       = false,
				$always_active = 0,
				$width         = 500     
			);

			$this->add_button(
				$defs,
				$name          = "btnSalvar",
				$title         = "salvar",
				$caption	   = "salvar",
				$width         =  200     
			);


			return $defs;
		}

		/**
		 * Obtain definition for control screen
		 * @param  MediaURL $media_url      source media_url
		 * @param  none   $plugin_cookies data has straged
		 * @return array
		 */
		public function get_control_defs(MediaURL $media_url, &$plugin_cookies)
		{
			hd_print(__METHOD__);
			return $this->do_get_control_defs($plugin_cookies);
		}

		public function handle_user_input(&$user_input, &$plugin_cookies )	
		{
			hd_print(__METHOD__  . ': ' . print_r($user_input, true) ."\n" .  print_r($plugin_cookies, true));
			$plugin_cookies->plexIp    = $user_input->plexIp;
			$plugin_cookies->plexPort  = $user_input->plexPort;
			$plugin_cookies->username = $user_input->plexPort;
			hd_print(__METHOD__  . ': ' .  print_r($plugin_cookies, true));

			$action = ActionFactory::reset_controls($this->do_get_control_defs($plugin_cookies));
			return ActionFactory::invalidate_folders(null, $action);
		}
	}
	?>