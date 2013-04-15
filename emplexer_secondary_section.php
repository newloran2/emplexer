<?php 


class EmplexerSecondarySection extends AbstractPreloadedRegularScreen
{
	
	const ID = "emplexer_secondary_Section"; 

	function __construct()
	{
		hd_print(__METHOD__);
		parent::__construct(self::ID, $this->get_folder_views());
	}

	public static function get_media_url_str($category_id, $filter_name=null, $type='movie')
	{
		// $filter_name = !isset($filter_name)?'all':$filter_name;

		return MediaURL::encode(
			array
			(
				'screen_id'     => self::ID,
				'category_id'   => $category_id,
				'filter_name'   => $filter_name,
				'type'          => $type
				)
			);
	}

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		hd_print(__METHOD__);
		$enter_action = ActionFactory::open_folder();

		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY => $enter_action,
			);
	}


	public function get_all_folder_items(MediaURL $media_url , &$plugin_cookies){
		hd_print(__METHOD__);
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
			$items[] = array
			(
				PluginRegularFolderItem::media_url => $this->get_right_media_url($media_url,$c),
				PluginRegularFolderItem::caption => (string) $c->attributes()->title,
				PluginRegularFolderItem::view_item_params =>
					array
					(
					
					)
			);

		}

		hd_print(__METHOD__ . ':' . print_r($items, true));
		return $items;
	}

	private function get_right_media_url(MediaURL $media_url, $node)
	{

		switch ($media_url->type) {
			case SECTION_MOVIE:
				return EmplexerMovieList::get_media_url_str($media_url->category_id, $media_url->filter_name . '/' . $node->attributes()->key , 'movie');
				break;
			case SECTION_SHOW:
				return EmplexerRootList::get_media_url_str($media_url->category_id,  $media_url->filter_name . '/' . $node->attributes()->key);
				break;
			default:

				break;
		}
	}

	public function get_folder_views()
	{
		hd_print(__METHOD__);
		// return EmplexerConfig::getInstance()->GET_SECTIONS_LIST_VIEW();
		return EmplexerConfig::getInstance()->GET_SECTIONS_LIST_VIEW();
	}

}

?>