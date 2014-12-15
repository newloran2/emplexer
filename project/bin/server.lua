basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
print (basedir)
if basedir == "server.lua" then
  basedir = ""
end
package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'
local io = require 'lem.io'
local utils = require "lem.utils"
local queue = require 'lem.io.queue'
require 'lem.http'
local httpClient = require 'lem.http.client'
local inspect = require "inspect"
local format = string.format
local urlParser = require "urlParser"
local yield = utils.yield
local packetSize = 8192

local socket = assert(io.tcp.listen('*', '5555'))

socket:autospawn(function(client)
      local req, err = client:read('HTTPRequest')
      local c = httpClient.new()
      local query = urlParser.parse(req.uri)
      print(inspect(query.query.url))
      if req.headers.range then
	 req.headers.Range = req.headers.range
      end
      local res  = c:get(query.query.url, req.headers)
      if (res) then
	 print("res", res.headers["content-range"])
	 client:write(format("HTTP/%s %s %s\n",res.version, res.status, res.text))
	 for k,v in pairs(res.headers) do
	    client:write(format("%s: %s\n", k,v))
	 end
	 client:write("\n")
	 
	 local size, counter = 0,1
	 local line, size = res:nextChunk(packetSize, size)  
	 while line do
	    client:write(line)
	    if not client:closed() then
	       line, size = res:nextChunk(packetSize, size)
		  collectgarbage()
		  yield()
	    else
	       line = nil
	    end
	 end
      else
	 client:write("HTTP/1.1 404 NOT Found")
	 client:write("\r\n\r\n")
      end
      client:close()
      c:close()
end)


