-- package.path = "/D/lua.linux.mips/share/lua/5.2/?.lua;/D/lua.linux.mips/share/lua/5.2/?/?.lua"
-- package.cpath = "/D/lua.linux.mips/lib/lua/5.2/?/core.so"

local socket = require("socket")
local utils = require 'lem.utils'
local client   = require 'lem.http.client'

local format = string.format
local function sleep(n)
  utils.newsleeper():sleep(n)
end

_M = {}

local plex ={}

function plex:getPlexServers( )
	broadcast_string ="M-SEARCH * HTTP/1.1\r\n\r\n"
	port =32414
	ip="255.255.255.255"
	response_buffer_len=4096
	sock = socket.udp()
	sock:setoption('broadcast', true)
	sock:settimeout(1)
	sock:sendto(broadcast_string,ip, port)
	ret = {}
	while true do
		local a, b = sock:receivefrom(response_buffer_len)
		if (not a) then
			break;
		else
			v= a:gsub("\r\n\r\n", "\r\nHost: " .. b)
			p = {}
            for w in v:gmatch("[^\r\n]+") do
                local key, value = w:match("(.+): (.+)")
                if (key and value) then
                    p[key] = value
                end
			end
			table.insert(ret, p)
		end
	end

	return ret
end



local continue = false
function plex:startRegister(serverName)
	serverName =  serverName or "emplexer"
	if (continue) then
		return
	end
	continue = true
	utils.spawn(function()

-- 		local hello_broadcast_string =
-- "HELLO * HTTP/1.0\r\nContent-Type: plex/media-player\r\nResource-Identifier: blablablablabla2\r\nDevice-Class: HTPC\r\nName: "..serverName.."\r\nPort: 3000\r\nProduct: emplexer\r\nProtocol: plex\r\nVersion: 1\r\nProtocol-Version: 1\r\nProtocol-Capabilities: navigation,playback,timeline"
		local hello_broadcast_string ="HELLO * HTTP/1.0\r\nContent-Type: plex/media-player\r\nResource-Identifier: emplexer\r\nDevice-Class: HTPC\r\nName: "..serverName.."\r\nPort: 3005\r\nProduct: emplexer\r\nProtocol: plex\r\nVersion: 1\r\nProtocol-Version: 1\r\nProtocol-Capabilities: navigation,playback,timeline"
		local bye_broadcast_string ="BYE * HTTP/1.0\r\n"
		local port =32413
		local ip="255.255.255.255"
		local response_buffer_len=4096
		local sock = nil

		sock = socket.udp()
		sock:setoption('broadcast', true)
		sock:settimeout(1)

		while continue do
			-- print ("chamando", collectgarbage("count"))
			sock:sendto(hello_broadcast_string,ip, port)
			sleep(5)
		end
		sock:sendto(bye_broadcast_string, ip, port)
		sock:close()
		sock = nil
		print ("register terminado")
	end)
	utils.yield()
end


function plex:stopRegister()
	utils.spawn (function ()
		continue = false;
		print("tentando parar o register")
	end)
end


function plex:notify( ip, port, time, duration, key, state)
	state = state or "playing"
	local url = format("http://%s:%s/:/timeline?time=%s&duration=%s&state=%s&key=/library/metadata/%s&ratingKey=%s", ip, port, time, duration, state, key, key)
	utils.spawn(function()
		-- print ("parâmetros:", ip, port, time, duration, key, state)
		-- print ("vou chamar a url teste ", url)
		local c = client.new()
		header ={}
		header["X-Plex-Client-Identifier"] = "emplexer"
		c:get(url, header)
		c:close()
		c= nil
	end)
end

function plex:timeline(ip, port, key, time, duration, state)
	state =  state  or "playing"
	local xml = format([[
	<?xml version="1.0" encoding="utf-8" ?>
<MediaContainer commandID="18" location="fullScreenVideo">
  <Timeline location="navigation" seekRange="0-0" state="stopped" time="0" type="music" />
  <Timeline location="navigation" seekRange="0-0" state="stopped" time="0" type="photo" />
  <Timeline address="%s" containerKey="/library/metadata/%s" controllable="playPause,stop,volume,stepBack,stepForward,seekTo,subtitleStream,audioStream" duration="%s" guid="com.plexapp.agents.thetvd" key="/library/metadata/%s"
  location="fullScreenVideo" machineIdentifier="836bce27a7cc2ae108939b16de14ed84aae93b1d" mute="0" port="%s" protocol="http" ratingKey="%s" repeat="0" seekRange="0-%s" shuffle="0" state="%s" subtitleStreamID="104112" time="%s"
  type="video" volume="100" />
</MediaContainer>
	]], ip, key, duration, duration, port, key, duration, state, time )

	url = format("http://%s:%s/:/timeline", ip, port )
    print (url)

	local c = client.new()
	header ={}
	header["X-Plex-Client-Identifier"] = "emplexer"
	-- c:get(url, header)
	c:post(url,xml, header)
	c:close()
    print (xml)
	c = nil

end

_M = plex

return _M
