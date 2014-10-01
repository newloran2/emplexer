<?php


/**
*
*/
class NfsFileSystemIterator extends RemoteFileSystemIterator
{

    function __construct($ip, $path, $mountPoint ) {
        parent::__construct($ip,null,$path, $mountPoint, 'nfs');
        // echo "teste  remotePath ", $this->remotePath, " mountPoint ",  $this->mountPoint , "\n";
        $this->mountComand = sprintf('/bin/mount -o nolock %s:%s %s', $this->ip,$this->remotePath, $this->mountPoint);
    }

    public function getUrl(){
        return sprintf('nfs://%s:%s', $this->ip, $this->remotePath);
    }

}

?>
