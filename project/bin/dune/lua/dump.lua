local _M = {}
local _NAME = ... or 'test'

local io = require 'io'
local os = require 'os'
local table = require 'table'
local string = require 'string'

_M.groupsize = 10000

local dumptablecontent

local tkeys = {
	boolean = true,
	number = true,
	string = true,
}
local tvalues = {
	boolean = true,
	number = true,
	string = true,
	table = true,
}

local function dumptable(table, write, level)
	-- prefix and suffix
	local mt = getmetatable(table)
	local prefix = mt and mt.__dump_prefix
	local suffix = mt and mt.__dump_suffix
	if type(prefix)=='function' then
		prefix = prefix(table)
	end
	prefix = prefix or ""
	if type(suffix)=='function' then
		suffix = suffix(table)
	end
	suffix = suffix or ""
	
	-- count keys
	local nkeys = 0
	for k,v in pairs(table) do
		nkeys = nkeys + 1
		local tk,tv = type(k),type(v)
		if not tkeys[tk] then
			return nil,"unsupported key type '"..tk.."'"
		end
		if not tvalues[tv] then
			return nil,"unsupported value type '"..tv.."'"
		end
	end
	
	-- if too many keys, use multiple closures
	if nkeys > _M.groupsize then
		local success,err
		success,err = write(((prefix..[[
(function()
	local t = { function() return {
]]):gsub("\n", "\n"..("\t"):rep(level))))
		if not success then return nil,err end
		success,err = dumptablecontent(table, write, level+2, _M.groupsize, ("\t"):rep(level+1)..'} end, function() return {\n')
		if not success then return nil,err end
		success,err = write((([[
	} end }
	local result = {}
	for _,f in ipairs(t) do
		for k,v in pairs(f()) do
			result[k] = v
		end
	end
	return result
end)()]]..suffix):gsub("\n", "\n"..("\t"):rep(level))))
		if not success then return nil,err end
		return true
	elseif nkeys==0 then
		local success,err = write(prefix.."{ }"..suffix)
		if not success then return nil,err end
		return true
	else
		local success,err
		success,err = write(prefix.."{\n")
		if not success then return nil,err end
		success,err = dumptablecontent(table, write, level+1)
		if not success then return nil,err end
		success,err = write(("\t"):rep(level).."}"..suffix)
		if not success then return nil,err end
		return true
	end
end

local function dumpvalue(v, write, level)
	local t = type(v)
	if t=='string' then
		return write('"'..v:gsub('[%z\1-\31\127"\\]', function(c)
			if c=='\\' then
				return '\\\\'
			elseif c=='"' then
				return '\\"'
			elseif c=='\t' then
				return '\\t'
			elseif c=='\n' then
				return '\\n'
			elseif c=='\r' then
				return '\\r'
			else
				return string.format('\\%03d', string.byte(c))
			end
		end)..'"')
	elseif t=='number' then
		if v~=v then -- nan
			return write('0/0')
		elseif v==1/0 then -- +inf
			return write('1/0')
		elseif v==-1/0 then -- -inf
			return write('-1/0')
		else
			return write(tostring(v))
		end
	elseif t=='boolean' then
		if v then
			return write('true')
		else
			return write('false')
		end
	elseif t=='nil' then
		return write('nil')
	elseif t=='table' then
		return dumptable(v, write, level)
	else
		return nil,"unsupported value type '"..t.."'"
	end
end

local lua_keywords = {
	['and'] = true,
	['break'] = true,
	['do'] = true,
	['else'] = true,
	['elseif'] = true,
	['end'] = true,
	['false'] = true,
	['for'] = true,
	['function'] = true,
	['if'] = true,
	['in'] = true,
	['local'] = true,
	['nil'] = true,
	['not'] = true,
	['or'] = true,
	['repeat'] = true,
	['return'] = true,
	['then'] = true,
	['true'] = true,
	['until'] = true,
	['while'] = true,
}

