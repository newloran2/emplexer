<?php

/**
 * Class TranscodeManager
 * @author newloran2
 http://192.168.2.8:32400/video/:/transcode/universal/start.m3u8?path=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F20253&
mediaIndex=0&
partIndex=0&
protocol=hls&
offset=0&
fastSeek=1&
directPlay=0&
directStream=1&
videoQuality=75&
videoResolution=1280x720&
maxVideoBitrate=3000&
subtitleSize=100&
audioBoost=100&
session=l2ly19h92tg4aemi&
X-Plex-Token=Fdxv1u7Rk97GspiQwqPy&
X-Plex-Client-Identifier=l2ly19h92tg4aemi&
X-Plex-Username=newloran2&
X-Plex-Product=Web%20Client&
X-Plex-Device=Mac&
X-Plex-Platform=Chrome&
X-Plex-Platform-Version=33&
X-Plex-Version=1.2.25&
X-Plex-Device-Name=Plex%2FWeb%20(Chrome)
 */
class TranscodeManager
{
    private $baseUrl;
    private $mediaIndex=0;
    private $partIndex=0;
    private $protocol="hls";
    private $offset=0;
    private $fastSeek=1;
    private $directPlay=1;
    private $directStream=1;
    private $videoQuality=75;
    private $videoResolution="1280x720";
    private $maxVideoBitrate=40000;
    private $subtitleSize=75;
    private $audioBoost=100;
    private $session;
    private $XPlexToken;
    private $XPlexClientIdentifier;
    private $XPlexUsername;
    private $XPlexProduct="emplexer";
    private $XPlexDevice="Dune";
    private $XPlexPlatform="Dune";
    private $XPlexPlatformVersion;
    private $XPlexVersion;
    private $XPlexDeviceName="emplexer (Dune)";
    public function __construct($url)
    {
    }
}




