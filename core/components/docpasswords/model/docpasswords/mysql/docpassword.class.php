<?php

require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/docpassword.class.php');

class Docpassword_mysql extends Docpassword
{

    public function __construct(& $xpdo)
    {
        parent :: __construct($xpdo);
    }

}