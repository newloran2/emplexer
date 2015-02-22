all: local_deploy 

local_deploy:
	rsync -ravzup  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' --exclude *.zip --exclude '*.swp'	--exclude '*.swo' ~/Dropbox/Projeto/Dune/emplexer/project/ /Library/WebServer/Documents/emplexer

deploy_303d:
	rsync -ravzup  --rsync-path=/ltu/bin/rsync  --exclude '.git' --exclude '*.swp' --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' --exclude *.zip /Users/newloran2/Dropbox/Projeto/Dune/emplexer/project/*  root@192.168.2.44:/D/dune_plugins/emplexer2

deploy_h1:
	rsync -ravzup  --rsync-path=/ltu/bin/rsync  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' --exclude *.zip /Users/newloran2/Dropbox/Projeto/Dune/emplexer/*  root@192.168.2.7:/D/dune_plugins/emplexer2

run_lem:
	~/Dropbox/Projeto/Dune/emplexer/project/bin/lem-mac ~/Dropbox/Projeto/Dune/emplexer/project/bin/emplexer.lua
pack: clean packNoRemove

packNoRemove:
	(cd ~/Dropbox/Projeto/Dune/emplexer/project && zip -r dune_plugin_emplexer2.zip * && mv dune_plugin_emplexer2.zip ..)
packTgz:
	tar -zcvf 2.tgz *

clean:
	rm dune_plugin_emplexer2.zip