local function dumppair(k, v, write, level)
	local success,err
	success,err = write(("\t"):rep(level))
	if not success then return nil,err end
	local assignment = " = "
	local tk = type(k)
	if tk=='string' and k:match("^[_a-zA-Z][_a-zA-Z0-9]*$") and not lua_keywords[k] then
		success,err = write(k)
		if not success then return nil,err end
	elseif tk=='string' or tk=='number' or tk=='boolean' then
		success,err = write('[')
		if not success then return nil,err end
		success,err = dumpvalue(k, write, level)
		if not success then return nil,err end
		success,err = write(']')
		if not success then return nil,err end
	elseif tk=='nil' then
		-- we are in the array part
		assignment = ""
	else
		error("unsupported key type '"..type(k).."'")
	end
	success,err = write(assignment)
	if not success then return nil,err end
	success,err = dumpvalue(v, write, level)
	if not success then return nil,err end
	success,err = write(",\n")
	if not success then return nil,err end
	return true
end

local function keycomp(a, b)
	local ta,tb = type(a),type(b)
	if ta==tb then
		return a < b
	else
		return ta=='string'
	end
end

local tsort = table.sort
local function dumptablesection(table, write, level, keys, state)
	-- sort keys
	local skeys = {}
	for k in pairs(keys) do skeys[#skeys+1] = k end
	tsort(skeys, keycomp)
	-- dump pairs
	for _,k in pairs(skeys) do
		local v = table[k]
		if state then
			state.i = state.i + 1
			if state.i % state.size == 0 then
				local success,err = write(state.sep)
				if not success then return nil,err end
			end
		end
		local success,err = dumppair(k, v, write, level)
		if not success then return nil,err end
	end
	return true
end

local function dumptableimplicitsection(table, write, level, state)
	for k,v in ipairs(table) do
		if state then
			state.i = state.i + 1
			if state.i % state.size == 0 then
				local success,err = write(state.sep)
				if not success then return nil,err end
			end
		end
		local success,err
		if state then
			success,err = dumppair(k, v, write, level)
		else
			success,err = dumppair(nil, v, write, level)
		end
		if not success then return nil,err end
	end
	return true
end

function dumptablecontent(table, write, level, groupsize, groupsep)
	-- order of groups:
	-- - explicit keys
	--   - keys with simple values
	--   - keys with structure values (table with only explicit keys)
	--   - keys with mixed values (table with both exiplicit and implicit keys)
	--   - keys with array values (table with only implicit keys)
	-- - set part (explicit key with boolean value)
	-- - implicit keys
	-- order within a group:
	-- - string keys in lexicographic order
	-- - numbers in increasing order
	-- :TODO: handle tables as keys
	-- :TODO: handle sets
	
	-- extract implicit keys
	local implicit = {}
	for k,v in ipairs(table) do
		implicit[k] = true
	end
	-- categorize explicit keys
	local set = {}
	local simples = {}
	local structures = {}
	local mixeds = {}
	local arrays = {}
	for k,v in pairs(table) do
		if not implicit[k] then
			if type(v)=='table' then
				if v[1]==nil then
					structures[k] = true
				else
					local implicit = {}
					for k in ipairs(v) do
						implicit[k] = true
					end
					local mixed = false
					for k in pairs(v) do
						if not implicit[k] then
							mixed = true
							break
						end
					end
					if mixed then
						mixeds[k] = true
					else
						arrays[k] = true
					end
				end
			else
				simples[k] = true
			end
		end
	end
	
	local success,err,state
	if groupsize and groupsep then
		state = {
			i = 0,
			size = groupsize,
			sep = groupsep,
		}
	end
	success,err = dumptablesection(table, write, level, simples, state)
	if not success then return nil,err end
	success,err = dumptablesection(table, write, level, structures, state)
	if not success then return nil,err end
	success,err = dumptablesection(table, write, level, mixeds, state)
	if not success then return nil,err end
	success,err = dumptablesection(table, write, level, arrays, state)
	if not success then return nil,err end
	success,err = dumptableimplicitsection(table, write, level, state)
	if not success then return nil,err end
	return true
	
	--[[
	local done = {}
	for k,v in ipairs(table) do
		local success,err = dumppair(nil, v, write, level)
		if not success then return nil,err end
		done[k] = true
	end
	for k,v in pairs(table) do
		if not done[k] then
			local success,err = dumppair(k, v, write, level)
			if not success then return nil,err end
			done[k] = true
		end
	end
	return true
	--]]
end

function _M.tostring(value)
	local t = {}
	local success,err = dumpvalue(value, function(str) table.insert(t, str); return true end, 0)
	if not success then return nil,err end
	return table.concat(t)
end

function _M.tofile(value, file)
	local filename
	if type(file)=='string' then
		filename = file
		file = nil
	end
	local success,err
	if filename then
		file,err = io.open(filename, 'wb')
		if not file then return nil,err end
	end
	success,err = file:write"return "
	if not success then return nil,err end
	success,err = dumpvalue(value, function(...) return file:write(...) end, 0)
	if not success then return nil,err end
	success,err = file:write("\n-- v".."i: ft=lua\n")
	if not success then return nil,err end
	if filename then
		success,err = file:close()
		if not success then return nil,err end
	end
	return true
end

function _M.tofile_safe(value, filename, oldsuffix)
	local lfs = require 'lfs'
	
	if oldsuffix and lfs.attributes(filename, 'mode') then
		local i,suffix = 0,oldsuffix
		while io.open(filename..suffix, "rb") do
			i = i+1
			suffix = oldsuffix..i
		end
		assert(os.rename(filename, filename..suffix))
	end
	local tmpfilename = filename..'.new'
	local err,file,success
	file,err = io.open(tmpfilename, "wb")
	if not file then return nil,err end
	success,err = _M.tofile(value, file)
	if not success then
		file:close()
		os.remove(tmpfilename)
		return nil,err
	end
	success,err = file:close()
	if not success then
		os.remove(tmpfilename)
		return nil,err
	end
	if lfs.attributes(filename, 'mode') then
		assert(os.remove(filename))
	end
	assert(os.rename(tmpfilename, filename))
	return true
end

if _NAME=='test' then
	local str = [[
return {
	Abc = false,
	FOO = 42,
	Foo = "42",
	abc = true,
	["f O"] = 42,
	fOO = 42,
	foo = "42",
	[-1] = 37,
	[0] = 37,
	[42] = 37,
	Bar = {
		foo = 142,
	},
	bar = {
		foo = 142,
	},
	Baz = {
		foo = 242,
		237,
	},
	baz = {
		foo = 242,
		237,
	},
	Baf = {
		337,
	},
	baf = {
		337,
	},
	37,
}
-- v]]..[[i: ft=lua
]]
	local t
	if _VERSION=="Lua 5.1" then
		t = assert(loadstring(str))()
	elseif _VERSION=="Lua 5.2" then
		t = assert(load(str))()
	else
		error("unsupported Lua version")
	end
	
	local filename = os.tmpname()
	if pcall(require, 'lfs') then
		assert(_M.tofile_safe(t, filename))
	else
		assert(_M.tofile(t, filename))
	end
	local file = assert(io.open(filename, "rb"))
	local content = assert(file:read"*a")
	assert(file:close())
--	print("=================================")
--	print(content)
--	print("=================================")
	assert(content==str, "tested string and dumped equivalent mismatch")
	local str2 = assert(_M.tostring(t))
	assert(content:sub(8, -16)==str2, "tested string and dumped equivalent mismatch")
	assert(_M.tostring(string.char(0, 1, 7, 9, 10, 13, 14, 31, 32, 33, 126, 127, 128, 129, 254, 255))
		==[["\000\001\007\t\n\r\014\031 !~\127]].."\128\129\254\255"..[["]])
	assert(_M.tostring(0/0)=='0/0')
	assert(_M.tostring(1/0)=='1/0')
	assert(_M.tostring(-1/0)=='-1/0')
	print("all tests passed successfully")
end

return _M
