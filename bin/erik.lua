package.path = '?.lua'
package.cpath = '?.so'

local utils = require 'lem.utils'
local queue = require 'lem.queue'
local io     = require 'lem.io'
local client = require 'lem.http.client'

local consumers = 0

local function consumer(q, id)
    consumers = consumers + 1
    local sleeper = utils.newsleeper()
    local c = client.new()
    for v in q:consume() do
        assert(c:download(v[1], v[2]))
        print(collectgarbage("count"))
        -- print(string.format('thread %d, n = %2d, received "%s"',
        --     id, q.n, tostring(v)))
        -- sleeper:sleep(0.5)
    end
    assert(c:close())

    consumers = consumers - 1
end



local q, sleeper = queue.new(), utils.newsleeper()

local file, err = io.streamfile('/tmp/urls.txt')

if (file) then
    counter =1
    for line in file:lines() do
        q:put({line,"/tmp/cache/teste"..counter .. ".jpg"})
        counter = counter +1
    end
end

utils.spawn(consumer, q , 1)
utils.yield()