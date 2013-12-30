<?php

require_once 'lib/utils.php'; 
$call_ctx_json = '{"op_type_code":"get_folder_view","op_id":"4","input_data":{"media_url":"main"},"plugin_cookies":{"plexIp":"192.168.2.8","plexPort":"32400","connectionMethod":"nfs","hasSeenCaptionColor":"15","notSeenCaptionColor":"0","handler_id":"controls_emplexer_setup_screen","control_id":"btnSalvar","selected_control_id":"btnSalvar","selected_media_url":"emplexer_setup_screen","showOnMainScreen":"yes","useVodPlayback":"true","/Volumes/MoviesThreeD":"nfs://192.168.2.9:/volume2/MoviesThreeD","/Volumes/Animes":"nfs://192.168.2.9:/volume1/Animes","/Volumes/Animes2":"nfs://192.168.2.9:/volume2/Animes2","/Volumes/Filmes":"nfs://192.168.2.9:/volume1/Filmes","/Volumes/Download/FilmeDeTeste":"nfs://192.168.2.9:/volume1/Download/FilmeDeTeste","/Volumes/Filmes2":"nfs://192.168.2.9:/volume2/Filmes2","/Volumes/music":"nfs://192.168.2.9:/volume1/music","/Volumes/Fotos":"/tmp/mnt/volume1/Fotos","/Volumes/Series":"nfs://192.168.2.9:/volume1/Series","parent_media_url":"emplexer_setup_screen","useCache":"false","resumePlayBack":"ask","action_type":"apply","_DISKSTATION/Filmes2":"nfs://192.168.2.9:/volume2/Filmes2","Q:/":"nfs://192.168.2.9:/volume2/Filmes2/","defaultMovieFilter":"unwatched","defaultShowFilter":"unwatched","defaultArtistFilter":"all","screen.emplexer_base_channel.view_idx":"2","channel_selected_index":"2","screen.plex_root_list.view_idx":"0","screen.emplexer_video_list.view_idx":"2","templateViewNumber":"{\"templateMovie\":29}"}}';
$post_data = "data=$call_ctx_json";
// echo($call_ctx_json . "\n");
$ret = HD::http_post_document('http://192.168.2.41/emplexer/index.php', $post_data);
echo($ret . "\n");
// hd_print(__METHOD__ . ':' . print_r(json_decode($ret), true));

?>