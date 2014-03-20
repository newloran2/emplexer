<?php

class NFS {
    // private $command = '/firmware/bin/showmount -d --no-headers';
    private $showMountCommand = '/tango/firmware/bin/showmount  -d';
    //private $showMountCommand = '/usr/bin/showmount -d ';
    private $mountCommand = '/bin/mount -o nolock ';
    // private $mountCommand = '/sbin/mount -o nolock ';
    private $mountCheck = "/bin/mount | grep -i ";
    // private $umountCommand = '/sbin/umount ';
    private $umountCommand = '/bin/umount ';
    // private $mountPoint =  '/Library/WebServer/Documents/emplexer/teste';
    private $mountPoint =  '/tmp/emplexer';
    private $exports = array();
    private $ip;
    private $iterators =  array();

    function __construct($ip)
    {
        // var_dump($ip);
        if (filter_var($ip, FILTER_VALIDATE_IP)){
            $this->ip = $ip;
        } else {
            $this->ip = parse_url($ip,PHP_URL_HOST);

        }
        // var_dump($this->ip);
        $this->updateWithIp($this->ip);
    }

    public static function isANFSServer($ip){
        // $a  =  ExecUtils::execute("/usr/bin/showmount -d $ip", 5) ;
        $a  =  ExecUtils::execute(sprintf("%s %s",$this->mountCommand, $ip), 5) ;
        // hd_print_r("retorno de exec = ", $a);
        return $a !== "timeout";
    }

    public function updateWithIp($ip){

        $this->umountAll();
        if (!file_exists($this->mountPoint)){
            mkdir($this->mountPoint);
        }


        $data = ExecUtils::execute($this->showMountCommand . " $ip", 5);
        $data =preg_filter("/^(?!\/).*/", "", $data);
        $paths = array_filter(explode("\n", $data));
        $this->exports = $paths;
        $mkdirPaths = array();
        foreach ($paths as $export) {
            $mkdirPath = sprintf("%s%s ", $this->mountPoint, $export);
            $mkdirPaths[] = $mkdirPath;
            $this->iterators["nfs://".$this->ip . ':'. $export] = new NfsFileSystemIterator($this->ip, $export, $mkdirPath);
        }

    }

    public function mountAll(array $mountPoints = null){
        $mountedCount =  0;
        if ($mountPoints != null){ // mount only mountPoints passed
            foreach ($mountPoints as $mountPoint) {
                if (isset($this->iterators[$mountPoint])){
                    $this->iterators[$mountPoint]->mount();
                    $mountedCount++;
                }
            }
        } else { //mount all
            foreach ($this->iterators as $it) {
                $it->mount();
                $mountedCount++;
            }
        }
        return $mountedCount;
    }

    public function umountAll(array $mountPoints = null){
        $uMountedCount =  0;
        if ($mountPoints != null){ // mount only mountPoints passed
            foreach ($mountPoints as $mountPoint) {
                if (isset($this->iterators[$mountPoint])){
                    $this->iterators[$mountPoint]->uMount();
                    unset($this->iteratorsp[$mountPoint]);
                    $uMountedCount++;
                }
            }
        } else { //mount all
            foreach ($this->iterators as $it) {
                $it->uMount();

                $uMountedCount++;
            }
            unset($this->iterators);

        }
        return $uMountedCount;
    }

    public function getAllNfsPaths(){
        $data = array();
        foreach ($this->exports as $value) {
            $data[] = sprintf('nfs://%s:%s',$this->ip,$value);
        }
        return $data;
    }

    public function getIteratorForNfsPath($share){
        // var_dump($share);
        // var_dump($this->iterators);
        if (isset($this->iterators[$share])){
            // var_dump("tem o caminho $share");
            // var_dump($this->iterators[$share]);
            return $this->iterators[$share];
        }
        var_dump("não tem o caminho $share");
        return null;
    }

    public function mountNfs($share){
      if (isset($this->iterators[$share])){
            $this->iterators[$share]->mount();
            return $this->iterators[$share];
        }
        return null;

    }

    public function isMounted($share){
        $mount = $this->iterators[$share]->isMounted;
        // echo "mount = $mount\n";
        return $mount;
    }

    public function umountNfs($share){
        if (isset($this->iterators[$share])){
            $this->iterators[$share]->unMount();
        }

    }
}


?>
