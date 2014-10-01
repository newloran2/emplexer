<?php

/**
*
*/
class CacheManager  extends CURL
{
    private $downloadDirectory;

    function __construct($downloadDirectory=null) {
        $this->downloadDirectory =  $downloadDirectory;
    }

    public function addMultiSessions($urls, $opts=false){
        $toBedownload=array();
        foreach ($urls as $url) {
            $a = $this->addSession($url, $opts);
            if ($a){
                $toBedownload[] = $a;
            }
        }
        return $toBedownload;
    }

    public function addSession( $url, $opts = false ){

        $name = sprintf('%s/%s.jpg', $this->downloadDirectory, md5($url) );
        $ret  = null;
        if (!file_exists($name) || filesize($name) <1){
            // echo "arquivo $name não existe\n";
            $file = fopen($name, 'w');
            $opts[CURLOPT_FILE] = $file;
            parent::addSession($url, $opts);
            return  $url;
        } else {
            // echo "arquivo $name existe\n";
            return $name;
        }
    }

}

?>