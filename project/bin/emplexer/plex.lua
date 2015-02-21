-- package.path = "/D/lua.linux.mips/share/lua/5.2/?.lua;/D/lua.linux.mips/share/lua/5.2/?/?.lua"
-- package.cpath = "/D/lua.linux.mips/lib/lua/5.2/?/core.so"

local socket = require("socket")
local utils = require 'lem.utils'
local client   = require 'lem.http.client'

local format = string.format
local inspect =  require "inspect"
local function sleep(n)
   utils.newsleeper():sleep(n)
end

local client_data = "Content-Type: plex/media-player\r\nResource-Identifier: 97512d2c-e5c8-4cd8-85ef-deef2c092b35\r\nDevice-Class: HTPC\r\nName: %s\r\nPort: 3005\r\nProduct: Plex Home Theater\r\nProtocol: plex\r\nProtocol-Version: 1\r\nProtocol-Capabilities: navigation,playback,timeline\r\nVersion: 1"
local hello_broadcast_string = string.format("HELLO * HTTP/1.0\r\n%s", client_data)
local bye_broadcast_string ="BYE * HTTP/1.0\r\n"
_M = {}

local plex ={}


setmetatable(plex, metatable)

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
	 
	 
	 local port =32413
	 local ip="255.255.255.255"
	 local response_buffer_len=4096
	 local sock = nil

	 sock = socket.udp()
	 sock:setoption('broadcast', true)
	 sock:settimeout(1)

	 while continue do
	    print ("chamando", collectgarbage("count"))
	    sock:sendto(string.format(hello_broadcast_string, serverName) ,ip, port)
	    data = plex:getPlexServers()
	    sleep(5)
	    continue = false
	 end
	 sock:sendto(bye_broadcast_string, ip, port)
	 sock:close()
	 sock = nil
	 plex:update(serverName)
	 print ("register terminado")
   end)
   utils.yield()
end

function plex:update(serverName)
   serverName =  serverName or "emplexer"
   utils.spawn(function()
	 local discover_ip = "0.0.0.0"
	 local discover_port =  32414
	 sock = socket.udp()
	 sock:setoption('broadcast', true)
	 sock:settimeout(1)
	 sock:setsockname(discover_ip, discover_port)
	 sock:sendto(string.format(hello_broadcast_string, serverName), "255.255.255.255", 32413)
	 while true do

	    --	    print(inspect(getmetatable(sock)))
	   -- sock:setpeername(discover_ip, discover_port)
	    local dgram,ip, port = sock:receivefrom()
	    print (" valor de " , dgram, ip, port)
	    if (ip and port ) then
	       a= string.format(client_data, serverName)
	       a= string.format("HTTP/1.0 200 OK\r\n%s",a)
	       print ("enviando a resposta para ", ip, port, a)
	       ret, err = sock:sendto(a, ip, port)
	       print ("retorno =", ret, err)
	    end
	    sleep(3)
	 end
   end)
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



function plex:ok(res)
   plex:headers(res)
   plex:xmlHeader(res)
   res:add('<Response code="200" status="OK" />')
end

function plex:resources(res)
   plex:headers(res)
   
end



function plex:xmlHeader(res)
   res:add('<?xml version="1.0" encoding="utf-8" ?>')
end

function plex:headers(res) 
   res.headers["Content-type"]= "application/x-www-form-urlencoded"
   res.headers["Access-Control-Allow-Origin"]= "*"
   res.headers["X-Plex-Version"]= 1
   res.headers["X-Plex-Client-Identifier"]= "emplexer"
   res.headers["X-Plex-Provides"]= "player"
   res.headers["X-Plex-Product"]= "emplexer"
   res.headers["X-Plex-Device-Name"]= "emplexer"
   res.headers["X-Plex-Platform"]= "DuneOs"
   res.headers["X-Plex-Model"]= "emplexer"
   res.headers["X-Plex-Device"]= "PC"

   
end


_M = plex

return _M
