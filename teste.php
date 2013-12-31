<?php
$a = "titulo = plex_item_field:title  plex_item_field:title  plex_item_field:title2||plex_field:identifier";

$title = "erik";
$title2= "clemente";

$items =  explode("||", $a);

// print_r($items);

foreach ($items as $value) {
    // echo ("value = $value\n");
    $ret = array();
    preg_match_all("/plex_item_field:[a-zA-Z0-9]+/", $value, $ret);
    $ret = array_unique($ret[0]);
    if ($ret && count ($ret) >0 ){
        print_r($ret);
    }

    $str = "";
    $newArray = array();
    foreach ($ret as $v) {
        $s = explode(":", $v);
        // print_r($s);
        $newArray[] = $s[1];
        // print_r($ret);
        // $str =

    }
    print_r($newArray);
}

?>