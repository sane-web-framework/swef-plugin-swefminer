<?php

namespace Swef\Base;

class SwefMinerTable {

    private $columns          = array ();
    private $name;

    public function __construct ($name) {
        $this->name  = $name;
    }

    public function __destruct ( ) {
    }

}

?>
