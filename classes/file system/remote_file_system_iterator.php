<?php


/**
*
*/
abstract class RemoteFileSystemIterator implements Iterator
{

    protected $mountPoint;
    protected  $remotePath;
    protected $ip;
    protected $port;
    protected $type;
    protected $isMountedCommand ;
    protected $mountComand;
    protected $unMountCommand;
    private $localPath;
    private $flags;
    private $filesystemIterator;

    function __construct($ip, $port, $path, $mountPoint, $type, int $flags = null){
        // echo "mountPoint = $mountPoint\n";
        $mountPoint = trim($mountPoint);
        if (!file_exists($mountPoint)){
            echo "vou criar o path $mountPoint teste\n";
            mkdir($mountPoint, 0777, true);
        }
        $this->ip=$ip;
        $this->port=$port;
        $this->remotePath=$path;
        $this->type = $type;
        $this->mountPoint = $mountPoint;
        $this->localPath = $mountPoint;
        $this->isMountedCommand = sprintf('/sbin/mount -t %s | grep -i %s|grep -i %s', $this->type, $this->ip, $this->remotePath);
        $this->unMountCommand = sprintf('/sbin/umount %s', $this->mountPoint);
        $this->flags = $flags;
    }

    public abstract function getUrl();

    public function isMounted(){
        $ret = ExecUtils::execute($this->isMountedCommand);
        return !is_null($ret);
    }

    public function mount(){
        echo "mount";
        try {
            ExecUtils::execute($this->mountComand);
            if ($this->isMounted()){
                $this->filesystemIterator =  new FilesystemIterator($this->mountPoint);
            }
        } catch (Exception $e) {
            //if A Exception throws i try to mount a directory inside another mounted directory
            echo "diretorio existente ", $this->mountPoint , "\n";
            echo $e->getMessage(),  "\n";
        }

    }

    public function uMount(){
        unset($this->filesystemIterator);
        ExecUtils::execute($this->unMountCommand);

    }

    //emcapsulate filesystemIterator methods

    public function  current (){
        return $this->filesystemIterator->current();
    }
    public function  getATime (){
        return $this->filesystemIterator->getATime();
    }
    public function  getBasename (string $suffix){
        return $this->filesystemIterator->getBasename($suffix);
    }
    public function  getCTime (){
        return $this->filesystemIterator->getCTime();
    }
    public function  getExtension (){
        return $this->filesystemIterator->function();
    }
    public function  getFilename (){
        return $this->filesystemIterator->function();
    }
    public function  getGroup (){
        return $this->filesystemIterator->getGroup();
    }
    public function  getInode (){
        return $this->filesystemIterator->getInode();
    }
    public function  getMTime (){
        return $this->filesystemIterator->getMTime();
    }
    public function  getOwner (){
        return $this->filesystemIterator->getOwner();
    }
    public function  getPath (){
        return $this->filesystemIterator->getPath();
    }
    public function  getPathname (){
        return $this->filesystemIterator->function();
    }
    public function  getPerms (){
        return $this->filesystemIterator->getPerms();
    }
    public function  getSize (){
        return $this->filesystemIterator->getSize();
    }
    public function  getType (){
        return $this->filesystemIterator->getType();
    }
    public function  isDir (){
        return $this->filesystemIterator->isDir();
    }
    public function  isDot (){
        return $this->filesystemIterator->isDot();
    }
    public function  isExecutable (){
        return $this->filesystemIterator->function();
    }
    public function  isFile (){
        return $this->filesystemIterator->isFile();
    }
    public function  isLink (){
        return $this->filesystemIterator->isLink();
    }
    public function  isReadable (){
        return $this->filesystemIterator->isReadable();
    }
    public function  isWritable (){
        return $this->filesystemIterator->isWritable();
    }
    public function  key (){
        return $this->filesystemIterator->key();
    }
    public function  next (){
        return $this->filesystemIterator->next();
    }
    public function  rewind (){
        return $this->filesystemIterator->rewind();
    }
    public function  seek ( int $position ){
        return $this->filesystemIterator->seek($position);
    }
    public function  valid (){
        return $this->filesystemIterator->valid();
    }
}