-- basedir = gsub(arg[0], "(.*/)(.*)", "%1")
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
local format, byte, gsub =  string.format, string.byte, string.gsub

hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API
local data ={}
local c = client.new()
local current = 1
local donwloadAllFilesInProgress = false

function urlencode(str)
   if (str) then
      str = gsub (str, "\n", "\r\n")
      str = gsub (str, "([^%w ])",
		  function (c) return format ("%%%02X", byte(c)) end)
      str = gsub (str, " ", "+")
   end
   return str
end 

--a="http://127.0.0.1:3005/get/3000000"
--print (a:match("[0-9]+$"))

function downloadAllFiles()
   utils.spawn(function()
	 donwloadAllFilesInProgress = true
	 local headers ={
	    Connection = "keep-alive"
	 }

	 local localClient = client.new()
	 local localCurrent = 3
	 local i = localCurrent
	 local total = #data
	 local size, counter, packetSize = 0,1, 8192
	 while i < total  and donwloadAllFilesInProgress do
	    local url = data[i]
--	    print ("downloadAllFiles requisitando url", url)
	    local r = localClient:get(url, headers)
	    local len = r.headers['content-length']
	    len = tonumber(len)
	    local path = format("/tmp/buffer/%d", i)
	    local attributes =  lfs.attributes(path)
	    if not attributes or (attributes.size ~=len) then
--	       print("dowloadAllFiles arquivo ainda não baixado ou baixado incompleto vou baixar", i)
	       local file = io.open(path, "w")
	       local line, size = r:nextChunk(packetSize, size)
	       while line  and donwloadAllFilesInProgress do
		  file:write(line)
		  if size > len then
		     file:close()
		  else
		     line, size = r:nextChunk(packetSize, size)
		  end
		  if current > i then
		     current = i + 3
		     line = nil
		     file:close()
		     break
		  end
		  local formatedSize =  (size ==nil and 1 or size)
		  formatedSize = formatedSize 
		  io.write("data:", i," ", formatedSize, " \r")
	       end
	       
	    else
	       print ("donwloadAllFiles arquivo já baixado vou para o proximo", i)
	    end
	    i= i+1
	 end
	 
   end)

   
   
   
end

GET("/" , function (req, res)
       print (p(res))
       res.file = "/tmp/buffer/20"
end)

GETM('^/start/(.*)$', function (req, res, url)
	
	data = nil
	data = {}
	--    local c = client.new()
	local r = c:get(url, {Connection ="keep-alive"})
	res.headers = r.headers
	print("url requisitada", url,  p(req))
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
	if not donwloadAllFilesInProgress then
	   downloadAllFiles()
	end
	
	local headers = req.headers
	headers.Connection = "keep-alive"
	headers.connection = nil
	local currentNumber = url
	current = tonumber(currentNumber)
	local r = c:get(data[tonumber(url)], headers)
	local count = 1
	--	print("headers da reqeust",p(req),"\nheaders da response", p(r))
	local len =  r.headers['content-length']
	res.headers['Content-Length'] = len
	res.headers['Content-Type'] = "video/MP2T"
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
	   print("arquivo está cacheado vou utilizar-lo")
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
