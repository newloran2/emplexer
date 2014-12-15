--
-- This file is part of LEM, a Lua Event Machine.
-- Copyright 2013 Emil Renner Berthing
--
-- LEM is free software: you can redistribute it and/or modify it
-- under the terms of the GNU Lesser General Public License as
-- published by the Free Software Foundation, either version 3 of
-- the License, or (at your option) any later version.
--
-- LEM is distributed in the hope that it will be useful, but
-- WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Lesser General Public License for more details.
--
-- You should have received a copy of the GNU Lesser General Public
-- License along with LEM.  If not, see <http://www.gnu.org/licenses/>.
--

local setmetatable = setmetatable
local tonumber = tonumber
local concat = table.concat

local io    = require 'lem.io'
local ssl = require 'lem.ssl'
require 'lem.http'

local inspect = require 'inspect'
-- Print anything - including nested tables
local M = {}

local Response = {}
Response.__index = Response
M.Response = Response

function Response:body_chunked()
	if self._body then return self._body end

	local conn = self.conn
	local rope, i = {}, 0
	local line, err
	while true do
		line, err = conn:read('*l')
		print ("line " , line)
		if not line then return nil, err end

		local len = tonumber(line, 16)
		if not len then return nil, 'expectation failed' end
		if len == 0 then break end

		local data, err = conn:read(len)
		if not data then return nil, err end

		i = i + 1
		rope[i] = data

		line, err = conn:read('*l')
		if not line then return nil, err end
	end

	line, err = conn:read('*l')
	if not line then return nil, err end

	rope = concat(rope)
	self._body = rope
	return rope
end

local function bodyContent(self)
	if self._body then return self._body end
	if self.headers['transfer-encoding'] == 'chunked' then
		return self:body_chunked()
	end

	local len, body, err = self.headers['content-length']
	if len then
		len = tonumber(len)
		if not len then return nil, 'invalid content length' end
		body, err = self.conn:read(len)
	else
		if self.headers['connection'] == 'close' then
			body, err = self.client:read('*a')
		else
			return nil, 'no content length specified'
		end
	end
	if not body then return nil, err end

	self._body = body
	return body
end
function Response:body(callback)
	if (callback == nil) then return bodyContent(self) end
	if self._body then return self._body end
	if self.headers['transfer-encoding'] == 'chunked' then
		return self:body_chunked()
	end

	local len, body, err = self.headers['content-length']
	if len then
		len = tonumber(len)
		if not len then return nil, 'invalid content length' end
		local totalRead = 0

		while totalRead < len do
			local l = nil
			if totalRead +2048 > len then
				l = len - totalRead
		  else 
				l = 2048
		  end
			body, err = self.conn:read(l)
	 
			if (err == nil) then
				totalRead = totalRead + #body
				callback(len,totalRead,body,err)
			else
				callback(len, totalRead,nil, err)
				return nil
			end
		end
	else

		if self.headers['connection'] == 'close' then
			local totalRead = 0

			while totalRead < len do
				body, err = self.client:read(4048)

				if (err == nil) then
					callback(len,total,body)
					totalRead = totalRead + #body
				else
					print ("erro =", err)
				end
			end
				return nil
		else
			return nil, 'no content length specified'
		end
	end
	if not body then return nil, err end

	self._body = body
	return body
