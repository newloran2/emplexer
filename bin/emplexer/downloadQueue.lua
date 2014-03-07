local queue = require 'lem.queue'
local utils    = require 'lem.utils'
local client   = require 'lem.http.client'
local inspect = require 'inspect'
local lfs   = require 'lem.lfs'

_M = {}


local isStarted = false
local downloadQueue ={}
local fileList ={}
local consumers = 0
local root =  "/tmp/teste/"


local function consumer(q, id)
    consumers = consumers + 1
    local sleeper = utils.newsleeper()
    local c = client.new()
    for v in q:consume() do
    	print (inspect(v))
        fileName = root .. v.f
        url = v.u
        c:download(url, fileName)
        fileList[fileName] = true
        print (inspect(fileList))
    end
    consumers = consumers - 1

end

local q, sleeper = queue.new(), utils.newsleeper()


function downloadQueue:refresh()
	for file in lfs.dir (root) do
		if (file ~= '..' and file ~= '.') then
	    	fileList[root..file] = true
	    end
	end
end

function downloadQueue:add(url, fileName)
	fileName = root..fileName
	print ("fileName =", fileName)
	if (not fileList[fileName]) then
		print ("não existe ainda", inspect(q))
		q:put({u=url,f=fileName})
		return url
	else
		-- a= fileList[fileName]
		return fileName
	end
end

function downloadQueue:start()
	if (not isStarted) then
		self:refresh()
		utils.spawn(consumer, q, 1)
		utils.yield()
		isStarted = true
	end
end

_M = downloadQueue
return _M