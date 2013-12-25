
all: deploy

local_deploy:
	rsync -ravzup  --rsync-path=/ltu/bin/rsync  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' /Users/newloran2/Dropbox/Projeto/Dune/emplexer   ~/Sites

deploy:
	rsync -ravzup  --rsync-path=/ltu/bin/rsync  --exclude '.git'  --exclude '.sublime-project' --exclude '.sublime-workspace' --exclude 'Makefile' /Users/newloran2/Dropbox/Projeto/Dune/emplexer  root@192.168.2.44:/D/dune_plugins

pack: clean
	zip -r dune_plugin_emplexer.zip  *

packNoRemove:
	zip -r dune_plugin_emplexer.zip  *

clean:
	@echo "removendo pacote antigo"
	rm dune_plugin_emplexer.zip