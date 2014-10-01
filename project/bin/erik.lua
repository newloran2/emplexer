-- package.path = '?.lua'
-- package.cpath = '?.so'
-- require("mobdebug").start()
basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
print (basedir)
if basedir == "erik.lua" then
  basedir = ""
end

package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so;.?'
local utils = require 'lem.utils'
local queue = require 'lem.queue'
local io     = require 'lem.io'

local client = require 'lem.http.client'
local downloadQueue = require 'downloadQueue'
local inspect = require  'inspect'
local slepper = utils.newsleeper()


downloadQueue:start()
for i = 0, 1000 do
    -- downloadQueue:add('http://192.168.2.8:32400/library/metadata/19360/thumb/1351259877/', 'file'..i)
end
    -- downloadQueue:add('http://192.168.2.8:32400/library/parts/17377/file.mkv', 'toradora.mkv')
local url, fileName = arg[1], arg[2]

local c = client.new()
local req = c:get(url)
-- local req = c:get('fohttp://192.168.2.8:32400/library/parts/17377/file.mkv')
local file = io.open(fileName ,"w")
req:body(function(total, data)
    file:write(data)
end)    
file:close()
print ("acabou")
-- print (a:match("([A-Z]+)/([0-9.]+) ([0-9]+)(.*)"))
