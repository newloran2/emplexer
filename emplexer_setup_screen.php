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
			$timeToMark   = isset($plugin_cookies->timeToMark) ? $plugin_cookies->timeToMark : DEFAULT_TIME_TO_MARK;
			$connectionMethod   = isset($plugin_cookies->connectionMethod) ? $plugin_cookies->connectionMethod : HTTP_CONNECTION_TYPE;
			
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


			$comboOpts = array();
			$comboOpts['http'] = 'HTTP';
			$comboOpts['nfs']  = 'NFS';
			$comboOpts['smb']  = 'SMB';


			$this->add_combobox(
				$defs,
        		$name		   			= 'connectionMethod',
        		$title		   			= 'Connection Type',
        		$initial_value 			= $connectionMethod,
        		$value_caption_pairs 	= $comboOpts,
        		$width					= 300,
        		$need_confirm 			= false, 
        		$need_apply = true
        	);


			$colorSeenNames = array();
			$colorSeenNames['000000'] = 'black';
			$colorSeenNames['0000A0'] = 'blue';
			$colorSeenNames['C0E0C0'] = 'light green';
			$colorSeenNames['A0C0FF'] = 'light blue';
			$colorSeenNames['FF4040'] = 'red';
			$colorSeenNames['C0FF40'] = 'lemon green';
			$colorSeenNames['FFE040'] = 'yellow';
			$colorSeenNames['C0C0C0'] = 'grey';
			$colorSeenNames['808080'] = 'dark grey';
			$colorSeenNames['4040C0'] = 'purple';
			$colorSeenNames['40FF40'] = 'light green';
			$colorSeenNames['40FFFF'] = 'cyan';
			$colorSeenNames['FF8040'] = 'orange';
			$colorSeenNames['FF40FF'] = 'pink';
			$colorSeenNames['FFFF40'] = 'light yellow';
			$colorSeenNames['FFFFE0'] = 'very light yellow?' ;
			$colorSeenNames['404040'] = 'Tundora';
			$colorSeenNames['AAAAA0'] = 'mid grey';
			$colorSeenNames['FFFF00'] = 'very light yellow?';
			$colorSeenNames['50FF50'] = 'other green :)';
			$colorSeenNames['5080FF'] = 'other blue :)';
			$colorSeenNames['FF5030'] = 'other red :)';
			$colorSeenNames['E0E0E0'] = 'other grey :)';


			$this->add_combobox(
				$defs,
        		$name		   			= 'hasSeenCaptionColor',
        		$title		   			= 'Has Seen caption Color',
        		$initial_value 			= 'black',
        		$value_caption_pairs 	= $colorSeenNames,
        		$width					= 300,
        		$need_confirm 			= false, 
        		$need_apply = true
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
			$plugin_cookies->plexIp           = $user_input->plexIp;
			$plugin_cookies->plexPort         = $user_input->plexPort;
			$plugin_cookies->username         = $user_input->plexPort;
			$plugin_cookies->connectionMethod = $user_input->connectionMethod ;
			return ActionFactory::show_title_dialog('Configuration successfully saved.');
		}
	}
	?>