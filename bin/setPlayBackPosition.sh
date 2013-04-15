echo "executado  delay=$1 position=$2 plugin_dir=$3 base_url=$4 id=$5 pooling_time=$6 mark_time=$7 " > /tmp/1.txt
sleep $1
while true
do
	isPlaing=`grep -i playing /tmp/run/ext_command.state`
	echo "conteud de isPlaing = $isPlaing"	 >> /tmp/1.txt
	if [ ! -z "$isPlaing" ]; then
		echo "iniciou o play, vou executar http://127.0.0.1/cgi-bin/do?cmd=set_playback_state&position=$2&speed=256" >> /tmp/1.txt
		wget  "http://127.0.0.1/cgi-bin/do?cmd=set_playback_state&position=$2&speed=256" -O /tmp/teste.txt -q
		break
	else
		#sleep for 0,5 seconds
		usleep 500000
	fi
done
echo  "executei"  >> /tmp/1.txt
commando="$3/bin/plex_notify.sh $5 $6 $4 $7 >> 1 2>/dev/null &"
echo "vou execurtar o startNotify com o comando $commando" >> /tmp/1.txt
sh $3/bin/plex_notify.sh $5 $6 $4 $7 >> 1 2>/dev/null &

