<?php 

// require_once 'lib/default_archive.php';
require_once 'lib/archive_cache.php';

class EmplexerArchive implements Archive
{

	private $arquiveName;	
	private $arquiveSize;
	private $urls =array();

	private static $instance;

	/**
	 * Init a arquive cache with $arquiveName name and $arquiveSize size
	 * 
	 * @param string  $arquiveName name for arquive cache (DEFAULT IS  emplexer_default_archive)
	 * @param integer $arquiveSize size for arquive cache (DEFAULT IS 51200 Bytes)
	 * @throws Exception if $arquiveSize is less or eguals zero 
	 */
	private function __construct($arquiveName='emplexer_default_archive', $arquiveSize=51200)
	{
		$this->arquiveName = $arquiveName;
		if(!is_numeric($arquiveSize) || $arquiveSize <=0){
			throw new Exception("A bigger size is necessary to create an archive" , 1);
		}
		$this->arquiveSize = $arquiveSize;
		ArchiveCache::set_archive($this);

	}

	//static methods
	
	public static function getInstance(){
		if (!isset(self::$instance)){
			self::$instance = new EmplexerArchive();
		}
		return self::$instance;
	}



	public static function clear_cache()
	{ 
		ArchiveCache::clear_all(); 
	}

	public static function clear_cached_archive($arquiveName)
	{ 
		ArchiveCache::clear_archive($arquiveName); 
	}

	public static function get_cached_archive($arquiveName)
	{ 
		return ArchiveCache::get_archive_by_id($arquiveName); 
	}

	//abstract methods
	public function get_id()
	{
		// hd_print(__METHOD__);
		return $this->arquiveName;
	}

	public function get_archive_def()
	{
		
		$a =array(
			PluginArchiveDef::id => $this->arquiveName,
			PluginArchiveDef::urls_with_keys => $this->urls,
			PluginArchiveDef::total_size => $this->arquiveSize
		);
		// hd_print(__METHOD__ . ':' . print_r($a, true));

		return $a;
	}


	//public non abtract methods
	
	public function setFileToArchive($fileName, $fileUrl)	
	{
		//no cache
		if(EmplexerConfig::USE_CACHE){
			//hd_print(__METHOD__ . " fileName=$fileName, fileUrl=$fileUrl");
			$this->urls[$fileName] = $fileUrl ;
			EmplexerFifoController::getInstance()->downloadToCache($fileName, $fileUrl);
		}
	}

	public function getFileFromArchive($fileName, $default_url ='missing://'){
		
		if (!isset($this->urls[$fileName])) {
			return $default_url;
		}
		if (file_exists("/persistfs/plugins_archive/emplexer/emplexer_default_archive/$fileName")){
			// hd_print ("arquivo /persistfs/plugins_archive/emplexer/emplexer_default_archive/$fileName exite" );
			return 'plugin_archive://' . $this->arquiveName . '/' . $fileName;
			// return '/persistfs/plugins_archive/emplexer/emplexer_default_archive/' . $this->arquiveName . '/' . $fileName;			
		} else {
			// hd_print ("arquivo /persistfs/plugins_archive/emplexer/emplexer_default_archive/$fileName não exite" );
			return $default_url;
		}
	}

	private function downloadTocacheIfNeed($fileName, $fileUrl){

	}

}

?>