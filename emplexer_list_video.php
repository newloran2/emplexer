<?php 

/**
* 		
*/
class EmplexerListVideo extends EmplexerBaseChannel
{
	const ID ='emplexer_list_video';
	function __construct()
	{
		parent::__construct(self::ID);		
	}

	public function get_folder_views()
	{	
		return EmplexerConfig::GET_VIDEOS_LIST_VIEW();
	}

	public static function get_media_url_str($key, $type=TYPE_DIRECTORY,$videoMediaArray=null)
	{

		return MediaURL::encode(
			array
			(
				'screen_id'         => self::ID,
				'key'               => $key,
				'type'          	=> $type,
				'video_media_array' => $videoMediaArray
				)
			);
	}		

}

 ?>