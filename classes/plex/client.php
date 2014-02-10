<?php

class Client
{
    private $plexIp   = '127.0.0.1';
    private $plexPort = '32400';
    private $myPlexBaseUr = 'https://plex.tv';
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

        // echo "url = $url";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           10);
        curl_setopt($ch, CURLOPT_USERAGENT,         'DuneHD/1.0');
        curl_setopt($ch,CURLOPT_ENCODING ,           "gzip");
        curl_setopt($ch, CURLOPT_URL,               $url);

        $myPlexToken = Config::getInstance()->myPlexToken;

        if (!is_null($myPlexToken)){
            curl_setopt($ch,CURLOPT_HTTPHEADER ,array("X-Plex-Token: $myPlexToken"));
        }

        if (isset($opts))
        {

            hd_print(print_r($opts, true));

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

        if ($http_code != 200 && $http_code != 201)
        {
            $err_msg = "HTTP request failed ($http_code)";
            //hd_print($err_msg);
            throw new Exception($err_msg);
        }

        //hd_print("HTTP OK ($http_code)");

        curl_close($ch);

        return $content;
    }


    public function startEmplexerServer(){
        $pid = shell_exec('pgrep emplexer.lua');
        // $pid = null;
        if (!$pid){
            // $command = ROOT_PATH . "/bin/lem-dune.bin " . ROOT_PATH . "/bin/emplexer.lua > /dev/null &";
            $command = ROOT_PATH . "/bin/lem-mac " . ROOT_PATH . "/bin/emplexer.lua > /tmp/teste.log &";
            hd_print("executando commando $command");

            shell_exec($command);
        }
    }

    public function registerAsPlayer($name='emplexer') {
        $url = sprintf("http://127.0.0.1:3000/startServer/%s", $name);
        $this->get($url);
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
        hd_print(__METHOD__ . ":" .  $key . ": " . $this->plexIp . ": "  . $this->plexPort);
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
        // hd_print("parametros = $lastKey, $newKey");
        // hd_print(__METHOD__ . ":" . $this->plexIp  );
        //
        if (trim($this->plexIp) === "" && !filter_var($this->plexIp, FILTER_VALIDATE_IP)){
            $this->refreshPlexIpAndPort();
        }


        if (strpos($newKey, "http") === 0){
            return $newKey;
        }

        if (strpos($lastKey, "http") === 0 && strpos($newKey, "/") === 0){
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
            return $url;
        }

        if (strpos($lastKey, "http") === 0 || strpos($lastKey, "https") === 0  && strpos($newKey, "/") !== 0){
            return sprintf("%s/%s", $lastKey, $newKey);
        }
        if (strpos($newKey, "/") !== 0){
            $url = sprintf("http://%s:%d%s/%s", $this->plexIp, $this->plexPort, $lastKey, $newKey);
        } else {
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
        }
        // HD::print_backtrace();
        // hd_print ("url = $url\n");
        return $url;
    }


    public function startMonitor($key, $viewOffset){
        $this->startEmplexerServer();
        $url = sprintf("http://127.0.0.1:3000/startNotifier/%s/%d/%d/%d/%d",$this->plexIp, $this->plexPort,$key, 10, $viewOffset);
        $this->get($url);
    }


    public function getAndParse($url){
        $data = $this->get($url);
        // hd_print("url = $url");
        return simplexml_load_string($data);
        // return new SimpleXMLElement($url, 0, true);
    }

    //myPlex
    public function myPLexAuth($force=false){

        $userName =  Config::getInstance()->myPlexUserName;
        $password =  Config::getInstance()->myPlexPassword;
        $myPlexToken = Config::getInstance()->myPlexToken;
        hd_print("myPlexToken = $myPlexToken");
        if (!is_null($myPlexToken)){
            hd_print("já tem vou retornar $myPlexToken");
            return $myPlexToken;
        }

        hd_print("Não tem vou buscar");


        $url = sprintf('%s/%s', $this->myPlexBaseUr, '/users/sign_in.xml');

        $opts = array( CURLOPT_HTTPHEADER =>
                        array(
                            'X-Plex-Client-Identifier:sdsdsdsds',
                            'X-Plex-Client-Platform:DuneOS',
                            'X-Plex-Device-Name:Dune',
                            'X-Plex-Model:1',
                            'X-Plex-Platform:DuneOS',
                            'X-Plex-Platform-Version:1',
                            'X-Plex-Product:emplexer',
                            'X-Plex-Version:1'
                            ),
                        CURLOPT_USERPWD => "newloran2@gmail.com:bastard123",
                        CURLOPT_POSTFIELDS =>  "",
                        CURLOPT_POST => true,
                     );

        $data = $this->get($url, $opts);
        if ($data){
            $xml = simplexml_load_string($data);
            hd_print(print_r($xml, true));
            Config::getInstance()->myPlexToken = $xml["authentication-token"];
            return $xml["authentication-token"];
        }

        return null;
    }

    public function getMyPlexServers(){
        // $myPlexToken =  $this->myPLexAuth();
        $url = sprintf('%s%s', $this->myPlexBaseUr, '/pms/servers');
        $data = $this->get($url);
        hd_print(print_r($data,true));
    }
}

?>