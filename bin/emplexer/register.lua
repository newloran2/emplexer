-- package.path = "/D/lua.linux.mips/share/lua/5.2/?.lua;/D/lua.linux.mips/share/lua/5.2/?/?.lua"
-- package.cpath = "/D/lua.linux.mips/lib/lua/5.2/?/core.so"

local socket = require("socket")
local utils = require 'lem.utils'
local stringUtils = require("utils")


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
		-- local ip, port = sock:getpeername()
		if (not a) then
			break;
		else
			v= a:gsub("\r\n\r\n", "\r\nHost: " .. b)
			
			a=stringUtils.split(v, "\r\n")
			-- print (a[4])
			p = {}
			for i,v in ipairs(a) do
				local b =  stringUtils.split(v, ":")
				local key =b[1]
				local value = b[2]
				p[key] = value
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

		local hello_broadcast_string =
"HELLO * HTTP/1.0\r\nContent-Type: plex/media-player\r\nResource-Identifier: blablablablabla2\r\nDevice-Class: HTPC\r\nName: "..serverName.."\r\nPort: 3000\r\nProduct: emplexer\r\nProtocol: xbmcjson\r\nVersion: 2.0"
		local bye_broadcast_string ="BYE * HTTP/1.0\r\n"
		local port =32413
		local ip="255.255.255.255"
		local response_buffer_len=4096
		local sock = nil
		
		sock = socket.udp()	
		sock:setoption('broadcast', true)
		sock:settimeout(1)

		while continue do
			print ("chamando", collectgarbage("count"))
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

_M = plex

return _M
