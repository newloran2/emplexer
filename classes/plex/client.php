<?php

class Client
{
    private $plexIp   = '127.0.0.1';
    private $plexPort = '32400';

    private static $instance;

    private function __construct() {
        // $this->plexIp   = '192.168.2.8';
        // $this->plexPort = '32400';
        $this->refreshPlexIpAndPort();
    }
    public static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new Client;
        }
        return self::$instance;
    }

    public function refreshPlexIpAndPort(){
        // HD::print_backtrace();
        // var_dump(__METHOD__);
        // var_dump(Config::getInstance()->pluginCookies);
        $this->plexPort = Config::getInstance()->plexPort;
        $this->plexIp = Config::getInstance()->plexIp;
    }

    public function get($url, $opts = null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           10);
        curl_setopt($ch, CURLOPT_USERAGENT,         'DuneHD/1.0');
        curl_setopt($ch,CURLOPT_ENCODING ,           "gzip");
        curl_setopt($ch, CURLOPT_URL,               $url);

        if (isset($opts))
        {
            foreach ($opts as $k => $v)
                curl_setopt($ch, $k, $v);
        }

        //hd_print("HTTP fetching '$url'...");

        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($content === false)
        {
            $err_msg =
                "HTTP error: $http_code; " .
                "CURL errno: " . curl_errno($ch) .
                " (" . curl_error($ch) . ')';
            //hd_print($err_msg);
            throw new Exception($err_msg);
        }

        if ($http_code != 200)
        {
            $err_msg = "HTTP request failed ($http_code)";
            //hd_print($err_msg);
            throw new Exception($err_msg);
        }

        //hd_print("HTTP OK ($http_code)");

        curl_close($ch);

        return $content;
    }


    private function startEmplexerServer(){
        $pid = shell_exec('pidof emplexer.lua');
        // $pid = null;
        if (!$pid){
            $command = ROOT_PATH . "/bin/lem-dune.bin " . ROOT_PATH . "/bin/emplexer.lua > /dev/null &";
            hd_print("executando commando $command");

            shell_exec($command);
        }
    }

    public function getFinalThumbUrl($url){
        return $this->get($url, array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_NOBODY => true,
                CURLOPT_HEADER => true
            )
        );
    }

    public function getThumbUrl($key, $with=250, $height=250){
        // hd_print(__METHOD__ . ":" . $this->plexIp  );
        if (trim($this->plexIp) === "" && !filter_var($this->plexIp, FILTER_VALIDATE_IP)){
            $this->refreshPlexIpAndPort();
        }

        if (!$height) $height = 250;
        if (!$with) $with = 250;
        $url = $this->getUrl(null, $key);
        if (strstr($key, "?") || strstr($key, "&")){
            $key = urlencode($key);
        }
        if (strstr($url, "?") || strstr($url, "&")){
            $url = urlencode($url);
        }

        return sprintf("http://%s:%d/photo/:/transcode?width=%d&height=%d&url=%s", $this->plexIp, $this->plexPort,$with, $height, $url );
        // http://192.168.2.8:32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2F%3A%2Fresources%2Fmovie.png&width=150&height=150&X-Plex-Token=Fdxv1u7Rk97GspiQwqPy
    }


    public function getUrl($lastKey, $newKey)
    {
        // hd_print(__METHOD__ . ":" . $this->plexIp  );
        if (trim($this->plexIp) === "" && !filter_var($this->plexIp, FILTER_VALIDATE_IP)){
            $this->refreshPlexIpAndPort();
        }

        // hd_print("parametros = $lastKey, $newKey");
        if (strpos($newKey, "http") === 0){
            return $newKey;
        }

        if (strpos($lastKey, "http") === 0 && strpos($newKey, "/") === 0){
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
            return $url;
        }

        if (strpos($lastKey, "http") === 0 && strpos($newKey, "/") !== 0){
            return sprintf("%s/%s", $lastKey, $newKey);
        }
        if (strpos($newKey, "/") !== 0){
            $url = sprintf("http://%s:%d%s/%s", $this->plexIp, $this->plexPort, $lastKey, $newKey);
        } else {
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
        }

        // echo ("url = $url\n");
        return $url;
    }


    public function startMonitor($key, $viewOffset){
        $this->startEmplexerServer();
        $url = sprintf("http://127.0.0.1:3000/startNotifier/%s/%d/%d/%d/%d",$this->plexIp, $this->plexPort,$key, 10, $viewOffset);
        $this->get($url);
    }


    public function getAndParse($url){
        return new SimpleXMLElement($url, 0, true);
    }
}

?>