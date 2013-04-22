<?php
///////////////////////////////////////////////////////////////////////////

$HD_NEW_LINE = '';

function hd_print($str)
{
    global $HD_NEW_LINE;
    echo $str . $HD_NEW_LINE;
}

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

// Install error handlers so that errors appear in the log.

function hd_error_handler($error_type, $message, $file, $line)
{
    static $map = array
    (
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED'
    );

    if (isset($map[$error_type]))
        hd_print("[$file:$line] [$map[$error_type]] $message");
}

///////////////////////////////////////////////////////////////////////////

function hd_shutdown_handler()
{
  $err = error_get_last();

  hd_error_handler($err['type'], $err['message'], $err['file'], $err['line']);
}

///////////////////////////////////////////////////////////////////////////

set_error_handler('hd_error_handler');
register_shutdown_function('hd_shutdown_handler');

///////////////////////////////////////////////////////////////////////////

function hd_error_silencer($severity, $msg, $filename, $linenum)
{ }

///////////////////////////////////////////////////////////////////////////

function hd_silence_warnings()
{
    error_reporting(0);
    set_error_handler("hd_error_silencer");
}

///////////////////////////////////////////////////////////////////////////

function hd_restore_warnings()
{
    restore_error_handler();
    error_reporting(E_STRICT | E_ALL);
}

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

// Use timezone from the environment (TZ).
// This is a workaround for known issue since 5.3.0.

hd_silence_warnings();
ini_set('date.timezone', date_default_timezone_get());
hd_restore_warnings();

///////////////////////////////////////////////////////////////////////////

set_time_limit(0);

///////////////////////////////////////////////////////////////////////////
?>
