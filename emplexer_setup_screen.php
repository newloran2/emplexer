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
			hd_print(__METHOD__);
			parent::__construct(self::ID);
			hd_print('teste setup screen');
		}

		public function do_get_control_defs(&$plugin_cookies){
			hd_print(__METHOD__  . ': ' . print_r($plugin_cookies, true));
			$defs = array();
			$plexIp     = isset($plugin_cookies->plexIp)   ? $plugin_cookies->plexIp   : '';
			$plexPort   = isset($plugin_cookies->plexPort) ? $plugin_cookies->plexPort : EmplexerConfig::DEFAULT_PLEX_PORT;
			$timeToMark   = isset($plugin_cookies->timeToMark) ? $plugin_cookies->timeToMark : DEFAULT_TIME_TO_MARK;
			$connectionMethod   = isset($plugin_cookies->connectionMethod) ? $plugin_cookies->connectionMethod : HTTP_CONNECTION_TYPE;
			$notSeenCaptionColor   = isset($plugin_cookies->notSeenCaptionColor) ? $plugin_cookies->notSeenCaptionColor : DEFAULT_NOT_SEEN_CAPTION_COLOR;
			$hasSeenCaptionColor   = isset($plugin_cookies->hasSeenCaptionColor) ? $plugin_cookies->hasSeenCaptionColor : DEFAULT_HAS_SEEN_CAPTION_COLOR;
			$userName   = isset($plugin_cookies->userName) ? $plugin_cookies->userName : "";
			$passWord   = isset($plugin_cookies->password) ? $plugin_cookies->password : "";
			$showOnMainScreen = isset($plugin_cookies->showOnMainScreen) ? $plugin_cookies->showOnMainScreen : 'No';
			$useCache = isset($plugin_cookies->useCache) ? $plugin_cookies->useCache : 'true';
			
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
			$comboOpts['http'] = 'http';
			$comboOpts['nfs']  = 'nfs';
			$comboOpts['smb']  = 'smb';


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
				$this->add_button(
					$defs,
					$name          = "configureSMB",
					$title         = "Advance SMB Configuration",
					$caption	   = "SMB Configuration",
					$width         =  200
				);

	   //      	$this->add_text_field(
				// 	$defs,
				// 	$name          = "userName",
				// 	$title         = "User Name",
				// 	$initial_value = $userName,
				// 	$numeric       = false,
				// 	$password      = false,
				// 	$has_osk       = false,
				// 	$always_active = 0,
				// 	$width         = 500     
				// );

				// $this->add_text_field(
				// 	$defs,
				// 	$name          = "password",
				// 	$title         = "Password",
				// 	$initial_value = $passWord,
				// 	$numeric       = false,
				// 	$password      = true,
				// 	$has_osk       = false,
				// 	$always_active = 0,
				// 	$width         = 500     
				// );
			}

			if ($connectionMethod == 'nfs'){
				$this->add_button(
					$defs,
					$name          = "configureNfs",
					$title         = "Advance Nfs Configuration",
					$caption	   = "Nfs Configuration",
					$width         =  200
				);
			}



			$colorSeenNames = array();
			$colorSeenNames[] = 'white';
			$colorSeenNames[] = 'black'; 				//000000
			$colorSeenNames[] = 'blue'; 				//0000A0
			$colorSeenNames[] = 'light green';			//C0E0C0
			$colorSeenNames[] = 'light blue';			//A0C0FF
			$colorSeenNames[] = 'red';					//FF4040
			$colorSeenNames[] = 'lemon green';			//C0FF40
			$colorSeenNames[] = 'yellow';				//FFE040
			$colorSeenNames[] = 'grey';					//C0C0C0
			$colorSeenNames[] = 'dark grey';			//808080
			$colorSeenNames[] = 'purple';				//4040C0
			$colorSeenNames[] = 'light green';			//40FF40
			$colorSeenNames[] = 'cyan';					//40FFFF
			$colorSeenNames[] = 'orange';				//FF8040
			$colorSeenNames[] = 'pink';					//FF40FF
			$colorSeenNames[] = 'light yellow';			//FFFF40
			$colorSeenNames[] = 'very light yellow?' ;	//FFFFE0
			$colorSeenNames[] = 'Tundora';				//404040
			$colorSeenNames[] = 'mid grey';				//AAAAA0
			$colorSeenNames[] = 'very light yellow?';	//FFFF00
			$colorSeenNames[] = 'other green :)';		//50FF50
			$colorSeenNames[] = 'other blue :)';		//5080FF
			$colorSeenNames[] = 'other red :)';			//FF5030
			$colorSeenNames[] = 'other grey :)';		//E0E0E0


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


        	$showOnMainScreenOptions = array('yes' => 'Yes', 'no' => 'No');

        	$this->add_combobox(
				$defs,
        		$name		   			= 'showOnMainScreen',
        		$title		   			= 'Add to main screen',
        		$initial_value 			= $showOnMainScreen,
        		$value_caption_pairs 	= $showOnMainScreenOptions,
        		$width					= 300,
        		$need_confirm 			= false, 
        		$need_apply = true
        	);

        	$useCacheOptions = array('true' => 'Yes', 'false' => 'No');

        	$this->add_combobox(
				$defs,
        		$name		   			= 'useCache',
        		$title		   			= 'Use Cache',
        		$initial_value 			= $useCache,
        		$value_caption_pairs 	= $useCacheOptions,
        		$width					= 300,
        		$need_confirm 			= false, 
        		$need_apply = true
        	);

        	// $this->getStorageLocations($plugin_cookies, $defs);
        	

			$this->add_button(
				$defs,
				$name          = "btnSalvar",
				$title         = "save",
				$caption	   = "save",
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
			hd_print(__METHOD__ . ':' . print_r($user_input, true));
			if ($user_input->selected_control_id == 'configureNfs'){
				return ActionFactory::show_nfs_advanced_configuration_modal('Nfs advanced configuration',&$plugin_cookies);
			} else if ($user_input->selected_control_id == 'configureSMB'){
				return ActionFactory::show_smb_advanced_configuration_modal('SMB advanced configuration',&$plugin_cookies);
			} else if ($user_input->selected_control_id == 'btnSalvar'){
				EmplexerSetupScreen::savePreferences($user_input, $plugin_cookies);
				return ActionFactory::reset_controls($this->do_get_control_defs($plugin_cookies));	
			} else if ($user_input->selected_control_id == 'connectionMethod') {
				EmplexerSetupScreen::savePreferences($user_input, $plugin_cookies);
				return ActionFactory::reset_controls($this->do_get_control_defs($plugin_cookies));	
			}
			
		}


		public static function savePreferences(&$user_input, &$plugin_cookies){
			hd_print(__METHOD__);
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
			$plugin_cookies->showOnMainScreen = $user_input->showOnMainScreen;
			$plugin_cookies->useCache = $user_input->useCache;
			// $plugin_cookies->connectionMethod = $user_input->connectionMethod ;

			hd_print("plugin_cookies = "  . print_r($plugin_cookies, true));
		}
	}

	?>