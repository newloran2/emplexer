<?php
ini_set ("display_errors", "1");
error_reporting(E_ALL);
header('content-type: image/png');
function convert($size)
 {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 }

 $image  = imagecreate(300,300);


 $dark_grey  = imagecolorallocate($image , 102, 102, 102);
 $white  = imagecolorallocate($image , 255, 255, 255);

$font_path = '/Library/Fonts/Arial.ttf';

 $string =  "hello world";


imagettftext($image, 40, -45, 70, 70, $white, $font_path, $string);
imagepng($image);

imagedestroy($image);
