-- -- vim: foldmethod=indent
-- basedir = string.gsub(arg[0], "(.*/)(.*)", "%1")
-- print (basedir)
-- if basedir == "testegd.lua" then
--   basedir = ""
-- end
-- -- package.path = basedir..'mac/?.lua;'..basedir..'mac/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
-- -- package.cpath = basedir .. 'mac/?.so;'.. basedir..'mac/lua/?.so;' ..basedir.. 'emplexer/?.so'

-- package.path = basedir..'dune/?.lua;'..basedir..'dune/lua/?.lua;'.. basedir.. 'emplexer/?.lua'
-- package.cpath = basedir .. 'dune/?.so;'.. basedir..'dune/lua/?.so;' ..basedir.. 'emplexer/?.so'

-- print(package.path)
-- print(package.cpath)

local utils    = require 'lem.utils'
local io       = require 'lem.io'
local hathaway = require 'lem.hathaway'
local client   = require 'lem.http.client'
local inspect = require ('inspect')
local urlParser =  require ('urlParser')
local gd = require("gd")

function url_decode(str)
  str = string.gsub (str, "+", " ")
  str = string.gsub (str, "%%(%x%x)",
      function(h) return string.char(tonumber(h,16)) end)
  str = string.gsub (str, "\r\n", "\n")
  return str
end

local format = string.format
hathaway.debug = print -- must be set before import()
hathaway.import()      -- when using single instance API


GET('/generate', function(req,res)
  local query = urlParser.parse(req.uri).query
  url = url_decode(query.url)
  p= query.p or 0
  if (tonumber(p)<=0) then
    res.status = 302
    res.headers['Location'] = url
    return
  end

  print ('teste do erik', inspect(url));

  r = query.r or 255
  b = query.b or 255
  g = query.g or 255
  bh = query.bh or 5



  local c = client.new()
  local  req = c:get(url)
  local data= req:body()


  local im = gd.createFromJpegStr(data)
-- print ('imagem ', inspect(im))
  y=0
  position = query.position or "botton"
  barh = bh*im:sizeY()/100
  barw = p*im:sizeX()/100
  if( position == "botton" ) then
    y = im:sizeY() - barh
  end

  print (barh, barw)

  local color = im:colorAllocate(r,g,b)
  im:filledRectangle(0, y, barw, y+barh , color)

  res:add(im:pngStr())
  -- os.execute("xdg-open out.gif")

end)



GET('/testeText', function(req, res)
  local fontPath = '/Library/Fonts/Arial.ttf'
  local bg = '/tmp/bg.jpg'
  local overlay = '/tmp/grid_tv.png'
  local bgIm = gd.createFromJpeg(bg)
  local overlayIm = gd.createFromPng(overlay)
  sx, sy = bgIm:sizeXY()
  blackTr = im2:colorAllocateAlpha(0, 0, 0, 80)
  bgIm:copy(overlayIm, 0, 0, 0, 0, sx, sy, sx, sy)
  -- bgIm:copyResized(overlayIm, 0, 0, 0, 0, sx, 600, sx, sy)
  -- copyResized(srcImage, dstX, dstY, srcX, srcY, dstW, dstH, srcw, srcH)
  white = bgIm:colorAllocate(255, 255, 255)
  bgIm:stringFT(white, "/tmp/luagd/demos/Vera.ttf", 30, 0, 560, 400, "Captain Harlock")
  res:add(bgIm:pngStr())


end)


GET('/testeTextA', function(req, res)
  local fontPath = '/Library/Fonts/Arial.ttf'
  local bg = '/tmp/bg.jpg'
  local overlay = '/tmp/grid_tv.png'
  local bgIm = gd.createFromJpeg(bg)
  -- local overlayIm = gd.createFromPng(overlay)
  sx, sy = bgIm:sizeXY()
  local blackAlpha = bgIm:colorAllocateAlpha(0, 0, 0, 70)
  bgIm:filledRectangle(0, sy-400, sx, sy, blackAlpha )
  -- bgIm:copy(overlayIm, 0, 0, 0, 0, sx, sy, sx, sy)
  -- bgIm:copyResized(overlayIm, 0, 0, 0, 0, sx, 600, sx, sy)
  -- copyResized(srcImage, dstX, dstY, srcX, srcY, dstW, dstH, srcw, srcH)
  white = bgIm:colorAllocate(255, 255, 255)
  bgIm:stringFT(white, "/tmp/luagd/demos/Vera.ttf", 30, 0, 560, 400, "Captain Harlock")
  res:add(bgIm:pngStr())


end)


GET('/teste', function (req, res)

math.randomseed(os.time())

im = gd.createFromJpeg("/tmp/luagd/demos/bugs.jpg")
assert(im)
sx, sy = im:sizeXY()

im2 = gd.createTrueColor(2*sx, sy)
black = im2:colorAllocate(0, 0, 255)
white = im2:colorAllocate(255, 255, 255)
gd.copy(im2, im, 0, 0, 0, 0, sx, sy, sx, sy)

sx2, sy2 = im2:sizeXY()
im2:stringUp(gd.FONT_SMALL, 5, sy2-10, gd.VERSION, white)

-- for i = 0, 14 do
--   for j = 0, 24 do
--     rcl = im2:colorAllocate(math.random(255), math.random(255),
--             math.random(255))
--     im2:filledRectangle(sx+20+j*10, i*20+40, sx+30+j*10, i*20+50, rcl)
--   end
-- end

im2:string(gd.FONT_GIANT, sx+80, 10, "Powered by Lua", white)

blackTr = im2:colorAllocateAlpha(0, 0, 0, 80)
im2:stringFT(blackTr, "/tmp/luagd/demos/Vera.ttf", 140, 0, 70, 130, "gd")
im2:stringFT(white, "/tmp/luagd/demos/Vera.ttf", 45, math.pi/5, 340, 250, "FreeType")


  res:add(im2:pngStr())


end)


GET('/generate1', function(req,res)
  print ('teste do erik', inspect(gd));


local im = gd.createPalette(90, 90)

local white = im:colorAllocate(255, 255, 255)
local blue = im:colorAllocate(0, 0, 255)
local red = im:colorAllocate(255, 0, 0)

im:colorTransparent(white)
im:filledRectangle(10, 10, 50, 50, blue)
im:filledRectangle(40, 40, 80, 80, red)

im:gif("out.gif")
-- os.execute("xdg-open out.gif")

end)


if arg[1] == 'socket' then
    local sock = assert(io.unix.listen('socket', 666))
    Hathaway(sock)
else
    Hathaway('*', arg[1] or '3005')
end
utils.exit(0)
