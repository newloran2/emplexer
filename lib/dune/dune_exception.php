<?php

namespace lib\dune;

use Exception;

class DuneException extends Exception
{
    private $error_action;

    public function __construct($message, $code = 0,
        $error_action = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->error_action = $error_action;
    }

    public function get_error_action()
    {
        return $this->error_action;
    }
}


?>
