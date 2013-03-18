<a  href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=newloran2%40gmail%2ecom&lc=US&item_name=emplexer&item_number=emplexer%20donation&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" alt="PayPal - The safer, easier way to pay online!"/></a>

emplexer
========


This is a work in progress plugin to integrate Dune Media Player with PLex Media Server.

To use this plugin, your dune must be 120531\_2200\_beta firmware or newer.
System storage is required to install plugins (flash storage work to but without image cache) see at <a href="http://dune-hd.com/firmware/usb_flash_drive/">Initialize System Storage</a>.

###Install and configure Plex Media Server
emplexer is only a client for PMS and not work with internal files, only work with files managed by PMS.
To install PMS all you need is at <a href="http://www.plexapp.com/help/">Plex</a>

###__Features currently supported:__


+ List Categories from PMS.
+ All Filters from categories are supported (popup button on category name).
+ Image cache suported.
+ Browse Tv shows and movies.
+ Play video files (mkv,mp4,avi,mpg, etc, play dvd folder, DVD ISO, Bluray ISO and BDMV are supported but without plex integration (don't resume from plex) and only over nfs or smb.
+ Notify play position to plex (reestar on other devices).
+ Start on last played position (from other devices and emplexer).
+ Mark as Watched/Unwatched
+ Cache poster images to better speed.
+ Many Audio and Video supported (videos that use flash or Silverlight are not supported).
+ Support direct play over nfs and smb or play over http.
+ Advanced Mapping to mount points, if your Plex is in one server but your files is on one or more nas/computers, you are able to tell this to emplexer
			
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
+ If section screen is empty, you don't configure sections in PLex.
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
		When plugins run it check if a storage is atached, if yes emplexer create a folder dune_plugin_logs on top of this storage, and make logs on file emplexer.log.
		Default Player method is over http, in this method external subtitles is not supported, but internal is.
		Currently play over nfs/smb is fully supported.
		To activate, go to Setup -> Applications -> emplexer -> connectionType and select nfs, smb or http.
		When emplexer play stream over 	nfs/smb external subtile automatic used, but, subtitle menu is not acessible (in http method too).
		Stream dvd folder, DVD ISO, Bluray ISO and BDMV folder only possible on nfs/smb method, in this case default player is used and plex not involved.
		if you submit a bug report, plese if possible submit with emplexer.log, this is make my work is much more easy :).



		Sorry, but my english is so bad!!!
