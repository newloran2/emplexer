--[[{{{teste de conversão de imagens usando json de template

campos comums com tratamentos diversos:
    w: 
        esse campo pode receber valores em:
            porcentagem (calcula a a largura em pixels com base na largura da imagem source) ex: "10%"
            pixels (número absoluto de pixels a ser utilizado na operação) ex: 100
            source (número de pixels da imagem source)
            operações matemáticas:
                todos os campos acima podem ser usados em operações matemáticas para posicionamento
                ex:
                    w: "25% -100" (irá calcular o tamanho em pixel de 25% do tamanho total do source e subtrair disso 100
                    w: "25% + 25%" ( irá calcular o tamanho das duas porcetagens e somar ambas )
                    w: "source - 25%" (irá executar a ação a partir de 75% do tamanho da imagem pos subtraiu 25% da altura total da imagem)

    h:
        idem a w
    x:
        posição horizontal em que a ação ira executar sua operação
        pixels : número absoluto que representa a onde a operação será executada (ex: 199)
        left: o equivalente a zero da imagem source
        rigth: o equivalente a largura da imagem source
        porcetagem: idem a w
        operações matemáticas:
            idem a w
        



campo colors:
    define uma tabela de cores reutilizaveis
    Esse objeto é composto por outros objetos, cada um desses objetos internos tem um campo htmlHex que é uma definição de cor com notação html:
    caso uma cor seja informada em qualquer lugar do template e a mesma não começe com # é substituida pela cor do objeto color com o mesmo nome do valor informado.
    Isso é feito no processo de parse do template.

campo images:
    contem um array com vários objetos image

campo image: 
    definido somente dentro do objeto images.
    contem a definição de uma imagem source e todas as ações que nela serão aplicadas
    campo source:
        definição de onde está a imagem a ser carregada (essa imagem pode ser um png ou um jpeg)
]]


local inspect = require "inspect"
local json = require ("dkjson")
local gd = require ("gd")

_M={}
local Convert={}
local localFunctions=setmetatable({}, {__index = function (t, k)
        return setmetatable({}, {__call = function(k) return nil end})
    end})

local template=nil
local im=nil
local h=nil
local w=nil
local source = nil

Convert.__index = Convert
_M.Convert = Convert

function string.starts(String,Start)
   return string.sub(String,1,string.len(Start))==Start
end


function string.ends(String,End)
   return End=='' or string.sub(String,-string.len(End))==End
end

string.split = function(str, pattern)
  pattern = pattern or "[^%s]+"
  if pattern:len() == 0 then pattern = "[^%s]+" end
  local parts = {__index = table.insert}
  setmetatable(parts, parts)
  str:gsub(pattern, parts)
  setmetatable(parts, nil)
  parts.__index = nil
  return parts
end

local function processExpresion(field,exp)
    print ("processExpresion", field, exp)
     local functionsForFields={}
     functionsForFields['source']={
        h=function ()
            return h
        end,
        w=function ()
            return w
        end
    }
    functionsForFields['botton'] =function() 
        print ("teste")
    end
    percentFunctions  = {}
    percentFunctions.h=function(p)
            p = tonumber(p)
            return p*h/100
        end

    percentFunctions.w=function(p)
            p = tonumber(p)
            return p*w/100
        end
    percentFunctions.x      = percentFunctions.w
    percentFunctions.y      = percentFunctions.w
    percentFunctions.x1     = percentFunctions.w
    percentFunctions.y1     = percentFunctions.h
    percentFunctions.x2     = percentFunctions.w
    percentFunctions.y2     = percentFunctions.h
    functionsForFields['%'] = percentFunctions

    functionsForFields['#'] = {
        color=function(code)
            ret={
            tonumber(code:sub(1,2),16),
            tonumber(code:sub(3,4),16),
            tonumber(code:sub(5,6),16)
            }
            return ret;
        end
    }

    functionsForFields['$'] = {
        sum=function(field,p)
            print ("estou dentro de $sub", inspect(p))
            ret = 0
            for k,v in pairs(p) do
                print ("valores de k,v dentro de $sum", k,v)
                expTrigger, expValue = v:gmatch("(%W)([0-9]+)")()
                print (expTrigger, expValue)
                if (expTrigger and expValue ) then
                    ret = ret + (tonumber(functionsForFields[expTrigger][field](expValue))) or 0
                else
                    ret = ret + tonumber(v) or 0
                end
            end
            print("valor de ret", ret)
            return ret
        end,
        sub=function(field,p)
             print ("estou dentro de $sub", inspect(p))
            ret = 0
            for k,v in pairs(p) do
                print ("valores de k,v dentro de $sub", k,v)
                expTrigger, expValue = v:gmatch("(%W)([0-9]+)")()
                print (expTrigger, expValue)
                if (expTrigger and expValue ) then
                    ret = ret == 0 and (tonumber(functionsForFields[expTrigger][field](expValue))) or ret - tonumber(functionsForFields[expTrigger][field](expValue)) or 0
                else
                    ret = ret == 0 and tonumber(v) or ret - tonumber(v) or 0
                end
                print ("valor parcial de sub", ret)
            end
            print("valor de ret de sub", ret)
            return ret

        end
    }
    setmetatable(functionsForFields, {__index = function (t, k)
        return setmetatable({}, {__call = function(k) return nil end})
    end})

    if (type(exp) == "string") then
        if (exp == "source") then
            return functionsForFields['source'][field] ~=nil and functionsForFields['source'][field](exp) or nil
        end
        if (string.starts(exp,"%") or string.starts(exp, "#")) then
            expTrigger = exp:sub(1,1)
            expValue   = exp:sub(2,exp:len())
            print ( "valores de expTrigger e expValue", expTrigger, expValue)
            return functionsForFields[expTrigger][field](expValue)
        elseif (string.starts(exp, '$')) then
            expTrigger, expValue = exp:gmatch("([a-z]+)(.*)")()
            print ("valores de $", expTrigger, expValue) 
            if (expTrigger and expValue) then
                return functionsForFields['$'][expTrigger](field,expValue:split("[^,^(,^),^ ]+"))
            end
        else
            return exp
        end
    end
    if ( type(exp) ==  "number") then
        return exp
    end
    return 0
end
function localFunctions.createImage(j)
    source = j.images[1].source
end
function Convert.init(j)
    file = io.open(j)
    j = file:read("*a")
    file:close()
    template =json.decode(j)
    return setmetatable({},Convert)
end


function localFunctions.line(t)
-- x1   = processExpresion('x1', t['x1'] or 10)
-- x2   = processExpresion('x2', t['x2'] or 10)
-- y1   = processExpresion('y1', t['y1'] or 10)
-- y1   = processExpresion('y2', t['y2'] or 10)
-- blue = im:colorAllocate(0,255,0)
-- im:filledRectangle(x1,y1,x2,y2, color)

end

function localFunctions.filledRectangle(t)
    x1    = processExpresion('x1', t['x1'] or 10)
    x2    = processExpresion('x2', t['x2'] or 10)
    y1    = processExpresion('y1', t['y1'] or 10)
    y2    = processExpresion('y2', t['y2'] or 10)
    c     = processExpresion('color', t['color'] or {0,0,0})
    color = im:colorAllocate(table.unpack(c))
    print ("filledRectangle", x1, y1, x2, y2)
    im:filledRectangle(x1,y1,x2,y2, color)
end
function localFunctions.drawText(te)
print("drawText", inspect(te[1]))
    for k, t in pairs(te) do
        print ("valores de drawText", k, t)
        angle = processExpresion('angle', t['angle'] or 0)
        size  = processExpresion('size', t['size'] or 13)
        text  = processExpresion('text', t['text'] or "não veio")
        color = processExpresion('color', t['color'] or {0,0,0})
        font  = processExpresion('font', t['font'] or "/Library/Fonts/Arial.ttf")
        x     = processExpresion('x', t['x'] or 0)
        y     = processExpresion('y', t['y'] or 0)

        im:stringFT(im:colorAllocate(table.unpack(color)), font, size, angle, x, y, text)
    end
end

function localFunctions.drawPrimitive(t)
    for k, v in pairs(t) do
        localFunctions[v['type']](v)
    end
end




function Convert:generate()
    im = gd.createFromJpeg(template.image.source)
    w,h = im:sizeXY()
    newW = processExpresion('w', template.image.w or w)
    newH = processExpresion('h', template.image.h or h)
    for k, v in pairs (template.image.actions) do
        -- print ( "valores :", k, v )
        if ( localFunctions[k] ~= nil) then
            print ("valor de drawPrimitive", inspect(localFunctions[k](v)))
        end
    end
    print (newW,newH)
    if (newW ~= w or newH ~= h) then
    end
    im:jpeg(template.image.output, 90)
    print (collectgarbage('count'))
end
-- print (inspect(img.source))
convert = Convert.init("/Users/newloran2/Dropbox/Projeto/Dune/emplexer/bin/converter.json")
convert:generate()
