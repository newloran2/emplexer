<?php
class LogInterceptor extends AbstractInterceptor {
    function around($object, $method, $args){
        print "before $method <br />";
        $value = $this->callMethod($method, $args);
        print "after $method <br />";
        return $value;
    }
}

 ?>