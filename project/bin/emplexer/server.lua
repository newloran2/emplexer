local utils = require "lem.utils"
local io = require "lem.io"
local hathaway = require 'lem.hathaway'
local register =require "register"
local jsonrpc = require "jsonrpc"

hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API

GET("/", function ( req, res )
    print ("functiona")
    res:add("functiona")
end)

GET("/quit", function ( req, res )
    print("esse servidor será terminados")
    res:add("esse servidor será terminados")
    hathaway.server:close()
end)

GET("/startMonitor", function ( req, res )
    res:add("tentando iniciar o monitor")
    register:startRegister()
end)

GET("/stopMonitor", function ( req, res )
    res:add("tentando terminar o monitor")
    register:stopRegister()
end)

POST("/jsonrpc", function ( req, res )
    local body = req:body()
    print (body)
    -- jsonrpc:handleAction(body)

end)

utils.spawn( function (  )
    register:startRegister()    
end)

Hathaway("*", "3000")
utils.exit(0)