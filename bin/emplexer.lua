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
local plex = require "plex"
local lfs   = require 'lem.lfs'
local json = require ("dkjson")
local xmlParser =  require('xml')
local handler = require ('handler')
local dune = require ('dune')
local dump = require ('dump')
local urlParser =  require ('urlParser')
local client   = require 'lem.http.client'
local lfs   = require 'lem.lfs'



local format = string.format
hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API


function log( text )
  utils.spawn(function()
    file = io.open("/D/dune_plugin_logs/emplexer2.log",  "a")
    file:write(text.."\n");
    file:close()
  end)
end

local currentPlaylist = nil
function table_print (tt, indent, done)
  done = done or {}
  indent = indent or 0
  if type(tt) == "table" then
    for key, value in pairs (tt) do
      io.write(string.rep (" ", indent)) -- indent it
      if type (value) == "table" and not done [value] then
        done [value] = true
        io.write(string.format("[%s] => table\n", tostring (key)));
        io.write(string.rep (" ", indent+4)) -- indent it
        io.write("(\n");
        table_print (value, indent + 7, done)
        io.write(string.rep (" ", indent+4)) -- indent it
        io.write(")\n");
      else
        io.write(string.format("[%s] => %s\n",
            tostring (key), tostring(value)))
      end
    end
  else
    io.write(tt .. "\n")
  end
end



GET('/', function(req, res)
    log(req.client:getpeer())
    res:add("erik")

  end)

GETM('^/startServer/([^/]+)$', function(req, res, name)
  res:add('startServer')
  plex:startRegister(name)
  end)

-- GETM('^/startNotifier/(.+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)$', function (req, res, ip, port, key, percentToDone,viewOffset)
GET('/startNotifier', function (req, res)
    -- , ip, port, key, percentToDone,viewOffset
  local query = urlParser.parse(req.uri).query
  table_print(query)
  viewOffset = tonumber(query.viewOffset)
  moved = false
  dune:startPlayBackMonitor(1,
    {
      playing=function(data)
          --reduce the monitor frequence to 5 seconds
          dune:setPlayBackMonitorFrequence(5)
          log("playing callback", data.playback_url)
          -- the dune works with seconds and plex with miliseconds i need to convert
          duration = tonumber(data.playback_duration) *1000
          position = tonumber(data.playback_position) *1000
          toDone = tonumber(query.percentToDone)
          if (100 - (position/duration *100) <= toDone) then
            plex:notify( query.ip, query.port, duration, duration, query.key, "stop")
            dune:stopPLaybackMonitor()
          else
            plex:notify( query.ip, query.port, position, duration, query.key)
          end
          end,
          buffering=function(data)
          log("buffering callback ")
          if (viewOffset > 0 and moved) then
            dune:goToPosition(viewOffset/1000)
            moved=true
          end
          end,
          standby = function ( data )
            log("buffering callback ")
            dune:stopPLaybackMonitor()
          end,
          stop=function( data )
            log("stop callback ")
            dune:stopPLaybackMonitor()
          end,
          navigator=function( data )
            dune:stopPLaybackMonitor()
          end
    }
  )
end)

GET('/stopServer', function ( req, res )
    log(req.client:getpeer())
    res:add('stopServer')
    plex:stopRegister()
  end)

GET('/findServers' , function ( req, res )
    log('findServers');
    res:add (json.encode(plex:getPlexServers()))
  end)

GET('/player/application/playMedia', function(req,res)
    a=  urlParser.parse(req.uri)
    log (dump.tostring(a))
    b= urlParser.parse(a.query.path)
    log (dump.tostring(b))

    local c = client.new()
    log(a.query.path)
    headers =  {}
    -- headers['Accept'] = 'application/json'
    local  req = c:get(a.query.path, headers )

    j=json.decode(req:body());
    url = "http://127.0.0.1:3000/startNotifier/127.0.0.1/32400/"..j.key.."/10/0"
    log (t"tentando chamar a url", url)
    req =  c:get(url)
    log (req:body())
end)

GET("/player/playback/playMedia", function ( req,res )
     a=  urlParser.parse(req.uri)
    log(req.client:getpeer())
    ip = a.query.address
    if (ip == '127.0.0.1') then
        ip =req.client:getpeer()
    end
    url = a.query.protocol.."://"..ip..":".. a.query.port..a.query.key
    log ("url = ", url)
    local c = client.new()
    headers =  {}
    -- headers['Accept'] = 'application/json'
    local  req, err  = c:get(url, headers )
    table_print(req)
    body, err = req:body()

    -- print ("body, err", body, err)
    xml =  xmlParser:new(handler:simpleTreeHandler())
    -- j=json.decode(body);
    xml:parse(body)
    -- table_print(j)
    -- print (j._children[1].key)
    fileUrl  = format("http://%s:%s%s", ip, a.query.port, xml._handler.root.MediaContainer.Video.Media.Part._attr.key)
    viewOffset = xml._handler.root.MediaContainer.Video._attr.viewOffset or '0'
    -- print ("fileurl=", fileUrl)
    url = format("http://127.0.0.1:3000/startNotifier?ip=%s&port=%s&key=%s&percentToDone=%s&viewOffset=%s", a.query.address, a.query.port, xml._handler.root.MediaContainer.Video._attr.ratingkey,10,0)
    req =  c:get(url)
    dune:startPlayback(fileUrl,viewOffset)
end)

GET('/player/playback/play', function(req, res)
    dune:play()
end)
GET('/player/playback/pause', function(req, res)
    dune:pause()
end)
GET('/player/playback/stop', function(req, res)
    dune:stop()
end)


GET('/player/timeline/subscribe', function(req, res)
    plex:timeline('192.168.2.8', '32400', '20255', '10000', '1500083' )
    res:add([[<?xml version="1.0" encoding="utf-8" ?>
<Response code="200" status="OK" />]])
end)


if arg[1] == 'socket' then
    local sock = assert(io.unix.listen('socket', 666))
    Hathaway(sock)
else
    Hathaway('*', arg[1] or '3000')
end
utils.exit(0)