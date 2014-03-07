<?php

class Httpfs {

    private  $baseDir;
    private $movieDir;
    private $subtitleDir;
    private  $videoUrl;
    private  $subtitleUrl;
    function __construct($url){
        $this->baseDir = "/tmp/emplexer/httpfs";
        $this->movieDir = sprintf("%s/movie", $this->baseDir);
        $this->subtitleDir = sprintf("%s/subtitle", $this->baseDir);

        if(!file_exists($this->movieDir)){
            echo "vour criar o diretório " . $this->movieDir . "\n";
             mkdir($this->movieDir , 0777, true);
        }

        if(!file_exists($this->subtitleDir)){
            echo "vour criar o diretório " . $this->subtitleDir . "\n";
             mkdir($this->subtitleDir , 0777, true);
        }

        $data = Client::getInstance()->getAndParse($url);

        $this->videoUrl = sprintf("http://192.168.2.8:32400%s", $data->Video->Media->Part->attributes()->key);
        $subtitleKeys = $data->xpath("//Stream[@codec='srt' and @selected='1'][1]/@key");
        if (count($subtitleKeys) >0 ){
            // $this->subtitleUrl= Client::getInstance()->getUrl(null, (string)$subtitleKeys[0]);
            $this->subtitleUrl= sprintf("http://192.168.2.8:32400%s?encoding=utf-8", (string)$subtitleKeys[0]);
        }
    }


    public function mount(){
        ExecUtils::execute(sprintf("%s/bin/httpfs2.mac %s %s", ROOT_PATH , $this->videoUrl, $this->movieDir));
        if (isset($this->subtitleUrl)){
            file_put_contents(sprintf("%s/file.str", $this->subtitleDir), Client::getInstance()->get($this->subtitleUrl));
        }

    }

    public function umount(){
        ExecUtils::execute(sprintf("/sbin/umount %s", $this->movieDir));
    }

}


?>
