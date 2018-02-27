<?php

namespace Swef;

class SwefMiner extends \Swef\Bespoke\Plugin {


/*
    PROPERTIES
*/

    public  $browse;
    public  $columns;
    public  $rows;


/*
    EVENT HANDLER SECTION
*/

    public function __construct ($page) {
        // Always construct the base class - PHP does not do this implicitly
        parent::__construct ($page,'\Swef\SwefMiner');
    }

    public function __destruct ( ) {
        // Always destruct the base class - PHP does not do this implicitly
        parent::__destruct ( );
    }


/*
    DASHBOARD SECTION
*/


    public function _dashboard ( ) {
        require_once swefminer_file_dash;
    }

    public function _info ( ) {
        $info   = __FILE__.SWEF_STR__CRLF;
        $info  .= SWEF_COL_CONTEXT.SWEF_STR__EQUALS;
        $info  .= $this->page->swef->context[SWEF_COL_CONTEXT];
        return $info;
    }

}

?>
