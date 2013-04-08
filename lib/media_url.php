<?php


class MediaURL
{
    // Original media-url string.
    private $str;

    // If media-url string contains map, it's decoded here.
    // Null otherwise.
    private $map;

    

    private function __construct($str, $map)
    {
        $this->str = $str;
        $this->map = $map;
    }

    
    

    public function __set($key, $value)
    {
        if (is_null($this->map))
            $this->map = (object) array();
        
        $this->map->{$key} = $value;
    }

    public function __unset($key)
    {
        if (is_null($this->map))
            return;

        unset($this->map->{$key});
    }

    public function __get($key)
    {
        if (is_null($this->map))
            return null;

        return isset($this->map->{$key}) ? $this->map->{$key} : null;
    }

    public function __isset($key)
    {
        if (is_null($this->map))
            return false;

        return isset($this->map->{$key});
    }

    
    

    public function get_raw_string()
    { return $this->str; }

    
    

    public static function encode($m)
    { 
        hd_print('resultado para encodar = ' .  $m);
        hd_print('resultado para encodar = ' .  print_r($m, true));
        return json_encode($m); 
    }

    

    public static function decode($s)
    {
        // $s = str_replace(array("\r\n", "\r", "\n",  "\""), " ", utf8_encode($s));

        // $s = str_replace(array("\n","\r"),"",$s); 

        hd_print('resultado para decodificar = ' . print_r($s, true));
        if (substr($s, 0, 1) !== '{')
            return new MediaURL($s, null);


        $json =  json_decode($s);
        hd_print("decodificando $s  resultado = " . print_r($json, true));
        return new MediaURL($s,$json);
    }
}


?>
