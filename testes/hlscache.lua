-- basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
-- print (basedir)
-- if basedir == "hlscache.lua" then
--   basedir = ""
-- end
basedir="../project/bin/"
package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'

-- pac
local utils    = require 'lem.utils'
local io       = require 'lem.io'
local hathaway = require 'lem.hathaway'
local client   = require 'lem.http.client'
local lfs      = require 'lem.lfs'
local p = require ('inspect')

hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API
local data ={}
local c = client.new()

function urlencode(str)
    if (str) then
        str = string.gsub (str, "\n", "\r\n")
        str = string.gsub (str, "([^%w ])",
        function (c) return string.format ("%%%02X", string.byte(c)) end)
        str = string.gsub (str, " ", "+")
    end
    return str
end 

GET("/" , function (req, res)
    print (p(res))
    res.file = "/tmp/buffer/20"
end)

GETM('^/start/(.*)$', function (req, res, url)
    data = nil
    data = {}
    local c = client.new()
    local r = c:get(url)
    local d = r:body()
    local iter = d:gmatch("http:%S+")
    local count = 1
    for i in iter do
        local url = "http://127.0.0.1:3005/get/" .. tostring(count)
        data[count] = i
        count=count +1
    end
    local count =1
    d = d:gsub("(http://%S+)", function (c)
        local ret = nil

        local ret =  count > 1 and  "http://127.0.0.1:3005/get/".. tostring(count) or "http://127.0.0.1:3005/get/".. tostring(count) .. "\""
        count = count +1
        return ret
    end)
    res:add(d)
end)

GETM('^/get/(.*)$', function (req, res, url)
    local r = c:get(data[tonumber(url)])
    local count = 1
    local len =  r.headers['content-length']
    res.headers['Content-Length'] = len
    res.headers['Content-Type'] = r.headers['content-type']
    len = tonumber(len) 
    local size, counter, packetSize = 0,1, 8192
    local path = "/tmp/buffer/".. url
    local attributes = lfs.attributes(path)
    print (collectgarbage("count"))
    if not attributes or (attributes.size ~= len) then
        print ("tamanos diferem ou o arquivo não existe baixando da internet", (attributes ~=nil and attributes.size or "não existe"), len)
        local file = io.open(path, "w")
        res.chunk = function()
            line, size = r:nextChunk(packetSize, size)  
            collectgarbage()
            if (line) then
                file:write(line)
            
                if (size >= len) then
                    file:close()
                end
            end
            return line
        end
    else
        res.file =path
        collectgarbage()
    end
    -- local d = r:body()
    -- res:add(d)

end)

if arg[1] == 'socket' then
    local sock = assert(io.unix.listen('socket', 666))
    Hathaway(sock)
else
    Hathaway('*', arg[1] or '3005')
end
utils.exit(0)
