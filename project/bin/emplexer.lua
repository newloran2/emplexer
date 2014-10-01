

-- vim: foldmethod=indent
basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
print (basedir)
if basedir == "emplexer.lua" then
  basedir = ""
end
package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'

-- package.path = basedir..'dune/?.lua;'..basedir..'dune/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
-- package.cpath = basedir .. 'dune/?.so;'.. basedir..'dune/lua/?.so;' ..basedir.. 'emplexer/?.so'

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
local inspect = require ('inspect')
local urlParser =  require ('urlParser')
local client   = require 'lem.http.client'
local lfs   = require 'lem.lfs'
local http =  require 'socket.http'
  -- local gd = require("gd")

local function sleep(n)
  utils.newsleeper():sleep(n)
end

local format = string.format
hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API


function log( text )
  utils.spawn(function()
    local file = io.open("/tmp/emplexer2.log",  "a")
    print (file)
    file:write(text.."\n");
    file:close()
  end)
end

GET('/a.mkv', function (req, res)
        local t, range = nil
    local c = client.new()
    req.headers['Range'] = req.headers['range'] or nil
    local r = c:get("http://192.168.2.8:32400/library/parts/41097/file.mkv", req.headers)
    -- print (inspect(req))
    -- print (inspect(r))
    if req.headers['range'] then
        t, range = req.headers['range']:match('(.+)=(.+)')
        range = "&range="..range
        res.status=302
        res.headers['Location'] = "http://192.168.2.44/cgi-bin/plugins/emplexer2/videoGetter.mkv?url=http://192.168.2.8:32400/library/parts/41097/file.mkv" .. range
    else 
        res.status = 200
    end
    -- res.headers["Content-Type"] = "video/x-matroska"
    res.headers["Accept-Ranges"]   = r.headers['accept-ranges']
    res.headers['Content-Length']  = r.headers['content-length']
    res.headers['content-type']    = r.headers['content-type']
    res.headers['Connection']      = 'close'
    -- res:add(r:bodyLine())
    -- res:add(r:bodyLine())
    local firstChunksize = tonumber(r.headers['content-length'])
    firstChunksize = firstChunksize > 10240 and 10240 or firstChunksize
    local line,total =nil, 0
    print ('firstChunksize', firstChunksize)
    repeat
        line , total = r:read(firstChunksize,total)
        -- print (total)
        req.client:write(line or "")
    until line == nil
    c:close()
    -- res.status=302
    -- res.headers['Location'] = "http://192.168.2.44/cgi-bin/plugins/emplexer2/videoGetter.mkv?url=http://192.168.2.8:32400/library/parts/41097/file.mkv"
    print(inspect(req)) 
end)
GET('/download', function (req, res)
    local query = urlParser.parse(req.uri).query
    local url = query.url
    print (inspect (req.headers))
    res.headers['Connection'] = 'Close'
    local c = client.new()
    local r = c:get(url, req.headers)

    -- if (r.headers.location ~= nil) then
    --     r.conn:close()
    --     print ("fechei a conexão")
    --     c = client.new()
    --     print ("nava url encontrada", r.headers.location)
    --     r = c:get(r.headers.location, req.headers)
    -- end
    -- print (inspect (r))
    r:body( function (len, total, body, err) 
        -- print ("baixei", len , total)
        res:add(body)
    end )
    
end)

GET('/downloadAndCache', function (req, res)
    local query = urlPrser.parse(req.uri).query
    local url = query.url
    
    local dirPath = url:gsub("//","/")
    print (dirPath)
    dirPath =  '/tmp/bkp/'.. dirPath 
    local attr = lfs.attributes(dirPath)
    if (not attr) then
        local command = 'mkdir -p ' .. dirPath
        print (command)
        io.popen(command)
        local c = client.new()
        local r = c:get(url)
        local filePath = dirPath .. "/image.jpg"
        file = io.open(filePath, "wb")
        r:body(function (len,total, body, err)
             res:add(body)
             file:write(body) 
             print (total, len)
        end)
        -- file:close()
    else 
        
        local filePath = dirPath .. "/image.jpg"
        local file = io.open(filePath, "r")
        local line = file:read(2048)
        while line  do 
            res:add(line)
            line  =nil
            line = file:read(2048)
        end
        file:close()
        file = nil
    end
end)



