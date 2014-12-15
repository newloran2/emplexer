local M = {}

local ringbuffer = {}
ringbuffer.__index = ringbuffer
M.ringbuffer = ringbuffer

function M.new(length)
   return setmetatable({first=0,size=length}, ringbuffer)
end

function ringbuffer:add(data)
   local first, size = self.first,self.size
   if data then
      first = first +1 > size and 1 or first +1
      self[first] = data
      self.first = first
      print ("first agora é", first)
   end
end

function ringbuffer:get()
   local first, size = self.first,self.size
   first = first - 1 < 1 and 1 or first -1
   self.first = first
   print ("first agora é", first, size)
   return self[first+1]
end

function ringbuffer:clear()
   for i=0, self.size, 1 do
      self[i]=nil
   end
   self.first=1
end

return M
