basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
print (basedir)
if basedir == "myPlex.lua" then
  basedir = ""
end
package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'

print(package.path)
print(package.cpath)
-- local utils = require 'lem.utils'
local format = string.format
local https = require 'ssl.https'
local inspect = require 'inspect'


-- local function sleep(n)
--   utils.newsleeper():sleep(n)
-- end
--
-- _M = {}
--
-- local plex ={}
--

resp = {}
local r, c, h, s = https.request{
    url = "https://plex.tv/",
    sink = ltn12.sink.table(resp),
    protocol = "tlsv1"
}


print (inspect(table.concat(resp)))
