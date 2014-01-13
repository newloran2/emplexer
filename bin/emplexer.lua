basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
print (basedir)
if basedir == "emplexer.lua" then
  basedir = ""
end
-- package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
-- package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'

package.path = basedir..'dune/?.lua;'..basedir..'dune/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'dune/?.so;'.. basedir..'dune/lua/?.so;' ..basedir.. 'emplexer/?.so'

print(package.path)
print(package.cpath)


local utils    = require 'lem.utils'
local io       = require 'lem.io'
local hathaway = require 'lem.hathaway'
local register = require "register"
local lfs   = require 'lem.lfs'
local json = require ("dkjson")
local dune = require ('dune')
local client   = require 'lem.http.client'



hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API


GET('/', function(req, res)
	print(req.client:getpeer())
	res:add("erik")

end)

GETM('^/startServer/([^/]+)$', function(req, res, name)
  print(req.client:getpeer())
  res:add('startServer')
  table_print(req)
  register:startRegister(name)
end)

GETM('^/startNotifier/(.+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)$', function (req, res, ip, port, key, percentToDone,viewOffset)
  res:add(key .. ": " .. percentToDone)
  -- viewOffset =1
  print("viewOffset", viewOffset)
  dune:goToPosition(10)
  baseUrl =  "http://"..ip..":".. port .. "/:/progress?key="..key .. "&identifier=com.plexapp.plugins.library"
  moved = false
  dune:startPlayBackMonitor(1,
    {
      playing=function(data)
        --reduce the monitor frequence to 5 seconds
        dune:setPlayBackMonitorFrequence(5)
        -- print("playing callback", data.playback_url)
        duration = tonumber(data.playback_duration)
        position = tonumber(data.playback_position)
        toDone = tonumber(percentToDone)
        local c = client.new()
        url = nil
        if (100 - (position/duration *100) <= toDone) then
          url = baseUrl .. "&state=stopped&time=" .. (duration*1000)
          dune:stopPLaybackMonitor()
        else
          url =  baseUrl .. "&state=playing&time=".. (position*1000)
        end
        print("chamando url " .. url)
        c:get(url)
      end,
      buffering=function(data)
        print("buffering callback ")
        if (viewOffset > 0 and moved) then
          dune:goToPosition(viewOffset/1000)
          moved=true
        end
      end,
      standby = function ( data )
        print("buffering callback ")
      end,
      stop=function( data )
        print("stop callback ")
        dune:stopPLaybackMonitor()
      end,
      navigator=function( data )
        dune:stopPLaybackMonitor()
      end
    }
  )
end)

GET('/stopServer', function ( req, res )
	print(req.client:getpeer())
	res:add('stopServer')
	register:stopRegister()
end)

GET('/findServers' , function ( req, res )
	print('findServers');
	res:add (json.encode(register:getPlexServers()))
end)

POST('/jsonrpc', function(req, res)
  table_print(req:body())
end)


if arg[1] == 'socket' then
	local sock = assert(io.unix.listen('socket', 666))
	Hathaway(sock)
else
	Hathaway('*', arg[1] or '3000')
end
utils.exit(0)