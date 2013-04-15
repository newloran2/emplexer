<?php 

/**
* 
*/
class EmplexerRootList extends AbstractPreloadedRegularScreen
{
	
	const ID = "plex_root_list"; 

	function __construct()
	{
		// hd_print(__METHOD__);
		parent::__construct(self::ID, $this->get_folder_views());
	}

	public static function get_media_url_str($category_id, $filter_name=null,$type='show')
	{
		// hd_print(__METHOD__);
		// hd_print('  category_id: ' . $category_id . ' filter_name: ' .  $filter_name);
		$filter_name = !isset($filter_name)?'all':$filter_name;

		return MediaURL::encode(
			array
			(
				'screen_id'     => self::ID,
				'category_id'   => $category_id,
				'filter_name'   => $filter_name,
				'type'			=> $type
				)
			);
	}

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		// hd_print(__METHOD__);
		$enter_action = ActionFactory::open_folder();

		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY => $enter_action,
			);
	}


	public function get_all_folder_items(MediaURL $media_url , &$plugin_cookies){
		// hd_print(__METHOD__);
		//hd_print(__METHOD__ . ':' . print_r($media_url, true));
		$doc = HD::http_get_document( 
			EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $this) . 
			'/library/sections/' . 
			$media_url->category_id . 
			'/' . $media_url->filter_name 
			);

		$xml = simplexml_load_string($doc);

		$items = array();
		$cache_keys = array();
		foreach ($xml->Directory as $c)
		{
			$thumb =(string)$c->attributes()->thumb;
			$url =  EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $this) . '/photo/:/transcode?width='.THUMB_WIDTH. '&height='. THUMB_HEIGHT . '&url=' . EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $this). $thumb;
			// $urlb = EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $this) . (string)$c->attributes()->thumb;
			
			// hd_print(__METHOD__ . ':EmplexerConfig::getInstance()->useCache= ' . EmplexerConfig::getInstance()->useCache .  ' tipo = '  . gettype(EmplexerConfig::getInstance()->useCache));
			// if (EmplexerConfig::getInstance()->useCache === 'false'){
			// 	hd_print('Entrou.... ' );
			// 	$url = EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $this). $thumb;
			// }
			$bgImage = EmplexerConfig::getInstance()->getPlexBaseUrl($plugin_cookies, $this) .  $c->attributes()->art;
			
			$caption = (string) $c->attributes()->ratingKey . '.jpg';

			if ($thumb){
				EmplexerArchive::getInstance()->setFileToArchive($caption, $url );		
			}
			
			
			$items[] = array
			(
				PluginRegularFolderItem::media_url => $this->get_right_media_url($media_url,$c),
				PluginRegularFolderItem::caption => (string) $c->attributes()->title,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path => EmplexerArchive::getInstance()->getFileFromArchive($caption, $url),
					ViewItemParams::item_detailed_icon_path => EmplexerArchive::getInstance()->getFileFromArchive($caption, $url),
					ViewItemParams::icon_keep_aspect_ratio => false
				)
			);

		}
		// hd_print(__METHOD__ .':'. print_r($items, true));
		return $items;
	}

	private function get_right_media_url(MediaURL $media_url, $node)
	{
		// hd_print(__METHOD__);
		$episodes = array( 'newest' , 'recentlyAdded', 'recentlyViewed', 'onDeck');
		$season = array('all','recentlyViewedShows');
		

		if (in_array($media_url->filter_name , $episodes)){			
			return EmplexerVideoList::get_media_url_str((string)$node->attributes()->key);
		} else {
			return EmplexerSeasonList::get_media_url_str((string)$node->attributes()->key);
		}
	}

	public function get_folder_views()
	{
		// hd_print(__METHOD__);p
		// return EmplexerConfig::getInstance()->GET_SECTIONS_LIST_VIEW();
		return EmplexerConfig::getInstance()->GET_VIDEOS_LIST_VIEW();
	}

}

?>