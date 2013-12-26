<?php


class Conf
{
    private $data;

    public function __construct($conf_file_name)
    {
        $this->data = array();

        $this->read_conf_file("/config/$conf_file_name") or
        $this->read_conf_file("/firmware/config/$conf_file_name");
    }

    private function read_conf_file($conf_file_path)
    {
        hd_silence_warnings();
        $lines = file($conf_file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        hd_restore_warnings();

        if ($lines === false)
        {
            hd_print("Configuration file '$conf_file_path' does not exist.");
            return false;
        }

        hd_print("Reading configuration from '$conf_file_path'...");

        for ($i = 0; $i < count($lines); ++$i)
        {
            if (preg_match('/^ *(\S+) *= *(\S+)$/', $lines[$i], $matches) != 1)
            {
                hd_print(
                    "Warning: line " . ($i + 1) . ": unknown format. " .
                    "Data: '" . $lines[$i] . "'.");
                continue;
            }

            $this->data[$matches[1]] = $matches[2];
        }

        return true;
    }

    public function updateWthPluginCookies(&$pluginCookies){
        foreach ($pluginCookies as $key => $value) {
            // echo "key = $key, value = $value\n";
            $this->$key = $value;
        }
    }

    public function __get($key){
        if ($key === "getData"){ 
            return $this->data;
        }
        return $this->data[$key]; 
    }

    public function __isset($key)
    { return isset($this->data[$key]); }

    public function set_default($key, $value)
    {
        if (!isset($this->data[$key]))
        {
            hd_print("Warning: no value for key '$key'. Using default: '$value'");
            $this->data[$key] = $value;
        }
    }
}


?>
