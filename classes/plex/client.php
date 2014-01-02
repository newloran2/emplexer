<?php

class Client
{
    private $plexIp   = '127.0.0.1';
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

    public function getThumbUrl($key, $with=250, $height=250){
        if (!$height) $height = 250;
        if (!$with) $with = 250;
        $url = $this->getUrl(null, $key);
        return sprintf("http://%s:%d/photo/:/transcode?width=%d&height=%d&url=%s", $this->plexIp, $this->plexPort,$with, $height, $url );
        // http://192.168.2.8:32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2F%3A%2Fresources%2Fmovie.png&width=150&height=150&X-Plex-Token=Fdxv1u7Rk97GspiQwqPy
    }


    public function getUrl($lastKey, $newKey)
    {
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
    public function getAndParse($url){
        $data =  $this->get($url);
        return simplexml_load_string($data);
    }
}

?>