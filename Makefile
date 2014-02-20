
all: local_deploy deploy_303d

local_deploy:
	rsync -ravzup  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' --exclude *.zip	 ~/Dropbox/Projeto/Dune/emplexer   /Library/WebServer/Documents/

deploy_303d:
	rsync -ravzup  --rsync-path=/ltu/bin/rsync  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' --exclude *.zip /Users/newloran2/Dropbox/Projeto/Dune/emplexer/*  root@192.168.2.44:/D/dune_plugins/emplexer2

deploy_h1:
	rsync -ravzup  --rsync-path=/ltu/bin/rsync  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' --exclude *.zip /Users/newloran2/Dropbox/Projeto/Dune/emplexer/*  root@192.168.2.7:/D/dune_plugins/emplexer2

pack: clean
	zip -r dune_plugin_emplexer2.zip  *

packNoRemove:
	zip -r dune_plugin_emplexer2.zip  *

packTgz:
	tar -zcvf 2.tgz *

clean:
	rm dune_plugin_emplexer2.zip