GETM('^/startServer/([^/]+)$', function(req, res, name)
  res:add('startServer')
  plex:startRegister(name)
end)          




GET('/startNotifier', function (req, res)
  local query = urlParser.parse(req.uri).query
  viewOffset = tonumber(query.viewOffset)
  if (viewOffset > 0) then
    moved =  false
  else 
    moved =  true
  end
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
    log (inspect(a))
    b= urlParser.parse(a.query.path)
    log (inspect(b))

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


--[[
    Os apps do plex chamam esse entry point quando mandam uma url para playback
]]

GET("/player/playback/playMedia", function ( req,res )
     a=  urlParser.parse(req.uri)
     local port = a.query.port
    log(req.client:getpeer())
    ip = a.query.address
    if (ip == '127.0.0.1') then
        ip =req.client:getpeer()
    end
    url = a.query.protocol.."://"..ip..":".. a.query.port..a.query.key
    log ("url = ".. url)
    local c = client.new()
    headers =  {}
    local  req, err  = c:get(url, headers )
    -- table_print(req)
    body, err = req:body()

    xml =  xmlParser:new(handler:simpleTreeHandler())
    xml:parse(body)
    print (inspect (xml._handler.root.MediaContainer.Video._attr.ratingKey))
    fileUrl  = format("http://%s:%s%s", ip, a.query.port, xml._handler.root.MediaContainer.Video.Media.Part._attr.key)
    viewOffset = xml._handler.root.MediaContainer.Video._attr.viewOffset or '0'
    url = format("http://127.0.0.1:3000/startNotifier?ip=%s&port=%s&key=%s&percentToDone=%s&viewOffset=%s", a.query.address, a.query.port, xml._handler.root.MediaContainer.Video._attr.ratingkey,10,0)
    req =  c:get(url)
    dune:startPlayback(fileUrl,viewOffset)
    local time = 1
    utils.spawn(function()
        while true do
            plex:timeline(ip, port,time,xml._handler.root.MediaContainer.Video.Media._attr.duration, xml._handler.root.MediaContainer.Video._attr.ratingKey )
            sleep(1)
            time = time +1
        end

    end)

end)

GET('/player/playback/play', function(req, res)
    dune:play()
end)
GET('/player/playback/pause', function(req, res)
    dune:pause() 
    res:add([[<?xml version="1.0" encoding="utf-8" ?>
<Response code="200" status="OK" />]])
end)
    
GET('/player/playback/stop', function(req, res)
    dune:stop()    
    res:add([[<?xml version="1.0" encoding="utf-8" ?>
<Response code="200" status="OK" />]])

end)


GET('/player/timeline/subscribe', function(req, res)
    print(inspect(req))
    res:add([[<?xml version="1.0" encoding="utf-8" ?>
<Response code="200" status="OK" />]])
    -- plex:timeline('192.168.2.8', '32400', '20255', '10000', '1500083' )
end)


GET('/resources', function(req,res)
    print(inspect(res))
    res.headers['X-Plex-Client-Identifier'] =  'emplexer'
    res.headers['Content-Type'] = 'Content-Type'
    res:add([[<?xml version="1.0" encoding="utf-8" ?>
<MediaContainer>
  <Player title="emplexer" protocol="plex" protocolVersion="1" protocolCapabilities="navigation,playback" machineIdentifier="emplexer" product="emplexer" platform="dune" platformVersion="1" deviceClass="htpc" />
</MediaContainer>]])
    
end)


GET('/generate', function(req,res)
  print ('teste do erik', inspect(gd));


local im = gd.createPalette(90, 90)

local white = im:colorAllocate(255, 255, 255)
local blue = im:colorAllocate(0, 0, 255)
local red = im:colorAllocate(255, 0, 0)

im:colorTransparent(white)
im:filledRectangle(10, 10, 50, 50, blue)
im:filledRectangle(40, 40, 80, 80, red)

im:gif("out.gif")
-- os.execute("xdg-open out.gif")

end)




plex:startRegister('emplexer')
if arg[1] == 'socket' then
    local sock = assert(io.unix.listen('socket', 666))
    Hathaway(sock)
else
    Hathaway('*', arg[1] or '3005')
end
utils.exit(0)
