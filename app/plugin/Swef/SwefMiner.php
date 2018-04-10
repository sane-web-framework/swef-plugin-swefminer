<?php

namespace Swef;

class SwefMiner extends \Swef\Bespoke\Plugin {


/*
    PROPERTIES
*/

    public  $browse;
    public  $columns                = array ();
    public  $models                 = array ();
    public  $joins                  = array ();
    public  $rows                   = array ();
    public  $supportedPDODrivers    = array ();
    public  $tables                 = array ();


/*
    EVENT HANDLER SECTION
*/

    public function __construct ($page) {
        // Always construct the base class - PHP does not do this implicitly
        parent::__construct ($page,'\Swef\SwefMiner');
        $this->supportedPDODrivers = explode (SWEF_STR__COMMA,swefminer_supported_pdo_drivers);
    }

    public function __destruct ( ) {
        // Always destruct the base class - PHP does not do this implicitly
        parent::__destruct ( );
    }


/*
    DASHBOARD SECTION
*/

    public function _dashboard ( ) {
        $this->_init ();
        $this->dashboardController ();
        require_once swefminer_file_dash;
        
    }

    public function _info ( ) {
        require_once swefminer_file_info;
    }


/*
    SUPPORTING METHODS
*/

    public function _init ( ) {
        $constants          = get_defined_constants (SWEF_BOOL_TRUE) [swefminer_col_user];
        $len                = strlen (swefminer_str_pfx_dm);
        foreach ($constants as $c=>$dsn) {
            if (strpos($c,swefminer_str_pfx_dm)!==SWEF_INT_0) {
                continue;
            }
            $c = substr ($c,$len);
            if (!strlen($c)) {
                continue;
            }
            if (substr($c,(SWEF_INT_0-strlen(swefminer_str_sfx_dsn)))!=swefminer_str_sfx_dsn) {
                continue;
            }
            $dm             = substr ($c,0,(SWEF_INT_0-strlen(swefminer_str_sfx_dsn)));
            $dsn            = constant (swefminer_str_pfx_dm.$c);
            if (!defined(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn)) {
                $this->notify ('Data model "'.$dm.'" has no database name - define '.swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn);
                return;
            }
            $dbn            = constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn);
            if (!defined(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_usr)) {
                $this->notify ('Data model "'.$dm.'" has no database user - define '.swefminer_str_pfx_dm.$dm.swefminer_str_sfx_usr);
                return;
            }
            if (!defined(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_pwd)) {
                $this->notify ('Data model "'.$dm.'" has no database password - define '.swefminer_str_pfx_dm.$dm.swefminer_str_sfx_pwd);
                return;
            }
            if (!defined(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tag)) {
                $this->notify ('Data model "'.$dm.'" has no descriptive tag - define '.swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tag);
                return;
            }
            $this->models[$dm] = constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tag);
        }
        if (!($dm=$this->page->_GET(SWEF_GET_OPTION))) {
            return;
        }
        if (!array_key_exists($dm,$this->models)) {
            return;
        }
        if (defined(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tlk)) {
            $table_like     = constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tlk);
        }
        else {
            $table_like     = swefminer_str_like_all;
        }
        $dsn                = constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dsn);
        $dsn               .= ';dbname=';
        $dsn               .= constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn);
        $this->db           = new \Swef\Bespoke\Database (
            $dsn,
            constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_usr),
            constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_pwd)
        );
        $this->tables       = $this->db->dbCall (
            swefminer_call_model_tables,
            constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn),
            $table_like
        );
        if (!is_array($this->tables)) {
            $this->notify ('A database error occurred (selecting tables for model)');
            return;
        }
        if (!count($this->tables)) {
            $this->notify ('A database error occurred (no tables like "'.$table_like.'" in database "'.constant(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn).'")');
            return;
        }
        $this->page->diagnosticAdd ('Data model "'.$dm.'": '.$this->db->dbCallLast());
        $meta               = $this->page->swef->db->dbCall (
            swefminer_call_tables,
            constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn),
            $table_like
        );
        foreach ($this->tables as $i=>$t) {
            foreach ($meta as $m) {
                if ($m[swefminer_col_database]!=$t[swefminer_col_table_schema]) {
                    continue;
                }
                if ($m[swefminer_col_table]!=$t[swefminer_col_table_name]) {
                    continue;
                }
                foreach ($m as $f=>$v) {
                    $this->tables[$i][$f] = $v;
                }
                if (array_key_exists(swefminer_col_inserters,$t)) {
                    if (strlen($t[swefminer_col_inserters])) {
                        $this->tables[$i][swefminer_col_inserters] = explode (
                            SWEF_STR__COMMA
                           ,$t[swefminer_col_inserters]
                        );
                    }
                    else {
                        $this->tables[$i][swefminer_col_inserters] = array ();
                    }
                }
                else {
                    $this->tables[$i][swefminer_col_inserters] = array ();
                }
                if (array_key_exists(swefminer_col_deleters,$t)) {
                    if (strlen($t[swefminer_col_deleters])) {
                        $this->tables[$i][swefminer_col_deleters] = explode (
                            SWEF_STR__COMMA
                           ,$t[swefminer_col_deleters]
                        );
                    }
                    else {
                        $this->tables[$i][swefminer_col_deleters] = array ();
                    }
                }
                else {
                    $this->tables[$i][swefminer_col_deleters] = array ();
                }
                break;
            }
        }
        $meta                   = $this->page->swef->db->dbCall (
            swefminer_call_columns
           ,$this->tables[SWEF_INT_0][swefminer_col_table_schema]
           ,$table_like
        );
        $this->columns          = $this->db->dbCall (
            swefminer_call_model_columns
           ,$this->tables[SWEF_INT_0][swefminer_col_table_schema]
           ,$table_like
        );
        $this->page->diagnosticAdd ('Data model "'.$dm.'": '.$this->db->dbCallLast());
        foreach ($this->columns as $i=>$c) {
            foreach ($meta as $m) {
                if ($m[swefminer_col_database]!=$c[swefminer_col_table_schema]) {
                    continue;
                }
                if ($m[swefminer_col_table]!=$c[swefminer_col_table_name]) {
                    continue;
                }
                if ($m[swefminer_col_column]!=$c[swefminer_col_column_name]) {
                    continue;
                }
                foreach ($m as $f=>$v) {
                    $this->columns[$i][$f] = $v;
                }
                if (array_key_exists(swefminer_col_selectors,$c)) {
                    if (strlen($c[swefminer_col_selectors])) {
                        $this->columns[$i][swefminer_col_selectors] = explode (
                            SWEF_STR__COMMA
                           ,$c[swefminer_col_selectors]
                        );
                    }
                    else {
                        $this->columns[$i][swefminer_col_selectors] = array ();
                    }
                }
                else {
                    $this->columns[$i][swefminer_col_selectors] = array ();
                }
                if (array_key_exists(swefminer_col_updaters,$c)) {
                    if (strlen($c[swefminer_col_updaters])) {
                        $this->columns[$i][swefminer_col_updaters] = explode (
                            SWEF_STR__COMMA
                           ,$c[swefminer_col_updaters]
                        );
                    }
                    else {
                        $this->columns[$i][swefminer_col_updaters] = array ();
                    }
                }
                else {
                    $this->columns[$i][swefminer_col_updaters] = array ();
                }
                break;
            }
        }        
    }

    public function dashboardController ( ) {
        if (!count($_POST)) {
            return;
        }
        if ($this->page->_POST (swefminer_form_couple)) {
            $dbn                = $this->page->_POST (swefminer_form_database);
            $tbn                = $this->page->_POST (swefminer_form_table);
            $tables             = $this->page->swef->db->dbCall (
                swefminer_call_tables,
                $dbn,
                swefminer_str_like_all
            );
            foreach ($tables as $t) {
                if ($t[swefminer_col_database]==$dbn && $t[swefminer_col_table]==$tbn) {
                    $ttl        = $this->page->_POST (swefminer_form_title);
                    $dsc        = $this->page->_POST (swefminer_form_description);
                    if ($ttl && $dsc) {
                        $this->page->swef->db->dbCall (swefminer_call_table_update,$dbn,$tbn,$ttl,$dsc);
                    }
                    else {
                        $this->page->swef->db->dbCall (swefminer_call_table_unremove,$dbn,$tbn);
                    }
                    $this->page->reload ();
                    return;
                }
            }
            $this->page->swef->db->dbCall (swefminer_call_table_create,$dbn,$tbn);
            $this->page->reload ();
            return;
        }
        elseif ($this->page->_POST(swefminer_form_remove)) {
            $dbn                = $this->page->_POST (swefminer_form_database);
            $tbn                = $this->page->_POST (swefminer_form_table);
            $this->page->swef->db->dbCall (swefminer_call_table_remove,$dbn,$tbn);
            $this->page->reload ();
            return;
        }
        elseif ($this->page->_POST(swefminer_form_table_usergroup)) {
            $d          = $this->page->_GET (SWEF_GET_OPTION);
            $t          = $this->page->_POST (swefminer_form_table);
            $ug         = $this->page->_POST (swefminer_form_usergroup);
            $ins        = SWEF_INT_0;
            $del        = SWEF_INT_0;
            if ($this->page->_POST(swefminer_form_inserter)) {
                $ins    = SWEF_INT_1;
            }
            if ($this->page->_POST(swefminer_form_deleter)) {
                $del    = SWEF_INT_1;
            }
            $this->notify (print_r($_POST,true));
            $this->notify ("TABLE/USERGROUP: ".$d.','.$t.','.$ug.','.$ins.','.$del);
            $this->page->reload ();
            return;
        }
        elseif ($this->page->_POST(swefminer_form_table_update)) {
            $d          = $this->page->_GET (SWEF_GET_OPTION);
            $t          = $this->page->_POST (swefminer_form_table);
            $this->notify ('TABLE/UPDATE: '.$d.','.$t.','.print_r($_POST,true));
            $this->page->reload ();
            return;
        }
        elseif ($this->page->_POST(swefminer_form_column_update)) {


            $dbn                = $this->page->_POST (swefminer_form_database);
            $tbn                = $this->page->_POST (swefminer_form_table);
            $cln                = $this->page->_POST (swefminer_form_column);
            $columns            = $this->page->swef->db->dbCall (
                swefminer_call_tables,
                $dbn,
                swefminer_str_like_all
            );
            foreach ($columns as $c) {
                if ($c[swefminer_col_database]==$dbn && $c[swefminer_col_table]==$tbn && $c[swefminer_col_column]==$cln) {
                    if (array_key_exists(swefminer_form_heading,$_POST)) {
                        $hdg    = $this->page->_POST (swefminer_form_heading);
                        $hnt    = $this->page->_POST (swefminer_form_hint);
                        $this->page->swef->db->dbCall (swefminer_call_column_update,$dbn,$tbn,$cln,$hdg,$hnt);
                    }
                    else {
                        $this->page->swef->db->dbCall (swefminer_call_column_unremove,$dbn,$tbn,$cln);
                    }
                    $this->page->reload ();
                    return;
                }
            }
            $this->page->swef->db->dbCall (swefminer_call_column_create,$dbn,$tbn,$cln);
            $this->page->reload ();
            return;
        }
        elseif ($this->page->_POST(swefminer_form_usergroup_table)) {
            $ug         = $this->page->_GET (swefminer_get_usergroup);
            $d          = $this->page->_GET (SWEF_GET_OPTION);
            $t          = $this->page->_POST (swefminer_form_table);
            $ins        = SWEF_INT_0;
            $del        = SWEF_INT_0;
            if ($this->page->_POST(swefminer_form_inserter)) {
                $ins    = SWEF_INT_1;
            }
            if ($this->page->_POST(swefminer_form_deleter)) {
                $del    = SWEF_INT_1;
            }
            $this->notify ('USERGROUP/TABLE: '.$ug.','.$d.','.$t.','.$ins.','.$del);
            $this->page->reload ();
            return;
        }
        elseif ($this->page->_POST(swefminer_form_usergroup_column)) {
            $ug         = $this->page->_GET (swefminer_get_usergroup);
            $d          = $this->page->_GET (SWEF_GET_OPTION);
            $t          = $this->page->_POST (swefminer_form_table);
            $c          = $this->page->_POST (swefminer_form_column);
            $ins        = SWEF_INT_0;
            $del        = SWEF_INT_0;
            if ($this->page->_POST(swefminer_form_inserter)) {
                $ins    = SWEF_INT_1;
            }
            if ($this->page->_POST(swefminer_form_deleter)) {
                $del    = SWEF_INT_1;
            }
            $this->notify ("USERGROUP/COLUMN: ".$ug.','.$d.','.$t.','.$c.','.$ins.','.$del);
            $this->page->reload ();
            return;
        }
        $this->notify ("Sorry - that posted data did not make sense");
        $this->page->reload ();
    }

    public function formID ( ) {
        return swefminer_formid_pfx.implode(swefminer_formid_sep,func_get_args());
    }

}

?>
