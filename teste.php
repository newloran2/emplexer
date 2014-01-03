<?php

require_once 'AutoLoad.php';

define('ROOT_DIR', __DIR__);


$nfs = new NFS('192.168.2.9');
$shares = $nfs->getAllNfsPaths();
print_r($shares);

// echo  explode(":", "192.168.2.9:/volume1/Animes")[1];
$share = current($shares);
echo "share = $share\n";
$nfs->mountNfs($share);

echo !is_null($nfs->isMonted($share)) ? " montado\n" : "Não montado \n";
$nfs->umountNfs($share);
echo !is_null($nfs->isMonted($share)) ? " montado\n" : "Não montado \n";


?>