emplexer
========

This is a work in progress plugin to integrate Dune Media Player with PLex Media Server.

To use this plugin, your dune must be 120531_2200_beta firmware or newer.
System storage is required to install plugins see at <a href="http://dune-hd.com/firmware/usb_flash_drive/">Initialize System Storage</a>.

###Install and configure Plex Media Server
emplexer is only a client for PMS and not work with internal files, only work with files managed by PMS.
To install PMS all you need is at <a href="http://www.plexapp.com/help/">Plex</a>

###__Features currently supported:__


+ List Categories from PMS.
+ All Filters from categories are supported (popup button on category name).
+ Image cache suported, but currenty deactivated.
+ Browse Tv shows and movies (audios and photos currenty not tested).
+ PLay video files (mkv,mp4,avi,mpg and other, play dvd folder currenty partialy supported).
+ Notify play position to plex (reestar on other devices).
+ Start on last played position (from other devices and emplexer).
			
###how to use this plugin?
+ Download only dune\_plugin\_emplexer.zip
+ Put dune\_plugin\_emplexer.zip on place accebible by Dune (on local storage, or network storage).
+ In Dune file explorer, hit enter over the file dune\_plugins\_emplexer.zip.
+ Wait some few seconds, an message like this:
 <img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/success_install.png">
+ Now emplexer is installed in your Dune.
+ Go to Applications > emplexer and press enter. One setup box like the image below should appear (same screen on Setup > Applications > emplexer).
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/config_modal.png">
+ Enter the ip address of your Plex installation on Plex IP field. 
+ If you have not changed the default plex port leave the port field and hit enter the save button.
+ A success message should appear.
+ Hit OK button am press enter again in emplexer and a list of sections registered in your plex should be appear.
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/section_screen.png">

###Using emplexer

+ If you see the section screen, emplexer and plex working.
+ When you enter in a section this is equivalent to filter all in plex.
+ If you need a specific filter from plex, press __pop up menu button__ on remote when a section is selected in section screen.
+ A pop up like below should be appear:
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/anime_filters.png">
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/movies_filters.png">
+ Enter on a movie section and to see something like below:
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/movies_big_thumbs_with_details.png">
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/movies_big_without_detail.png">
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/movies_text_list_with_details.png">

+ TV Show filters have a 2 subnivels, first is Series Name.
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/tv_shows_big_thumbs_with_details.png">

+ Second is Season.
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/tv_show_list_with_details.png">
<img src="https://dl.dropbox.com/u/5493320/emplexe_plugins_screenshots/tv_shows_seasons_with_big_thumbs%20and%20detail.png">


	PS:
		When plugins run it check if a storage is atached, if yes emplexer create a folder dune_plugin_logs on top of this storage, and log make logs on file emplexer.log.
		Default Player method is over http, in this method external subtitles is not supported, but internal is.
		Currently play over nfs is supported but need more tests.
		To activate, edit emplexer_config.php and set true do constant USE_NFS, and restart plex (stand by OK).
		When emplexer play stream over 	nfs external subtile automatic used, but, subtitle menu is not acessible (in http mehod too).
		Stream dvd folder only possible on nfs method, in this case default player is used and plex not involved.
		if you submit a bug report, plese if possible submit with emplexer.log, this is make my work is much more easy :).



		Sorry, but my english is so bad!!!