local client = require 'lem.http.client'
local io     = require 'lem.io'
local lxp    = require "lem.lxp"
local _M = {}

local format = string.format

local XmlParser = {}
XmlParser.__index = XmlParser
_M.XmlParser = XmlParser


local HttpReader ={}
HttpReader.__index = HttpReader

function HttpReader:readChunks(bytesToRead)
    local bytes = bytesToRead  or "*l"
    local line , err = self.conn:read(bytes)
    if line then
        return line
    end    
    return nil
end

function _M.new()
    return setmetatable({},XmlParser)
end

function XmlParser:parseHttp(url, callBacks, headers )
    local c = client.new()
    local res = c:get(url, headers)

    setmetatable(res, HttpReader)
    count =1
    p = lxp.new(callBacks)
    p:setbase(format("%s://%s:%s", c.proto, c.domain, c.port))
    while true do 
        line = res:readChunks(2048)
        if line then
            p:parse(line)
        else
            c:close()
            p:parse()
            p:close()        
            return 1
        end
        count =count +1
    end
end
return _M

