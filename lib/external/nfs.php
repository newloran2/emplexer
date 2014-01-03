<?php
// require_once 'lib/ExecUtils.php';
/**
 * Created by JetBrains PhpStorm.
 * User: newloran2
 * Date: 8/25/13
 * Time: 7:42 PM
 * To change this template use File | Settings | File Templates.
 */

class NFS {
    // private $command = '/firmware/bin/showmount -d --no-headers';
    private $command = '/usr/bin/showmount -d ';
    private $mountCommand = '/sbin/mount -o nolock ';
    private $umountCommand = '/sbin/umount ';
    private $mountCheck = "/sbin/mount | grep -i ";
    private $mountPoint =  '/tmp/emplexer3';
    private $exports = array();
    private $ip;

    function __construct($ip)
    {
        $this->ip = $ip;

        if (!file_exists($this->mountPoint)){
            mkdir($this->mountPoint);
        }
        $data = ExecUtils::execute($this->command . " $ip");
        $data =preg_filter("/^(?!\/).*/", "", $data);
        $paths = array_filter(explode("\n", $data));
        // print_r($paths);
        $this->exports = $paths;
    }


    public function getNfsPath($directory){
        if (array_search($directory, $this->exports)){
            return  "nfs://$this->ip:$directory";
        } else {
            return null;
        }
    }

    public function getAllNfsPaths(){
        $exports = array();
        foreach ($this->exports as $value) {
            $exports[$value] = $this->getNfsPath($value);
        }
        return $exports;
    }

    public function getLocalDirForShare($share){
        $share = str_replace("nfs://", "", $share);
        $a = explode(":", $share);
        $dir = $this->mountPoint . $a[1];
        return $dir;
    }

    public function mountNfs($share){
        $share = str_replace("nfs://", "", $share);
        echo "tentando montar o share $share \n";
        $dir = $this->getLocalDirForShare($share);
        echo $dir . "\n";
        if (!file_exists($dir)){
            if (!mkdir($dir, 0777, true)){
                echo "não foi possivel criar os diretorios " . $a[1];
            }
        }
        if (!$this->isMonted($share)){
            echo "mountando $share\n";
            ExecUtils::execute($this->mountCommand . " $share $dir");
        }


    }

    public function isMonted($share){
        $share = str_replace("nfs://", "", $share);
        $mount = ExecUtils::execute($this->mountCheck .  $share);
        echo "mount = $mount\n";
        return is_null($mount);
    }

    public function umountNfs($share){
        $share = str_replace("nfs://", "", $share);
        $dir =  $this->getLocalDirForShare($share);
        ExecUtils::execute($this->umountCommand . " $dir");
    }

    // public function getPathExportForPath($nfsPath){
    //     $nfs = $this->decomposeNfsPath($nfsPath);
    //     $export = null;
    //     foreach ($this->exports[$nfs['ip']] as $path) {
    //         if (strstr($nfsPath, $path)){
    //             $export = $nfs['path'];
    //             break;
    //         }
    //     }
    //     return $export;
    // }

    /**
     * decompose an nfs path and return an array with ip and path;
     * nfsPath example nfs://192.168.2.8:/volume2/Filmes2/The.Wicked.2013.720p.BluRay.x264-SONiDO%20[PublicHD]/thew_720_son.mkv
     * @param $nfsPath
     * @return array
     */
    // public function decomposeNfsPath($nfsPath){
    //     $n = explode(':', $nfsPath);
    //     $a = array();
    //     $a['ip'] = str_replace('//', '', $n[1]);
    //     $a['path'] = $n[2];

    //     return $a;
    // }


    // public function fileExists($nfsPath){
    //     $n = explode(':', $nfsPath);
    //     $type = $n[0];
    //     $ip = str_replace('//', '', $n[1]);
    //     $path = $n[2];
    //     $this->refreshExportsForIp($ip);

    // }
}


?>