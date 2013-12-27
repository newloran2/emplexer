<?php 


/**
* Class to handle all dune plugin_cookies
*/
class Config {	
	
	// private const $pluginCookiesFile = '/config/emplexer_plugin_cookies.properties'
	private $pluginCookies;
	public static $instance;

	/**
	 * Read initial plugin cookies from /config dir
	 */
	private function __construct(){
		echo ("config criado\n");
	}

	public static function getInstance() {
		if (!isset(self::$instance)){
			self::$instance =  new Config();
		}
		return self::$instance;
	}

	public function setPluginCookies(&$pluginCookies){
		$this->pluginCookies = $pluginCookies;
	}

	public function __get($key)
	{
		if (!isset($this->pluginCookies->{$key})){
			return null;
		}

		if ($key == "pluginCookies") {
			echo "retornando pluginCookies\n";
			return  $this->pluginCookies;
		}

		return $this->pluginCookies[$key];
	}

	public function __set($key, $value){

		$this->pluginCookies->{$key} = $value;
	}
}


?>