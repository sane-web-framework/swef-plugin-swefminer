<?php

namespace Swef\Bespoke;

class SwefMinerTable extends \Swef\Base\SwefMinerTable {

    public function __construct ($name) {
        // Always construct the base class - PHP does not do this implicitly
        parent::__construct ($name);
    }

    public function __destruct ( ) {
        // Always destruct the base class - PHP does not do this implicitly
        parent::__destruct ();
    }

}

?>
