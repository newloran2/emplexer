
<?php

define('LD_LIBRARY_PATH_FIX', 'LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/firmware/lib');
define('SMBTREE', '/firmware/bin/smbtree -A /tmp/emplexer_auth.conf|awk  \'{print $1}\'');
// define('SMBTREE', '/firmware/bin/smbtree -A /tmp/auth.txt|awk  \'{print $1}\'');

define('NMLOOkUP', LD_LIBRARY_PATH . '/firmware/bin/nmblookup|awk \'{print $1}\'');

class SMBServer {

    private $serverName;
    private $serverIp;
    private $shares = array();
    // private $env;
    // private $cwd;
    // private $cmd;

    function __construct($serverName) {
        $this->serverName = $serverName;
        $this->serverIp = $this->getServerIp($serverName);
        $this->fillShares();
    }

    private function fillShares() {
        // echo " vou executar\n";
        $command = '/tango/firmware/bin/smbtree  -U admin  -A /tmp/emplexer_auth.conf --debuglevel=0 --server-shares=' . $this->serverName;
        $strShares = ExecUtils::execute($command);
        // echo "strShares = $strShares\n";
        $tmpShares = array_filter(explode("\n", $strShares));

        // print_r($tmpShares);

        foreach ($tmpShares as $share) {
            $s = explode("\t", $share);
            $this->shares[$s[0]] = $this->getUnixSmbPath($s[0]);
        }
    }



    private function getServerIp($serverName) {
        $command = "/firmware/bin/nmblookup $serverName|tail -n 1| awk '{print $1}'";
        $ip = ExecUtils::execute($command);
        return str_replace("\n", "", $ip);
    }

    public function addShare($shareName) {
        $this->shares[$shareName] = $shareName;
    }

    public function getUnixSmbPath($shareName) {
        return 'smb://' . $this->serverIp . '/' . $shareName;
    }

}

/**
 * Class to LookUp Smb shares
 * This Class use dune smbtree and nmblookup executable
 * To use smbclient the  /tango/firmware/lib must be in LD_LIBRARY_PATH system variable
 */
class SMBLookUp {

    private $workgroup;
    private $servers = array();

    public function __construct() {
        // echo "oi1\n";
        // $this->workgroup = $workgroup;
        $domains = $this->listDomains();
        $this->listServersForDomains($domains);


    }


    public function listDomains(){
        $domains = ExecUtils::execute("/firmware/bin/smbtree -A /tmp/emplexer_auth.conf" . " --domains");
        return  array_filter(explode("\n", $domains));
    }

    public function listServersForDomains($domains = array()){
        $command = "/firmware/bin/smbtree -A /tmp/emplexer_auth.conf --debuglevel=0 --workgroup-servers=";
        $servers = array();
        foreach ($domains as $domain) {
            $output = ExecUtils::execute($command . $domain);
            $tmpServers = array_filter(explode("\n", $output));
            foreach ($tmpServers as $server) {
                $s = explode("\t", $server);
                $smbServer = new SMBServer($s[0]);
                $servers[$s[0]] = $smbServer;
            }
            $this->servers[$domain] = $servers;

        }
        // print_r($this->servers);


    }

    public function getServers() {
        return $this->servers;
    }

}

// echo "vou iniciar\n";
// $a = new SMBLookUp();
// print_r($a->getServers());
?>