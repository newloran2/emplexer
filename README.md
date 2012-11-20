emplexer
========

This is a work in progress plugin to integrate Dune Media Player with PLex Media server.


Features currently supported:

	List Categories from PMS.
	All Filters From categories are supported (popup button on category name).
	Image cache.
	Browse Tv shows and movies (audios and photos currenty not tested).
	Play video files (mkv,mp4,avi,mpg and other, play dvd folder currenty partialy supported).
	Notify play position to plex (reestar on other devices).
	Start on last played position (from other devices and emplexer).




How to use wip plugins?
	
	Create a folder named dune_plugins on top of your main HD (pen drive, memory card or other).
	Create a folder named emplexer and put code in there.
	Create a folder named dune_plugin_logs on top of your main HD.
		On this folder plugins make logs (this is very important for bug reports).
	Edit File emplexer_config.php  and set your plex base url in DEFAULT_PLEX constant (EX: http://192.168.2.9:32400) (sorry, this step is temporary).
	Reestart your Dune (stand by OK).
	A new folder named emplexer apear on plugin section.



PS:
	Default player method is over http, in this method external subtitles is not supported, but internal is.
	Currently play over nfs is supported but need more tests
		To activate, edit emplexer_config.php and set true do constant USE_NFS, and restart plex (stand by OK).
		When emplexer play stream over nfs external subtile automatic used, but, subtitle menu is not acessible (in http mehod too).
	Stream dvd folder only possible on nfs method, in this case default player is used and plex not involved.

	if you submit a bug report, plese if possible submit with emplexer.log, this is make my work is much more easy :).



Sorry, but my english is so bad!!!
