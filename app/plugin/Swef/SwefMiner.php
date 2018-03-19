<?php

namespace Swef;

class SwefMiner extends \Swef\Bespoke\Plugin {


/*
    PROPERTIES
*/

    public  $browse;
    public  $columns                = array ();
    public  $dbs                    = array ();
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
        $constants              = get_defined_constants (SWEF_BOOL_TRUE) [swefminer_col_user];
        $meta                   = $this->page->swef->db->dbCall (swefminer_call_tables);
        foreach ($constants as $c=>$dsn) {
            if (strpos($c,swefminer_db_const_pfx_dsn)!==SWEF_INT_0) {
                continue;
            }
            $dbn = substr ($c,strlen(swefminer_db_const_pfx_dsn));
            if (!defined(swefminer_db_const_pfx_usr.$dbn)) {
                $this->notify ('Database "'.$dbn.'" has no database user - define '.swefminer_db_const_pfx_usr.$dbn);
                continue;
            }
            if (!defined(swefminer_db_const_pfx_pwd.$dbn)) {
                $this->notify ('Database "'.$dbn.'" has no database password - define '.swefminer_db_const_pfx_pwd.$dbn);
                continue;
            }
            $this->dbs[$dbn]    = new \Swef\Bespoke\Database (
                $dsn
               ,constant (swefminer_db_const_pfx_usr.$dbn)
               ,constant (swefminer_db_const_pfx_pwd.$dbn)
            );
            $this->tables[$dbn] = $this->dbs[$dbn]->dbCall (swefminer_call_model_tables,$dbn);
            $this->page->diagnosticAdd ('Database "'.$dbn.'": '.$this->dbs[$dbn]->dbCallLast());
            foreach ($this->tables[$dbn] as $i=>$t) {
                foreach ($meta as $m) {
                    if ($m[swefminer_col_database]!=$dbn) {
                        continue;
                    }
                    if ($m[swefminer_col_table]!=$t[swefminer_col_table_name]) {
                        continue;
                    }
                    foreach ($m as $f=>$v) {
                        $this->tables[$dbn][$i][$f] = $v;
                    }
                    if (array_key_exists(swefminer_col_inserters,$this->tables[$dbn][$i])) {
                        $this->tables[$dbn][$i][swefminer_col_inserters] = explode (
                            SWEF_STR__COMMA
                           ,$this->tables[$dbn][$i][swefminer_col_inserters]
                        );
                    }
                    if (array_key_exists(swefminer_col_deleters,$this->tables[$dbn][$i])) {
                        $this->tables[$dbn][$i][swefminer_col_deleters] = explode (
                            SWEF_STR__COMMA
                           ,$this->tables[$dbn][$i][swefminer_col_deleters]
                        );
                    }
                    break;
                }
            }
        }
        if (!($dbn=$this->page->_GET(SWEF_GET_OPTION))) {
            return;
        }
        $meta                   = $this->page->swef->db->dbCall (swefminer_call_columns,$dbn);
        $this->columns          = $this->dbs[$dbn]->dbCall (swefminer_call_model_columns,$dbn);
        $this->page->diagnosticAdd ('Database "'.$dbn.'": '.$this->dbs[$dbn]->dbCallLast());
        foreach ($this->columns as $i=>$c) {
            foreach ($meta as $m) {
                if ($m[swefminer_col_database]!=$dbn) {
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
                if (array_key_exists(swefminer_col_selectors,$this->columns[$i])) {
                    $this->columns[$i][swefminer_col_selectors] = explode (
                        SWEF_STR__COMMA
                       ,$this->columns[$i][swefminer_col_selectors]
                    );
                }
                if (array_key_exists(swefminer_col_updaters,$this->columns[$i])) {
                    $this->columns[$i][swefminer_col_updaters] = explode (
                        SWEF_STR__COMMA
                       ,$this->columns[$i][swefminer_col_updaters]
                    );
                }
                break;
            }
        }        
    }

