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
			hd_print(__METHOD__  . print_r($plugin_cookies, true));
			$defs = array();
			$plexIp     = isset($plugin_cookies->plexIp)   ? $plugin_cookies->plexIp   : '';
			$plexPort   = isset($plugin_cookies->plexPort) ? $plugin_cookies->plexPort : EmplexerConfig::DEFAULT_PLEX_PORT;
			$timeToMark   = isset($plugin_cookies->timeToMark) ? $plugin_cookies->timeToMark : DEFAULT_TIME_TO_MARK;
			$connectionMethod   = isset($plugin_cookies->connectionMethod) ? $plugin_cookies->connectionMethod : HTTP_CONNECTION_TYPE;
			$notSeenCaptionColor   = isset($plugin_cookies->notSeenCaptionColor) ? $plugin_cookies->notSeenCaptionColor : DEFAULT_NOT_SEEN_CAPTION_COLOR;
			$hasSeenCaptionColor   = isset($plugin_cookies->hasSeenCaptionColor) ? $plugin_cookies->hasSeenCaptionColor : DEFAULT_HAS_SEEN_CAPTION_COLOR;
			$userName   = isset($plugin_cookies->userName) ? $plugin_cookies->userName : "";
			$passWord   = isset($plugin_cookies->password) ? $plugin_cookies->password : "";
			
			hd_print("password = $passWord userName = $userName" );
			
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

			if ($connectionMethod == 'smb'){

	        	$this->add_text_field(
					$defs,
					$name          = "userName",
					$title         = "User Name",
					$initial_value = $userName,
					$numeric       = false,
					$password      = false,
					$has_osk       = false,
					$always_active = 0,
					$width         = 500     
				);

				$this->add_text_field(
					$defs,
					$name          = "password",
					$title         = "Password",
					$initial_value = $passWord,
					$numeric       = false,
					$password      = true,
					$has_osk       = false,
					$always_active = 0,
					$width         = 500     
				);
			}



			$colorSeenNames = array();
			$colorSeenNames[] = 'white';
			$colorSeenNames[] = 'black';
			$colorSeenNames[] = 'blue';
			$colorSeenNames[] = 'light green';
			$colorSeenNames[] = 'light blue';
			$colorSeenNames[] = 'red';
			$colorSeenNames[] = 'lemon green';
			$colorSeenNames[] = 'yellow';
			$colorSeenNames[] = 'grey';
			$colorSeenNames[] = 'dark grey';
			$colorSeenNames[] = 'purple';
			$colorSeenNames[] = 'light green';
			$colorSeenNames[] = 'cyan';
			$colorSeenNames[] = 'orange';
			$colorSeenNames[] = 'pink';
			$colorSeenNames[] = 'light yellow';
			$colorSeenNames[] = 'very light yellow?' ;
			$colorSeenNames[] = 'Tundora';
			$colorSeenNames[] = 'mid grey';
			$colorSeenNames[] = 'very light yellow?';
			$colorSeenNames[] = 'other green :)';
			$colorSeenNames[] = 'other blue :)';
			$colorSeenNames[] = 'other red :)';
			$colorSeenNames[] = 'other grey :)';

// 			$colorSeenNames['000000'] = 'black';
// 			$colorSeenNames['0000A0'] = 'blue';
// 			$colorSeenNames['C0E0C0'] = 'light green';
// 			$colorSeenNames['A0C0FF'] = 'light blue';
// 			$colorSeenNames['FF4040'] = 'red';
// 			$colorSeenNames['C0FF40'] = 'lemon green';
// 			$colorSeenNames['FFE040'] = 'yellow';
// 			$colorSeenNames['C0C0C0'] = 'grey';
// 			$colorSeenNames['808080'] = 'dark grey';
// 			$colorSeenNames['4040C0'] = 'purple';
// 			$colorSeenNames['40FF40'] = 'light green';
// 			$colorSeenNames['40FFFF'] = 'cyan';
// 			$colorSeenNames['FF8040'] = 'orange';
// 			$colorSeenNames['FF40FF'] = 'pink';
// 			$colorSeenNames['FFFF40'] = 'light yellow';
// 			$colorSeenNames['FFFFE0'] = 'very light yellow?' ;
// 			$colorSeenNames['404040'] = 'Tundora';
// 			$colorSeenNames['AAAAA0'] = 'mid grey';
// 			$colorSeenNames['FFFF00'] = 'very light yellow?';
// 			$colorSeenNames['50FF50'] = 'other green :)';
// 			$colorSeenNames['5080FF'] = 'other blue :)';
// 			$colorSeenNames['FF5030'] = 'other red :)';
// 			$colorSeenNames['E0E0E0'] = 'other grey :)';

			$this->add_combobox(
				$defs,
        		$name		   			= 'notSeenCaptionColor',
        		$title		   			= 'Not Seen caption Color',
        		$initial_value 			= $notSeenCaptionColor,
        		$value_caption_pairs 	= $colorSeenNames,
        		$width					= 300,
        		$need_confirm 			= false, 
        		$need_apply = true
        	);

			$this->add_combobox(
				$defs,
        		$name		   			= 'hasSeenCaptionColor',
        		$title		   			= 'Has Seen caption Color',
        		$initial_value 			= $hasSeenCaptionColor,
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
			EmplexerSetupScreen::savePreferences($user_input, $plugin_cookies);
			return ActionFactory::reset_controls($this->do_get_control_defs($plugin_cookies));
		}


		public static function savePreferences(&$user_input, &$plugin_cookies){
			hd_print("user_input = "  . print_r($user_input, true));
			hd_print("plugin_cookies = "  . print_r($plugin_cookies, true));

			$plugin_cookies->plexIp           = $user_input->plexIp;
			$plugin_cookies->plexPort         = $user_input->plexPort;
			$plugin_cookies->connectionMethod = $user_input->connectionMethod ? $user_input->connectionMethod : HTTP_CONNECTION_TYPE ;
			$plugin_cookies->hasSeenCaptionColor = $user_input->hasSeenCaptionColor ? $user_input->hasSeenCaptionColor : DEFAULT_HAS_SEEN_CAPTION_COLOR ;
			$plugin_cookies->notSeenCaptionColor = $user_input->notSeenCaptionColor ? $user_input->notSeenCaptionColor : DEFAULT_NOT_SEEN_CAPTION_COLOR;

			if ($user_input->connectionMethod == 'smb'){
				$plugin_cookies->userName = $user_input->userName ? $user_input->userName : $plugin_cookies->userName;
				$plugin_cookies->password = $user_input->password ? $user_input->password : $plugin_cookies->password;				
			} else {
				$plugin_cookies->userName = $plugin_cookies->userName ;
				$plugin_cookies->password = $plugin_cookies->password ;				
			}
			// $plugin_cookies->connectionMethod = $user_input->connectionMethod ;

			hd_print("plugin_cookies = "  . print_r($plugin_cookies, true));
		}
	}
	?>