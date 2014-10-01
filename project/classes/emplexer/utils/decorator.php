<?php
class Decorator
{
    protected $foo;

    function __construct($foo) {
       $this->foo = $foo;
    }

    public function __call($method_name, $args) {
       hd_print(sprintf("Metodo: %s->%s ", get_class($this->foo), $method_name));
       $ret =  call_user_func_array(array($this->foo, $method_name), $args);
       // hd_print_r("retorno =", $ret);
       return $ret;
    }
}

?>