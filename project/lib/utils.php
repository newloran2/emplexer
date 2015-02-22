<?php

function _($key)
{
    return TranslationManager::getInstance()->getTranslation($key);
}

function validadeArrayTypes($arrayOfTypes, $array){
    foreach ($arrayOfTypes as $key => $value) {
        $i = gettype($array[$key]);
        if (!isset($array[$key]) || ($i != $value)){
            hd_print("valor de ". $array[$key] . " inválido $value esperado e " . (isset($array[$key])? gettype($array[$key]): null) . " encontado");
            return false;
        }
    } 
    return true;

}


class HD
{
    public static function is_map($a)
    {
        return is_array($a) &&
            array_diff_key($a, array_keys(array_keys($a)));
    }

    public static function is_url($url){
        $urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
        if (eregi($urlregex, $url))
            return true;
        else
            return false;

    }
    
    public static function print_info()
    {
        $version = `grep -i "<version>.*</version>" ../dune_plugin.xml|sed 's/[<>\/version]//g'`;
        $version = str_replace(array("\n", "\r"),"", $version);
        $version_index = `grep -i "<version_index>.*</version_index>" dune_plugin.xml|sed 's/[<>\/version_index]//g'`;
        $version_index = str_replace(array("\n", "\r"),"", $version_index);

        hd_print( " ============================   " );
        hd_print( " Emplexer                       " );
        hd_print( " Version       : $version       " );
        hd_print( " Version index : $version_index " );
        hd_print( " ============================   " );
    }    

    public static function has_attribute($obj, $n)
    {
        $arr = (array) $obj;
        return isset($arr[$n]);
    }


    public static function get_map_element($map, $key)
    {
        return isset($map[$key]) ? $map[$key] : null;
    }



    public static function starts_with($str, $pattern)
    {
        return strpos($str, $pattern) === 0;
    }



    public static function format_timestamp($ts, $fmt = null)
    {
        // NOTE: for some reason, explicit timezone is required for PHP
        // on Dune (no builtin timezone info?).

        if (is_null($fmt))
            $fmt = 'Y:m:d H:i:s';

        $dt = new DateTime('@' . $ts);
        return $dt->format($fmt);
    }



    public static function format_duration($msecs)
    {
        $n = intval($msecs);

        if (strlen($msecs) <= 0 || $n <= 0)
            return "--:--";

        $n = $n / 1000;
        $hours = $n / 3600;
        $remainder = $n % 3600;
        $minutes = $remainder / 60;
        $seconds = $remainder % 60;

        if (intval($hours) > 0)
        {
            return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
        }
        else
        {
            return sprintf("%02d:%02d", $minutes, $seconds);
        }
    }



    public static function encode_user_data($a, $b = null)
    {
        $media_url = null;
        $user_data = null;

        if (is_array($a) && is_null($b))
        {
            $media_url = '';
            $user_data = $a;
        }
        else
        {
            $media_url = $a;
            $user_data = $b;
        }

        if (!is_null($user_data))
            $media_url .= '||' . json_encode($user_data);

        return $media_url;
    }



    public static function decode_user_data($media_url_str, &$media_url, &$user_data)
    {
        $idx = strpos($media_url_str, '||');

        if ($idx === false)
        {
            $media_url = $media_url_str;
            $user_data = null;
            return;
        }

        $media_url = substr($media_url_str, 0, $idx);
        $user_data = json_decode(substr($media_url_str, $idx + 2));
    }



    public static function create_regular_folder_range($items,
        $from_ndx = 0, $total = -1, $more_items_available = false)
    {
        if ($total === -1)
            $total = $from_ndx + count($items);

        if ($from_ndx >= $total)
        {
            $from_ndx = $total;
            $items = array();
        }
        else if ($from_ndx + count($items) > $total)
        {
            array_splice($items, $total - $from_ndx);
        }

        return array
            (
                PluginRegularFolderRange::total => intval($total),
                PluginRegularFolderRange::more_items_available => $more_items_available,
                PluginRegularFolderRange::from_ndx => intval($from_ndx),
                PluginRegularFolderRange::count => count($items),
                PluginRegularFolderRange::items => $items
            );
    }



    public static function http_get_document($url, $opts = null)
    {
        $url = str_replace('//', '/', $url);
        $url = str_replace('http:/', 'http://', $url);
        $url = str_replace('https:/', 'https://', $url);


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           20);
        curl_setopt($ch, CURLOPT_USERAGENT,         'DuneHD/1.0');
        curl_setopt($ch, CURLOPT_URL,               $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,    true);
        curl_setopt($ch, CURLOPT_MAXREDIRS,         10);



        $emplexerClientHeaders = array(
            'X-Plex-Client-Capabilities' => 'protocols=shoutcast,webkit,http-video;videoDecoders=h264{profile:high&resolution:1080&level:51};audioDecoders=mp3,aac',
            'X-Plex-Client-Identifier' => 'b6d41f8a-e5ad-4987-a74b-ca12b143b0e0',
            'X-Plex-Client-Platform' => 'Dune',
            'X-Plex-Language' => 'en',
            'X-Plex-Version' => 'abc'
        );



        if (isset($opts))
        {
            foreach ($opts as $k => $v)
                curl_setopt($ch, $k, $v);
        }

