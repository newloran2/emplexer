#!/bin/sh


key=$1
sleep_time=$2
plex_base_url=$3

time_to_stop=$4
delay_to_position="$5"

# echo "1=$1|$key, 2=$2|$sleep_time, 3=$3|$plex_base_url, 4=$4|$time_to_stop, 5=$5|$delay_to_position" > /D/dune_plugin_logs/emplexer_run_position.txt


 
# if [[  [[$delay_to_position]] ]]; then
# 	echo "entrey em delay_to_position vou esperar por 2 segundos delay_to_position=$delay_to_position" >> /D/dune_plugin_logs/emplexer_run_position.txt
# 	sleep 8
# 	echo "10 segundos terminados" >> /D/dune_plugin_logs/emplexer_run_position.txt
# 	/usr/bin/wget -q -O /tmp/emplexer.txt "http://192.168.2.7/cgi-bin/do?cmd=set_playback_state&position=$delay_to_position"
# 	echo "pronto chamei" >> /D/dune_plugin_logs/emplexer_run_position.txt
# 	sleep 5
# fi

playback_duration=`grep -i playback_duration  /tmp/run/ext_command.state |awk -F= '{print $2}'`
compare_duration=$((playback_duration*1000))
last_playback_position=0


trap "/usr/bin/wget -q -O /tmp/emplexer.txt '$plex_base_url:/progress?key=$key&identifier=com.plexapp.plugins.library&time=$last_playback_position&state=stopped'" EXIT

echo "plex notify iniciado key=$key sleep_time=$sleep_time plex_base_url=$plex_base_url time_to_stop=$time_to_stop playback_duration=$playback_duration compare_duration=$compare_duration playback_position=$playback_position" >> /D/dune_plugin_logs/emplexer_run_position.txt
echo "1=$1|$key, 2=$2|$sleep_time, 3=$3|$plex_base_url, 4=$4|$time_to_stop, 5=$5|$delay_to_position" >> /D/dune_plugin_logs/emplexer_run_position.txt

while [[ 1 ]]; do
	echo "" >> /D/dune_plugin_logs/emplexer_run_position.txt
	playback_position=`grep -i playback_position  /tmp/run/ext_command.state |awk -F= '{print $2}'`
	# caso falte time_to_stop segundos para acabar o video considero que deve acabar agora mesmo
	# dessa forma evito de ficar faltando pucos milisegundos para o plex considerar que o video foi visto por completo
	# adiciono + 10 segundos só por garantia pois no plex o tempo é em milisegundos e no dune é em segundos
	if [[ $((playback_duration - playback_position)) -le $time_to_stop ]]; then
		playback_position=$((playback_duration+10))
		echo "foi menor $playback_position $playback_duration "  >> /D/dune_plugin_logs/emplexer_run_position.txt
	fi

	playback_position=$((playback_position*1000))	
	if [[ $playback_position -le 0  || $last_playback_position -ge $compare_duration ]]; then
		/usr/bin/wget -q -O /tmp/emplexer.txt "$plex_base_url:/progress?key=$key&identifier=com.plexapp.plugins.library&time=$last_playback_position&state=stopped"
		if [[ $last_playback_position -ge $compare_duration ]]; then
			/usr/bin/wget -q -O /tmp/emplexer.txt "$plex_base_url:/scrobble?key=$key&identifier=com.plexapp.plugins.library"
		fi
		
		echo "$plex_base_url:/progress?key=$key&identifier=com.plexapp.plugins.library&time=$playback_position&state=stopped" >> /D/dune_plugin_logs/emplexer_run_position.txt

		exit
	fi

	/usr/bin/wget -q -O /tmp/emplexer.txt "$plex_base_url:/progress?key=$key&identifier=com.plexapp.plugins.library&time=$playback_position&state=playing"
    echo "$plex_base_url:/progress?key=$key&identifier=com.plexapp.plugins.library&time=$playback_position&state=playing" >> /D/dune_plugin_logs/emplexer_run_position.txt
    if [[ $playback_position -gt 0  ]]; then
    	last_playback_position=$playback_position
    	echo "last_playback_position = $last_playback_position" >> /D/dune_plugin_logs/emplexer_run_position.txt
    fi    
	sleep $sleep_time
done


