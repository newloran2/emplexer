package.path = 'mac/?.lua;mac/lua/?.lua;emplexer/?.lua'
package.cpath = 'mac/?.so;mac/lua/?.so;emplexer/?.so'

local utils    = require 'lem.utils'
local io       = require 'lem.io'
local hathaway = require 'lem.hathaway'
local register = require "register"
local lfs   = require 'lem.lfs'
local json = require ("dkjson")

hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API


function table_print (tt, indent, done)
  done = done or {}
  indent = indent or 0
  if type(tt) == "table" then
    for key, value in pairs (tt) do
      io.write(string.rep (" ", indent)) -- indent it
      if type (value) == "table" and not done [value] then
        done [value] = true
        io.write(string.format("[%s] => table\n", tostring (key)));
        io.write(string.rep (" ", indent+4)) -- indent it
        io.write("(\n");
        table_print (value, indent + 7, done)
        io.write(string.rep (" ", indent+4)) -- indent it
        io.write(")\n");
      else
        io.write(string.format("[%s] => %s\n",
            tostring (key), tostring(value)))
      end
    end
  else
    io.write(tt .. "\n")
  end
end

GET('/', function(req, res)
	print(req.client:getpeer())
	res:add("erik")
	
end)

GETM('^/startServer/([^/]+)$', function(req, res, name)
  print(req.client:getpeer()) 
  res:add('startServer')
  table_print(req)
  register:startRegister(name)
end)

GETM('^/startNotifier/([^/]+)/([^/]+)$', function (req, res, key, percentToDone )
  res:add(key .. ": " .. percentToDone)
  utils.spawn(function()
    attrs = lfs.attributes('/tmp/run/ext_command.state')
    lastAttrs = nil
    file = io.streamfile('/tmp/run/ext_command.state')
    -- while attrs['modification'] >
    if not file then
      io.stderr:write(format("Error opening '%s': %s\n", arg[1], err))
      utils.exit(1)
    else 
      for i=1,10 do

        print(file:lines())
        utils.newsleeper():sleep(5)
      end
    end
  end)
end)

GET('/stopServer', function ( req, res )
	print(req.client:getpeer())	
	res:add('stopServer')
	register:stopRegister()
end)

GET('/findServers' , function ( req, res )
	print('findServers');
	res:add (json.encode(register:getPlexServers()))	
end)



if arg[1] == 'socket' then
	local sock = assert(io.unix.listen('socket', 666))
	Hathaway(sock)
else
	Hathaway('*', arg[1] or '8080')
end
utils.exit(0) -- otherwise open connections will keep us running

-- vim: syntax=lua ts=2 sw=2 noet:
