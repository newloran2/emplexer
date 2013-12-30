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
		// //echo ("config criado\n");
		$this->read_conf_file("/config/emplexer_plugin_cookies.properties");
		// print_r($this->pluginCookies);
	}

	public static function getInstance() {
		if (!isset(self::$instance)){
			self::$instance =  new Config();
		}
		return self::$instance;
	}


	private function read_conf_file($conf_file_path)
    {
        
        $lines = file($conf_file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false)
        {
            // //hd_print("Configuration file '$conf_file_path' does not exist.");
            return false;
        }

        // //hd_print("Reading configuration from '$conf_file_path'...");

        for ($i = 0; $i < count($lines); ++$i)
        {
            if (preg_match('/^ *(\S+) *= *(\S+)$/', $lines[$i], $matches) != 1)
            {
                hd_print(
                    "Warning: line " . ($i + 1) . ": unknown format. " .
                    "Data: '" . $lines[$i] . "'.");
                continue;
            }

            $this->pluginCookies[$matches[1]] = $matches[2];
        }

        return true;
    }

	public function setPluginCookies(&$pluginCookies){
		$this->pluginCookies = $pluginCookies;
	}

	public function __get($key)
	{

		// //echo ("key = $key\n");
		if ($key === "pluginCookies") {
			//echo "retornando pluginCookies\n";
			return  $this->pluginCookies;
		}


		if (!isset($this->pluginCookies->{$key})){
			return null;
		}

		// print_r($this->pluginCookies);
		return $this->pluginCookies->{$key};
	}

	public function __set($key, $value){

		$this->pluginCookies->{$key} = $value;
	}

	public function __destruct(){
		$data = null;
		foreach ($this->pluginCookies as $key => $value) {
			$data .= "$key = $value\n";
		}

		// file_put_contents("/config/emplexer_plugin_cookies.properties", $data);
	}
}


?>