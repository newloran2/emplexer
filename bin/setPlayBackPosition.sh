echo "executado " >> /tmp/1.txt
sleep 3
while true
do
	isPlaing=`grep -i playing /tmp/run/ext_command.state`
	echo "conteud de isPlaing = $isPlaing"	 >> /tmp/1.txt
	if [ ! -z "$isPlaing" ]; then
		echo "iniciou o play, vou executar" >> /tmp/1.txt
		wget  "http://127.0.0.1/cgi-bin/do?cmd=set_playback_state&position=1000&speed=256" -O /tmp/teste.txt -q
		break
	else
		usleep 5000
	fi
done
echo  "executei"  >> /tmp/1.txt
