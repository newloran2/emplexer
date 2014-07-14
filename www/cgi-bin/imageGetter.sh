#!/bin/bash
echo "Content-Type: image/png"
echo ""



#um script para fazer download de uma imagem caso a mesma ainda não esteja presente.
#Esse script é usado em conjunto com o "async_icon_loading": true no dune dessa forma é possivel combinar async_icon_loading com image cache no mesmo plugin

# script espera um parâmetro url com a url da imagem.
# essa url deve ser urlencoded
saveIFS=$IFS
IFS='=&'
set -- $QUERY_STRING
IFS=$saveIFS
url=$(echo -e "$(echo $2 | sed 'y/+/ /; s/%/\\x/g')")
url=$(echo -e "$(echo $url | sed 'y/+/ /; s/%/\\x/g'| sed 's/ /%20/g')")
echo $url > log.log
fileName=$(echo $url | md5sum| awk '{print $1}')
if [ ! -f $fileName ]; then
    wget "$url" -O -|tee $fileName
else
    cat $fileName
fi