    public function dashboardController ( ) {
        if ($this->page->_POST (swefminer_form_update)) {
            $dbn                = $this->page->_POST (swefminer_form_database);
            $tbn                = $this->page->_POST (swefminer_form_table);
            $tables             = $this->page->swef->db->dbCall (swefminer_call_tables);
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
        if ($this->page->_POST (swefminer_form_remove)) {
            $dbn                = $this->page->_POST (swefminer_form_database);
            $tbn                = $this->page->_POST (swefminer_form_table);
            $this->page->swef->db->dbCall (swefminer_call_table_remove,$dbn,$tbn);
            $this->page->reload ();
            return;
        }
    }

/*
    public function columnsLoad ( ) {
        if ($this->columns) {
            return SWEF_BOOL_TRUE;
        }
        $this->columns  = $this->page->swef->lookupLoad (
            swefminer_vendor
           ,swefminer_columns_lookup
           ,swefminer_call_columns
        );
        if (!is_array($this->columns) || !count($this->columns)) {
            $this->notify ('Could not load column data (or there was no data)');
            return SWEF_BOOL_FALSE;
        }
        foreach ($this->columns as $column) {
            if (!in_array($column[swefminer_col_table],$this->tables)) {
                $this->tables[$column[swefminer_col_table]] = array (
                    swefminer_col_set           => $column[swefminer_col_set]
                   ,swefminer_col_read_only     => $column[swefminer_col_read_only]
                   ,swefminer_col_table         => $column[swefminer_col_table]
                   ,swefminer_col_title         => $column[swefminer_col_title]
                   ,swefminer_col_description   => $column[swefminer_col_description]
                );
            }
            if (!in_array($column[swefminer_col_set],$this->sets)) {
                $this->sets[$column[swefminer_col_set]] = array (
                    swefminer_col_set           => $column[swefminer_col_set]
                   ,swefminer_col_read_only     => $column[swefminer_col_read_only]
                );
            }
        }
        return SWEF_BOOL_TRUE;
    }

    public function columnsChildren ($column) {
        $cs                     = array ();
        foreach ($this->columns as $c) {
            if ($c[swefminer_col_set]!=$column[swefminer_col_set]) {
                continue;
            }
            if ($c[swefminer_col_parent_table]!=$column[swefminer_col_table]) {
                continue;
            }
            if ($c[swefminer_col_parent_column]!=$column[swefminer_col_column]) {
                continue;
            }
            array_push ($cs,$c);
            $this->joins[$c[swefminer_col_table]] = array ();
        }
        $children               = array ();
        foreach ($cs as $c) {
            $cols               = $this->columnsSelf ($c[swefminer_col_table]);
            foreach ($cols as $col) {
                if ($col[swefminer_col_describes_record]) {
                    array_push ($children,$col);
                    continue;
                }
                if ($col[swefminer_col_set]==$column[swefminer_col_set]) {
                    if ($col[swefminer_col_parent_table]==$column[swefminer_col_table]) {
                        if ($col[swefminer_col_parent_column]==$column[swefminer_col_column]) {
                            $col[swefminer_col_is_index] = SWEF_BOOL_TRUE;
                            array_push ($children,$col);
                            array_push ($this->joins[$col[swefminer_col_table]],$col);
                        }
                    }
                }
            }
        }
        return $children;
    }

    public function columnsDisplay ($table) {
        $columns                                = array ();
        foreach ($this->columnsSelf($table) as $cs) {
            if ($cs[swefminer_col_parent_table]) {
                $this->joins[$cs[swefminer_col_parent_table]] = array ();
            }
            foreach ($this->columnsParent($cs) as $cp) {
                $cp[swefminer_col_is_parent]    = SWEF_BOOL_TRUE;
                array_push ($columns,$cp);
            }
            $cs[swefminer_col_is_self]          = SWEF_BOOL_TRUE;
            array_push ($columns,$cs);
            foreach ($this->columnsChildren($cs) as $cc) {
                $cc[swefminer_col_is_child]     = SWEF_BOOL_TRUE;
                array_push ($columns,$cc);
            }
        }
        return $columns;
    }

    public function columnsSelf ($table) {
        $cs = array ();
        if (!$table) {
            return $cs;
        }
        foreach ($this->columns as $c) {
            if ($c[swefminer_col_table]!=$table) {
                continue;
            }
            array_push ($cs,$c);
        }
        return $cs;
    }

    public function columnsParent ($column) {
        $cs                     = array ();
        $columns                = $this->columnsSelf ($column[swefminer_col_parent_table]);
        foreach ($columns as $c) {
            if ($c[swefminer_col_describes_record]) {
                array_push ($cs,$c);
                continue;
            }
            if ($c[swefminer_col_column]==$column[swefminer_col_parent_column]) {
                $c[swefminer_col_is_index] = SWEF_BOOL_TRUE;
                array_push ($this->joins[$c[swefminer_col_table]],$c);
                array_push ($cs,$c);
                continue;
            }
        }
        return $cs;
    }

    public function select ($columns) {
        $selects                = array ();
        foreach ($columns as $c) {
            if (array_key_exists(swefminer_col_is_self,$c)) {
                $table          = $c[swefminer_col_table];
            }
            if (array_key_exists(swefminer_col_is_child,$c)) {
                $select         = "GROUP_CONCAT(";
                $select        .= $c[swefminer_col_table].SWEF_STR__DOT.$c[swefminer_col_column];
//                $select        .= " ORDER BY ".implode(SWEF_STR__COMMA,$order_by);
                $select        .= " SEPARATOR '".swefminer_concat_separator."'";
                $select        .= ') AS '.$c[swefminer_col_column];
            }
            else {
                $select         = $c[swefminer_col_table].SWEF_STR__DOT.$c[swefminer_col_column];
            }
            array_push ($selects,$select);
        }
        $select                 = swefminer_str_select.SWEF_STR__CRLF;
        $select                .= swefminer_str_indent.SWEF_STR__SPACE;
        $select                .= implode (SWEF_STR__CRLF.swefminer_str_indent.SWEF_STR__COMMA,$selects);
        $select                .= SWEF_STR__CRLF;
        $select                .= swefminer_str_from.SWEF_STR__CRLF;
        $select                .= swefminer_str_indent.SWEF_STR__SPACE.$table.SWEF_STR__CRLF;
        return $select;
    }

    public function modelScan ( ) {
        if (!preg_match(swefminer_preg_table,$table)) {
            $this->notify ('Table name is not allowed - see swefminer_preg_table');
            return SWEF_BOOL_FALSE;
        }
        if (!in_array($this->db->dbPDOAttribute(PDO::ATTR_DRIVER_NAME),$this->supportedPDODrivers)) {
            $this->notify ('SwefMiner::describe() does not currently support this PDO driver');
            return SWEF_BOOL_FALSE;
        }
        if ($this->db->dbPDOAttribute(PDO::ATTR_DRIVER_NAME)==swefminer_pdo_driver_mysql) {
            // MySQL / MariaDB specific table information
            $q          = 'SHOW CREATE TABLE `'.$table.'`';
            $describe   = $this->db->dbQuery ($q);
            return $describe[SWEF_INT_0][SWEF_INT_1];
        }
        return SWEF_BOOL_FALSE;
    }

    public function tablesScan ($dbname) {
        $mod                    = SWEF_BOOL_FALSE;
        if (defined(swefminer_db_const_pfx_mod.$dbname) && constant(swefminer_db_const_pfx_mod.$dbname)) {
            $mod                = SWEF_BOOL_TRUE;
        }
        $tag                    = SWEF_STR__EMPTY;
        if (defined(swefminer_db_const_pfx_tag.$dbname)) {
            $tag                = constant (swefminer_db_const_pfx_tag.$dbname);
        }
        if (!$tag) {
            $tag                = $dbname;
        }
        $tables                 = $this->dbs[$dbname]->dbCall (swefminer_call_model_tables,$dbname);
        $this->page->diagnosticAdd ('Database "'.$dbname.'": '.$this->dbs[$dbname]->dbCallLast());
        return $tables;
    }

swefminer_col_column
swefminer_col_table
swefminer_col_set
swefminer_col_read_only
swefminer_col_description
swefminer_col_is_uuid
swefminer_col_parent_table
swefminer_col_parent_column
swefminer_col_describes_record
swefminer_col_heading
swefminer_col_hint
*/

}

?>
