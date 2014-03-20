<?php
/**
 * Created by JetBrains PhpStorm.
 * User: newloran2
 * Date: 8/25/13
 * Time: 7:57 PM
 * To change this template use File | Settings | File Templates.
 */

define("SIGTERM", 143);

class ExecUtils {



    /**
     * Exec one Command and return the output
     * This method use the default env  'LD_LIBRARY_PATH' => '/tango/firmware/lib'
     * any extra env setted are append to the env
     * @param $command
     * @param array $extraEnv
     * @return string
     */
    public static function execute($command, $timeout=0, $extraEnv=array()) {
        $timeoutScript = sprintf("%s/bin/timeout.sh", ROOT_PATH);
        if ($timeout > 0 ){
            //set command to use timeout script and redirect stderr to stdout for timeout error catch
            $command =  sprintf("%s -t %d %s 2>&1", $timeoutScript, $timeout, $command);
        }
        hd_print("command =$command");
        $command = trim($command);
        $output = "";

        $descriptorspec = array
        (
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );
        $env = array('LD_LIBRARY_PATH' => '/tango/firmware/lib');
        // $env = array();


        $process = proc_open($command, $descriptorspec, $pipes, '/tmp', $env);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            $return_value = proc_close($process);
        }

        switch ($return_value) {
            case 0:
                return $output;
                break;
            case SIGTERM:
                return "timeout";
                break;
            default:
                return  "";
                break;
        }
    }

}
