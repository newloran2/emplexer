<?php


/**
*
*/
class SmbFileSystemIterator extends RemoteFileSystemIterator
{

    function __construct($ip, $path, $mountPoint, $workgroup, $user, $password ) {
        parent::__construct($ip,null,$path, $mountPoint, 'smb');
        $this->mountComand = sprintf('/sbin/mount %s:%s %s', $this->ip,$this->remotePath, $this->mountPoint);
    }

}

?>