        //hd_print("HTTP fetching \nurl:'$url'...");

        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $acceptedHeaders = array(200, 403);
        //hd_print('http_code = ' . $http_code);
        if ( !in_array($http_code, $acceptedHeaders) )
        {
            $err_msg = "HTTP request failed ($http_code)";
            //hd_print($err_msg);
            HD::print_backtrace();


            switch ($http_code) {
            case '0': //timeout
                $msg = "The area you are trying to access is taking too long to respond, try again. ($http_code)";
                break;
            case '404':
                $msg = "the area you are trying to access does not return any xml. ($http_code)";
                break;
            default:
                $msg = "houve um problema ao acessar essa area, try again. ($http_code)";
                break;
            }


            throw new DuneException(
                'Error: "'.$err_msg.'"',
                0,
                ActionFactory::show_error(false, $msg)
            );
        }

        //hd_print("HTTP OK ($http_code)");

        curl_close($ch);

        if ($http_code == 200) {
            return $content;
        }
        elseif ($http_code == 403)
        {
            $session_expired = array(
                'result' => 'error',
                'error' => 'Session expired',
                'error_code' => '403'
            );
            return json_encode($session_expired);
        }

    }

    public static function http_head_document($url)
    {
        $init = microtime(true);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,    true);
        curl_setopt($curl, CURLOPT_MAXREDIRS,         10);

        curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $end =  microtime(true);
        //hd_print("executed head request to $url in " . ($end - $init));
        return $info;
    }


    public static function http_post_document($url, $post_data, $opts = array())
    {
        $post  = array(CURLOPT_POST => true,CURLOPT_POSTFIELDS => $post_data);
        print_r(array_merge($post, $opts));
        return self::http_get_document($url,$post);
    }




    public static function parse_xml_document($doc)
    {
        $xml = simplexml_load_string($doc);

        if ($xml === false)
        {
            //hd_print("Error: can not parse XML document.");
            //hd_print("XML-text: $doc.");
            throw new Exception('Illegal XML document');
        }

        return $xml;
    }

    public static function getAndParseXmlFromUrl($url)
    {


        $time_start = microtime(true);
        $get_start = microtime(true);
        $doc = HD::http_get_document($url);


        $get_end = microtime(true);
        $parse_start =  microtime(true);
        $xml = HD::parse_xml_document($doc);
        $parse_end = microtime(true);
        $time_end =  microtime(true);


        $time  = $time_end - $time_start;
        $get_time = $get_end - $get_start;
        $parse_time = $parse_end - $parse_start;


        //hd_print(__METHOD__ . ':' . " xml obtained in $get_time seconds and parsed in $parse_time seconds total =$time" );
        return $xml;
    }

    public static function make_json_rpc_request($op_name, $params)
    {
        static $request_id = 0;

        $request = array
            (
                'jsonrpc' => '2.0',
                'id' => ++$request_id,
                'method' => $op_name,
                'params' => $params
            );

        return $request;
    }



    public static function get_mac_addr()
    {
        static $mac_addr = null;

        if (is_null($mac_addr))
        {
            $mac_addr = shell_exec(
                'ifconfig  eth0 | head -1 | sed "s/^.*HWaddr //"');

            $mac_addr = trim($mac_addr);

            //hd_print("MAC Address: '$mac_addr'");
        }

        return $mac_addr;
    }



    // TODO: localization
    private static $MONTHS = array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    );

    public static function format_date_time_date($tm)
    {
        $lt = localtime($tm);
        $mon = self::$MONTHS[$lt[4]];
        return sprintf("%02d %s %04d", $lt[3], $mon, $lt[5] + 1900);
    }

    public static function format_date_time_time($tm, $with_sec = false)
    {
        $format = '%H:%M';
        if ($with_sec)
            $format .= ':%S';
        return strftime($format, $tm);
    }

    public static function print_backtrace()
    {
        //hd_print('Back trace:');
        foreach (debug_backtrace() as $f)
        {
            hd_print(
                '  - ' . $f['function'] .
                ' at ' . $f['file'] . ':' . $f['line']);
        }
    }



    public static function getPlexServers($timeout=2){

        $broadcast_string="M-SEARCH * HTTP/1.1\r\n\r\n";
        $port = 32414;
        $ip='255.255.255.255';

        $response_buffer_len=4096;

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
        socket_sendto($sock, $broadcast_string, strlen($broadcast_string), 0, '255.255.255.255', $port);
        $servers = array();


        for(;;){
            $a= $sock;
            $read = array($a);
            $z = socket_select($read, $write = NULL, $except = NULL, 1 );
            if ($z >0){
                socket_recvfrom($sock,$buf,$response_buffer_len,0,$ip,$port);
                $data = explode("\n", $buf);
                $server = array();
                $server['Ip' ] = $ip;
                foreach ($data as $line ) {
                    if ($line != "" && $line != "\n" && (strpos($line, 'HTTP') === false)){
                        ////echo "$line\n";
                        $a= explode(':', $line);

                        if (count($a) >1){
                            $server[$a[0]] = $a[1];
                        }
                    }
                }
                $servers[] =$server;
            }else {
                break;
            }
        }
        socket_close($sock);
        return $servers;
    }


    public static function nfsUrlToSystemPath($nfsUrl){
        $mounts = `mount`;
        $mounts =  split("\n", $mounts);
        foreach ($mounts as $m) {
            $mount = explode(' on ', $m);
            $nfsPoint = $mount[0];
            $tmp = explode(' ', $mount[1]);
            $sysPath = $tmp[0];

            if (strstr($nfsUrl, $nfsPoint)){
                return str_replace($nfsPoint, $sysPath, str_replace('nfs://', '', $nfsUrl));
            }
        }
    }




}


?>