end
function Response:bodyLine()
		local line = self.conn:read(100)
		print ('tamanho de line', #line or 0)
end


function Response:nextChunk(packetSize, size)
   len = tonumber(self.headers['content-length'])
   if size >=len then return nil end
   packetSize = packetSize + size  > len and len - size or packetSize
   size = size+packetSize
   return self.conn:read(packetSize), size
   
end


local Client = {}
Client.__index = Client
M.Client = Client


function M.new()
	return setmetatable({
		proto = false,
		domain = false,
		conn = false,
	   ssl = ssl.newcontext()} ,Client)
end

-- local req_get = "GET %s HTTP/1.1\r\nHost: %s\r\nConnection: keep-alive\r\n"
local req_get = "GET %s HTTP/1.1\r\nHost: %s\r\n"
local req_post = "POST %s HTTP/1.1\r\nHost: %s\r\n"

local function close(self)
	local c = self.conn
	if c then
		self.conn = false
		return c:close()
	end
	return true
end
Client.close = close

local function readHeader(connection)
		ret = {
			conn = connection,
			headers ={}
		}
		print ("connection:", connection)
		line = connection:read("*l")
		print ("line = ", line)
		local type, version, status, text = line:match("([A-Z]+)/([0-9.]+) ([0-9]+)(.*)")
		ret.status = status
		ret.text = text
		ret.version = version
		
		line = connection:read("*l")
		while line ~="\r\n" do
			local key,value =line:match("([a-zA-Z-]+): (.*)")
			-- print ("line =", line)
			-- print ("key value", key, value)
			ret.headers[key] = value
		  line = connection:read("*l")
		end
		
	return ret	

end

local function fail(self, err)
	self.proto = false
	close(self)
	return nil, err
end


local function doTheRequest(self,url, type, data, headers)
	-- print ("chamou o doTheRequest", url)
	-- print ("valor de self dentro de doTheRequest ", inspect(self))
		-- print (inspect(headers))
	local proto, domain, port , uri = url:match('([a-zA-Z0-9]+)://([a-zA-Z0-9.]+):([0-9]+)(/.*)')
	if not port then
		proto, domain, uri = url:match('([a-zA-Z0-9]+)://([a-zA-Z0-9.]+)(/.*)')
	end

	local h = headers or {}
	if not proto then
		error('Invalid URL', 2)
	end

	local c, err
	local req = nil
	if (type == "get") then
		req = req_get:format(uri, domain)
	else
		req = req_post:format(uri, domain)
	end

	local res

	if headers then
		for key,value in pairs(headers) do
			req = req .. key ..": " .. value .. "\r\n"
		end
	end
	req = req .. "Connection: close\r\n\r\n"
	if (type == "post") then
		req =  req .. data
	end


	-- print ("headers:", inspect(headers))

		
	if proto == self.proto and domain == self.domain then
		c = self.conn
		if c:write(req) then
			res = c:read('HTTPResponse')
		end
	end

	if not res then
		c = self.conn
		if c then
			c:close()
		end

		if proto == 'http' then
			c, err = io.tcp.connect(domain, port or '80')
			port  = port and port or '80'
		elseif proto == 'https' then
			local ssl = self.ssl
			if not ssl then
				error('No ssl context defined', 2)
			end
			c, err = ssl:connect(domain..':'.. ( port or 443))
					-- c, err = ssl:connect(domain, port or '443')
			port  = port and port or '443'
		else
			error('Unknown protocol', 2)
		end
		if not c then return fail(self, err) end

		local ok
		ok, err = c:write(req)
		if not ok then return fail(self, err) end
		if (proto=="https") then
				res = readHeader(c)
		else
			res, err = c:read('HTTPResponse')
		end
		print (inspect (res))	
		if not res then return fail(self, err) end
	end
	res.conn = c
	setmetatable(res, Response)

	self.proto = proto
	self.domain = domain
	self.port = port
	self.conn = c
	return res
end


function Client:get(url, headers)
	-- print ("valor de self em Client:get", inspect(self))
	return doTheRequest(self, url, "get", nil, headers)
end

function Client:post(url, data, headers)
	headers['Content-Length'] = #data
	headers['Content-Type'] = 'application/x-www-form-urlencoded'
	return doTheRequest(self, url,"post", data, headers)
end

function Client:download(url, filename)
	local res, err = self:get(url)
	if not res then return res, err end

	local file
	file, err = io.open(filename, 'w')
	if not file then return file, err end

	local ok
	ok, err = file:write(res:body())
	if not ok then return ok, err end
	ok, err = file:close()
	if not ok then return ok, err end

	return true
end

return M

-- vim: set ts=2 sw=2 noet:
