<?php 

/**
* 		
*/
class EmplexerListVideo extends EmplexerBaseChannel
{
	const ID ='emplexer_list_video';
	function __construct()
	{
		hd_print(__METHOD__);
		parent::__construct(self::ID);		
	}

	public function get_folder_views()
	{	
		hd_print(__METHOD__);
		return EmplexerConfig::GET_SECTIONS_LIST_VIEW();
	}

	public static function get_media_url_str($key, $type=TYPE_DIRECTORY,$videoMediaArray=null)
	{
		hd_print(__METHOD__);
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

	public function getThumbURL(SimpleXMLElement &$node)
	{
		$thumb = (string)$node->attributes()->thumb;
		$thumb = $this->base_url . '/photo/:/transcode?width=340&height=480&url=' . urlencode($this->base_url . $thumb); 
		hd_print(__METHOD__ . ':' . $thumb);
		
		
		if ($node->attributes()->thumb && $node->attributes()->ratingKey){
			$cacheKey = (string)$node->attributes()->ratingKey. '.jpg';
			EmplexerArchive::getInstance()->setFileToArchive($cacheKey, $thumb );				
			return EmplexerArchive::getInstance()->getFileFromArchive($cacheKey, $thumb);
		}
		return $thumb;
	}		

	public function getVideoUrl(SimpleXMLElement &$node, &$plugin_cookies)
	{
		hd_print(__METHOD__);
			
			$httpVidelUrl = $this->base_url . (string)$node->Part->attributes()->key;
			$nfsVideoUrl  = 'nfs://' . $plugin_cookies->plexIp . ':' . (string)$node->Part->attributes()->file; 
			if ($plugin_cookies->connectionMethod == 'smb'){
				$smbVideoUrl  = 'smb://' . $plugin_cookies->userName . ':' .  $plugin_cookies->password . '@' . $plugin_cookies->plexIp . '/' . (string)$node->Media->Part->attributes()->file;	
				$videoUrl[SMB_CONNECTION_TYPE]  = $smbVideoUrl;
			}
			
			$videoUrl[HTTP_CONNECTION_TYPE] = $httpVidelUrl;
			$videoUrl[NFS_CONNECTION_TYPE]  = $nfsVideoUrl;

			// $v = EmplexerConfig::USE_NFS ? $nfsVideoUrl : $httpVidelUrl;
			$v=null;
			if ($node->Part->attributes()->file && $node->Part->attributes()->file != ""){
				$v = $videoUrl[$plugin_cookies->connectionMethod];
			} else {
				$v= $httpVidelUrl;
			}
			

			if (!$v){
				hd_print('connectionMethod not setted use http as default');
				$v = $httpVidelUrl;
			}

			hd_print("-----------videoUrl = $v-----------");

		return $v;
	}

	public function getNextScreen($parameters){
		$key = $parameters['key'];
		$type = $parameters['type'];
		$params = $parameters['params'];
		$art = $params['art'];
		$a = EmplexerConfig::GET_SECTIONS_LIST_VIEW();
		$this->folder_views = $a ;
		return $this->get_media_url_str($key, $type, $params);
	}
	
	public function doParseOnDirectories(&$xml, &$media_url,  &$plugin_cookies){
		$items = parent::doParseOnDirectories($xml, $media_url, $plugin_cookies);
		return $items;
	}


}

 ?>