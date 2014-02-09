local utils    = require 'lem.utils'
local io       = require 'lem.io'
local client   = require 'lem.http.client'
local lfs   = require 'lem.lfs'
local dump = require 'dump'
local stringUtils = require 'utils'
local dump = require ('dump')


local function sleep(n)
  utils.newsleeper():sleep(n)
end

_M ={}


local dune = {}
local playBackMonitorIsRunning=false

local playBackMonitorFrequence=5
local stateFile = '/tmp/run/ext_command.state'

local function readExtCommandState()
    local file, err = io.streamfile(stateFile)
    if (not err) then
        data={}
        for line in file:lines() do
            a,b = line:match("(.+) = (.+)")
            if (a and b) then
                data[a] = b
            end
        end
        return data
    end
end



function dune:goToPosition(pos)
    url = "http://localhost/cgi-bin/do?cmd=set_playback_state&position="..pos
    -- url = 'http://127.0.0.1:3000/10'
    print ("chamando url", url)
    utils.spawn(function()
        local c = client.new()
        c:get(url)
    end)
end


function dune:startPlayBackMonitor( frequence, callbacks )
    playBackMonitorIsRunning = true
    playBackMonitorFrequence = frequence
    utils.spawn(
        function()
            attrs = lfs.attributes(stateFile)
            lastAttrs =  attrs
            -- count=1
            while playBackMonitorIsRunning do
                print ("data attrs     = " .. attrs['modification'])
                print ("data lastAttrs     = " .. lastAttrs['modification'])
                print ("data lastAttrs = " .. lastAttrs['modification'])
                -- print("count ", count)
                -- count = count +1
                if (tonumber(attrs['modification']) > tonumber(lastAttrs['modification'])) then
                    data = readExtCommandState()
                    print ("entrou")
                    if (callbacks[data['playback_state']]) then
                        callbacks[data['playback_state']](data)
                    end
                end
                lastAttrs = attrs
                attrs = lfs.attributes('/tmp/run/ext_command.state')
                sleep(playBackMonitorFrequence)
            end
            print("loop terminou")
        end
    )
end

function dune:stopPLaybackMonitor()
    playBackMonitorIsRunning = false
    playBackMonitorFrequence = 5
end

function dune:setPlayBackMonitorFrequence( time )
    playBackMonitorFrequence = time
end






_M=dune

return _M