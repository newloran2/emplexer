basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
print (basedir)
if basedir == "teste.lua" then
  basedir = ""
end
package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'

-- package.path = basedir..'dune/?.lua;'..basedir..'dune/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
-- package.cpath = basedir .. 'dune/?.so;'.. basedir..'dune/lua/?.so;' ..basedir.. 'emplexer/?.so'

print(package.path)
print(package.cpath)

local dump   = require 'dump'
local client   = require 'lem.http.client'

local file = io.open("/Library/WebServer/Documents/teste/teste.m3u8")
local a=file:read("*a")
file:close()

-- print (a)s


local t ={}
count=1
for i in string.gmatch(a, "(.-)\r?\n") do
    if (i:match("http.*")) then
        local data={}
        name = i:match("video.*ts") or i:match("http.*key")
        data.localUrl = "http://127.0.0.1/"..name
        count =count+1
        data.remoteUrl = i
        data.localFile = "sdjsd"
        t[data.localUrl] = data
    end
end

-- print(dump.tostring(t))

local b = t['http://127.0.0.1/video-9.ts']

-- print(dump.tostring(b))


local c = assert(client.new())
print(dump.tostring(c))

local res =  c:get(b.remoteUrl)
count =1
file = io.open('/tmp/teste.file',  "w")
local d, err = res:body(1024*20, function ( data )
    print ('baixou ', #data, count )
    file:write(data)
end)
file:close()
-- print (data, err)


