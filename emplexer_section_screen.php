<?php 

/**
* Section Screen
*Show all section has created on Plex
*/
class EmplexerSectionScreen extends	AbstractPreloadedRegularScreen implements UserInputHandler
{

	const ID = "emplexer_section_secreen"; 	
	private $servers;

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
		$pop_up_action = UserInputHandlerRegistry::create_action($this, 'pop_up', null,'Filtos');


		// hd_print(__METHOD__ . ':' . print_r($pop_up_action, true));
		$enter_action = ActionFactory::open_folder();

	
		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY => $enter_action,
			GUI_EVENT_KEY_POPUP_MENU => $pop_up_action
		);
	}

	public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
	{
		
		// hd_print('get_all_folder_items em ' .  self::ID);	

		$items = array();
		// foreach ($this->servers as $server ) {
			// print_r($server);
			// $doc = HD::http_get_document( EmplexerConfig::DEFAULT_PLEX . '/library/sections/all');
			$doc = HD::http_get_document( EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/library/sections/all');
			// $doc = HD::http_get_document( "http://" . $server['Ip'].  ';' . $server['Port'] . "/library/sections/all");

			//hd_print($doc);

			$xml = simplexml_load_string($doc);

			//hd_print(print_r($xml, true));

			
			
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

			$channels = EmplexerConfig::getAllAvailableChannels($plugin_cookies, $this);
			if (count($channels)>0){
				$items =  array_merge($items, $channels);
			}



		// }


		// hd_print(print_r($items, true));
		return $items;
	}


	public function handle_user_input(&$user_input, &$plugin_cookies){
		// hd_print(__METHOD__ . ":" . print_r($user_input, true));
		

		if ($user_input->control_id == 'pop_up') {
			$media_url = MediaURL::decode($user_input->selected_media_url);

			$key = (string) $media_url->category_id;
			// hd_print("key = $key");

			if ($key){
	 			$url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/library/sections/' . $key;
	 			/*$popUp = new EmplexerPopUp(4);
	 			$action = $popUp->showPopUpMenu($url);*/

				$doc = HD::http_get_document( EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/library/sections/' . $key);
				$pop_up_items =  array();
				$xml = simplexml_load_string($doc);
				foreach ($xml->Directory as $c){
					$key = (string)$c->attributes()->key;
					$prompt = (string)$c->attributes()->prompt;
					if ($key != 'all' &&  $key != 'folder' && !$prompt ){
						$pop_up_items[] = array(
							GuiMenuItemDef::caption=> (string)$c->attributes()->title,
							GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url_for_pop_up($media_url, $key), $key)
							);
					}
				}
				// hd_print(__METHOD__ . ' pop_up_items:' .print_r($pop_up_items, true));		
				$action = ActionFactory::show_popup_menu($pop_up_items);	
				// hd_print(__METHOD__ . ': ' . print_r($action, true));
				return $action;
			} else {
				return null;
			}
			
		}	
	}


	private function get_right_media_url_for_pop_up(MediaURL $media_url,$filter_name)
	{
		$episodes = array( 'newest' , 'recentlyAdded', 'recentlyViewed', 'onDeck');
		$season = array('all','recentlyViewedShows','unwatched');
		
		if (in_array($filter_name , $episodes)){			
			return EmplexerVideoList::get_media_url_str($media_url->category_id, $filter_name);
		} else {
			return EmplexerRootList::get_media_url_str($media_url->category_id, $filter_name);
		}
	}


	private function get_right_media_url($type, $key)
	{
		
		if ($type == "movie"){			
			// hd_print ("key =$key type=$type  movie");
			return EmplexerMovieList::get_media_url_str($key, 'all', 'movie');
		} else {
			// hd_print ("key =$key type=$ddtype  show");
			return EmplexerRootList::get_media_url_str($key); //all
		}
	}


	public function get_folder_views()
	{
		return EmplexerConfig::GET_SECTIONS_LIST_VIEW();
	}
}

?>