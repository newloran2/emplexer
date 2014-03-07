function! EmplexerTester()
    let line = getline(line('.'))
    let command  = 'wget -q -O - "http://127.0.0.1/emplexer/index.php" --post-data ''data=' . line . '''|python -mjson.tool'
    silent! execute 'belowright new teste'
    setlocal buftype=nowrite bufhidden=wipe nobuflisted noswapfile nowrap number
    silent execute '$read !'. command
    silent execute 'resize' . line ('$')
    silent execute 'goto 1'
endfunction
map <F8> :call EmplexerTester()<cr>

