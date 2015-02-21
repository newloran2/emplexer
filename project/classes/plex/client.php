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

            // hd_print(print_r($opts, true));

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

        if ($http_code != 200 && $http_code != 201 && $http_code != 204)
        {
            $err_msg = "HTTP request failed ($http_code)";
            hd_print("deu erro na url $url");
            //hd_print($err_msg);
            throw new Exception($err_msg);
        }

        //hd_print("HTTP OK ($http_code)");

        curl_close($ch);

        return $content;
    }

    public function startEmplexerServerAndRegisterAsPlayer()
    {
        hd_print("valor de DEV " . DEV);
        if (!DEV){
            hd_print(__METHOD__. " entrou");
            if ($this->startEmplexerServer()){
                $this->registerAsPlayer();
            }
        }
    }
    public function startEmplexerServer(){
        // HD::print_backtrace();
        if (!DEV){
            hd_print(__METHOD__. " entrou");
            $pid = shell_exec('pgrep lem-dune.bin');
            hd_print(__METHOD__. " valor do pid $pid");
            if (!$pid){
                hd_print(__METHOD__ . "pid inválido registrando");
                $command = ROOT_PATH . "/bin/lem-dune.bin " . ROOT_PATH . "/bin/emplexer.lua > /dev/null &";
                // $command = ROOT_PATH . "/bin/lem-dune.bin " . ROOT_PATH . "/bin/emplexer.lua >> /D/dune_plugin_logs/emplexer2.log &";
                // $command = ROOT_PATH . "/bin/lem-mac " . ROOT_PATH . "/bin/emplexer.lua > /tmp/teste.log &";
                hd_print("executando commando $command");
                $command = "LD_LIBRARY_PATH=" . ROOT_PATH . "/bin/dune/lua/ $command";
                // ExecUtils::execute($command);
                exec($command);
                sleep(3);
                return true;
            }
        }
        return false;
    }

    public function registerAsPlayer($name='emplexer') {
        if (!DEV){
            hd_print(__METHOD__. " entrou");
            $url = sprintf("http://127.0.0.1:3005/startServer/%s", $name);
            $this->get($url);
        }
    }

    public function getFinalThumbUrl($url){

        // $data  =  $this->get($url, array( CURLOPT_FOLLOWLOCATION => true, CURLOPT_NOBODY => true,CURLOPT_HEADER => true));

        // return curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
    }

    public function getRemoteFileType($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,               $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $data  =  curl_exec($ch);

        $ret =  curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return $ret;
    }


    public function getFinalVideoUrl($url){
        $this->get($url, array(CURLOPT_FOLLOWLOCATION => true, CURLOPT_NOBODY => true,CURLOPT_HEADER => true));
        $info = curl_getinfo($ch);
        // hd_print_r("info" , $info);
        if ($info['content_type'] ==  "application/xml") {
            $data = $this->getAndParse($url);
            $videoUrl = $this->getUrl(null, $data->Video[0]->Media->Part->attributes()->key);
            hd_print(__METHOD__ . ": video url $videoUrl");
            return $this->getFinalVideoUrl($videoUrl);
        }
    }

    public function getThumbUrl($key, $with=250, $height=250){

        // hd_print("key = $key");
        // hd_print(__METHOD__ . ":" .  $key . ": " . $this->plexIp . ": "  . $this->plexPort);
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
        $a=sprintf("http://%s:%d/photo/:/transcode?width=%d&height=%d&url=%s", $this->plexIp, $this->plexPort,$with, $height, $url );
        return sprintf("http://%s:%d/photo/:/transcode?width=%d&height=%d&url=%s", $this->plexIp, $this->plexPort,$with, $height, $url );
    }

    public function getEpisodeCouterThumbUrl($key,$count=0,$with = 250, $height=250){
        $plexThumUrl = $this->getThumbUrl($key, $with, $height);
        $url = str_replace("?", "firstParameter", $plexThumUrl);
        $url = str_replace("&", "otherParameter", $url);
        $url = str_replace("=", "equal", $url);
        $emplexerUtilsCounterThumbUrl = sprintf("http://192.168.2.8/wideimage-11/demo/epidodesCountImage.php?count=%d&url=%s", $count, $url );

        hd_print("retornando a url $emplexerUtilsCounterThumbUrl");
        return sprintf("http://127.0.0.1/cgi-bin/plugins/emplexer2/imageGetter.sh?url=%s",urlencode($emplexerUtilsCounterThumbUrl));
    }

    public function getEpisodeProgressThumbUrl($key,$percent=0,$title=null,$with = 250, $height=250){
        $plexThumUrl = $this->getThumbUrl($key, $with, $height);
        // $url = str_replace("?", "firstParameter", $plexThumUrl);
        // $url = str_replace("&", "otherParameter", $url);
        // $url = str_replace("=", "equal", $url);
        // $emplexerUtilsProgressThumbUrl = sprintf("http://192.168.2.8/wideimage-11/demo/progressImage.php?percent=%d&url=%s&title=%s", $percent, $url, $title);
        // $emplexerUtilsProgressThumbUrl = sprintf("http://192.168.2.7:3005/generate?url=%s&p=%d&r=204&g=123&b=25&bh=2", urlencode($plexThumUrl),$percent );
        hd_print("retornando a url $emplexerUtilsProgressThumbUrl");
        // return $emplexerUtilsProgressThumbUrl;
        return $plexThumUrl;
        // return sprintf("http://127.0.0.1/cgi-bin/plugins/emplexer2/imageGetter.sh?url=%s",urlencode($emplexerUtilsProgressThumbUrl));
    }

    public function getUrl($lastKey, $newKey)
    {
        $decLastKey = parse_url($lastKey);
        //        hd_print("parametros   lastKey= $lastKey,  newKey = $newKey");
        /* HD::print_backtrace(); */
        // hd_print(__METHOD__ . ":" . $this->plexIp  );
        //
        if (trim($this->plexIp) === "" && !filter_var($this->plexIp, FILTER_VALIDATE_IP)){
            $this->refreshPlexIpAndPort();
        }


        if (strpos($newKey, "http") === 0 ||strpos($newKey, "https") === 0){
            //            hd_print('strpos($newKey, "http") === 0 ||strpos($newKey, "https") === 0');
            return $newKey;
        }

        if ((strpos($lastKey, "http") === 0 || strpos($lastKey, "https") ===0) && strpos($newKey, "/library") === 0){
            //            hd_print_r('strpos($lastKey, "http") === 0 || strpos($lastKey, "https") ===0 && strpos($newKey, "/library") === 0' . "  lastKey = $lastKey , newKey = $newKey" , $decLastKey);

            $urlWithoutQueryParams = sprintf("%s://%s:%s", $decLastKey['scheme'],$decLastKey['host'],$decLastKey['port']);
            $separator = strpos($newKey , '?' ) === false ? '?' : '&';
            return sprintf("%s%s%s", $urlWithoutQueryParams, $newKey, isset($decLastKey['query'])? '?'.$decLastKey['query'] : '' );
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
            return $url;
        }

        if ((strpos($lastKey, "http") === 0 || strpos($lastKey, "https") ===0) && strpos($newKey, "/") === 0){
            //            hd_print_r('strpos($lastKey, "http") === 0 || strpos($lastKey, "https") ===0 && strpos($newKey, "/") === 0' . "  lastKey = $lastKey , newKey = $newKey", $decLastKey);

            $urlWithoutQueryParams = sprintf("%s://%s:%s", $decLastKey['scheme'],$decLastKey['host'],$decLastKey['port']);
            //            hd_print("urlWithoutQueryParams = $urlWithoutQueryParams newKey = $newKey");
            $separator = strpos($newKey , '?' ) === false ? '?' : '&';
            return sprintf("%s%s%s", $urlWithoutQueryParams, $newKey, isset($decLastKey['query'])? $separator .$decLastKey['query'] : '' );
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
            return $url;
        }


        if ((strpos($lastKey, "http") === 0 || strpos($lastKey, "https") === 0)  && strpos($newKey, "/") !== 0){
            //            hd_print('strpos($lastKey, "http") === 0 || strpos($lastKey, "https") === 0  && strpos($newKey, "/") !== 0');
            $urlWithoutQueryParams = sprintf("%s://%s:%s%s", $decLastKey['scheme'],$decLastKey['host'],$decLastKey['port'],$decLastKey['path']);
            $separator = strpos($newKey , '?' ) === false ? '?' : '&';
            return sprintf("%s/%s%s", $urlWithoutQueryParams, $newKey, isset($decLastKey['query'])? '?'.$decLastKey['query'] : '' );
        }
        if (strpos($newKey, "/") !== 0){
            //            hd_print('strpos($newKey, "/") !== 0');
            /* HD::print_backtrace(); */
            return $newKey;
            $url = sprintf("http://%s:%d%s/%s", $this->plexIp, $this->plexPort, $lastKey, $newKey);
        } else {
            //            hd_print("else");
            /* HD::print_backtrace(); */
            $url = sprintf("http://%s:%d%s", $this->plexIp, $this->plexPort, $newKey);
        }
        // HD::print_backtrace();
        // hd_print ("url = $url\n");
        return $url;
    }


    public function startMonitor($key, $viewOffset){
        if (!DEV){
            $this->startEmplexerServer();
            $url = sprintf("http://127.0.0.1:3005/startNotifier?ip=%s&port=%s&key=%s&percentToDone=%s&viewOffset=%s", $this->plexIp,$this->plexPort, $key,10,$viewOffset);
            // $url = sprintf("http://127.0.0.1:3000/startNotifier/%s/%d/%d/%d/%d",$this->plexIp, $this->plexPort,$key, 10, $viewOffset);
            $this->get($url);
        }
    }


    public function getAndParse($url){
        $data = $this->get($url);
        $xml=  simplexml_load_string($data);
        // return new SimpleXMLElement($url, 0, true);
        return $xml;
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


        $url = sprintf('%s/%s', $this->myPlexBaseUr, '/users/sign_in.json');
        $opts = array( CURLOPT_HTTPHEADER =>
                        array(
                            'X-Plex-Client-Identifier:precisa mudar',
                            'X-Plex-Client-Platform:DuneOS',
                            'X-Plex-Device-Name:Dune',
                            'X-Plex-Model:1',
                            'X-Plex-Platform:DuneOS',
                            'X-Plex-Platform-Version:1',
                            'X-Plex-Product:emplexer',
                            'X-Plex-Version:1'
                            ),
                        CURLOPT_USERPWD => "",
                        CURLOPT_POSTFIELDS =>  "",
                        CURLOPT_POST => true,
                     );

        $data = $this->get($url, $opts);
        if ($data){
            hd_print(print_r($data, true));
            Config::getInstance()->myPlexToken = $data->user->authentication_token;
            return $data->user->authentication_token;
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
