
function! EmplexerTesterNoNewWin()
    let line = getline(line('.'))
    let command  = 'wget -q -O - "http://127.0.0.1/emplexer/index.php" --post-data ''data=' . line . '''|python -mjson.tool'
    let val =system(command)
endfunction
function! EmplexerTester()
    let line = getline(line('.'))
    let command  = 'wget -q -O - "http://127.0.0.1/emplexer/index.php" --post-data ''data=' . line . '''|python -mjson.tool'
    silent! execute 'belowright new teste'
    setlocal buftype=nowrite bufhidden=wipe nobuflisted noswapfile nowrap number
    map <buffer> q :bd<cr>
    silent execute '$read !'. command
    silent execute 'resize' . line ('$')
    silent execute 'goto 1'
    set syntax=javasctipt
endfunction

map <F9> :call EmplexerTesterNoNewWin()<cr>
map <F8> :call EmplexerTester()<cr>
set relativenumber 
autocmd BufEnter *.php execute 'NeoSnippetSource snippets/php.snippets' 
