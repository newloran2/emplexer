<?php 

/**
* Section Screen
Show all section has created on Plex
*/
class EmplexerSectionScreen extends	AbstractPreloadedRegularScreen implements UserInputHandler
{

	const ID = "emplexer_section_secreen"; 	
	function __construct()
	{
		parent::__construct(self::ID, $this->get_folder_views());
	}

	public function get_handler_id()
	{
		return self::ID;
	}

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{

		// hd_print(__METHOD__ . ': ' .  print_r($media_url, true));

		UserInputHandlerRegistry::get_instance()->register_handler($this);
		$stop_action = UserInputHandlerRegistry::create_action($this, 'pop_up', null,'Filtos');

		$enter_action = ActionFactory::open_folder();

	
		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY => $enter_action,
			GUI_EVENT_KEY_POPUP_MENU => $stop_action
		);
	}

	public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
	{
		
		hd_print('get_all_folder_items em ' .  self::ID);	
		$doc = HD::http_get_document( EmplexerConfig::DEFAULT_PLEX . '/library/sections/all');

		//hd_print($doc);

		$xml = simplexml_load_string($doc);

		//hd_print(print_r($xml, true));

		$items = array();
		
		foreach ($xml->Directory as $c)			
		{
			$items[] = array
			(
				 // EmplexerRootList::get_media_url_str((string)$c->attributes()->key, null), 
				PluginRegularFolderItem::media_url =>  $this->get_right_media_url((string)$c->attributes()->type,(string)$c->attributes()->key),
				PluginRegularFolderItem::caption => (string) $c->attributes()->title,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path => 'plugin_file://icons/sudoku.png',
				)
			);

		}


		// hd_print(print_r($items, true));
		return $items;
	}


	public function handle_user_input(&$user_input, &$plugin_cookies){

		// hd_print(__METHOD__ . ': ' . $user_input);
		if ($user_input->control_id == 'pop_up') {
			$action = ActionFactory::show_popup_menu($this->pop_up_items);	
			
		}
		return $action;
		
	}

	private function get_right_media_url($type, $key)
	{
		
		if ($type == "movie"){			
			hd_print ("key =$key type=$type  movie");
			return EmplexerMovieList::get_media_url_str($key, 'all', 'movie');
		} else {
			hd_print ("key =$key type=$type  show");
			return EmplexerRootList::get_media_url_str($key); //all
		}
	}


	public function get_folder_views()
	{
		return EmplexerConfig::GET_SECTIONS_LIST_VIEW();
	}
}

?>