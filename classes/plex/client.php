<?php  

class Client 
{
	private $plexIp   = '192.168.2.8';
	private $plexPort = '32400';

	private static $instance;

	private function __construct() {
		$this->plexIp   = '192.168.2.8';
		$this->plexPort = '32400';
	}
	public static function getInstance(){
		if (!isset(self::$instance)){
			self::$instance = new Client;
		}
		return self::$instance;
	}

	private function get($url, $opts = null){
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           10);
        curl_setopt($ch, CURLOPT_USERAGENT,         'DuneHD/1.0');
        curl_setopt($ch, CURLOPT_URL,               $url);

        if (isset($opts))
        {
            foreach ($opts as $k => $v)
                curl_setopt($ch, $k, $v);
        }

        hd_print("HTTP fetching '$url'...");

        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($content === false)
        {
            $err_msg =
                "HTTP error: $http_code; " .
                "CURL errno: " . curl_errno($ch) .
                " (" . curl_error($ch) . ')';
            hd_print($err_msg);
            throw new Exception($err_msg);
        }

        if ($http_code != 200)
        {
            $err_msg = "HTTP request failed ($http_code)";
            hd_print($err_msg);
            throw new Exception($err_msg);
        }

        hd_print("HTTP OK ($http_code)");

        curl_close($ch);

        return $content;
	}

	public function getByPath($path){
		$url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $path);
		$data =  $this->get($url);
		return simplexml_load_string($data);
	}
}

?>