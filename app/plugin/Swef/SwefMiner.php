<?php

namespace Swef;

class SwefMiner extends \Swef\Bespoke\Plugin {


/*
    PROPERTIES
*/

    public  $browsingDashboard;
    public  $browse;
    public  $columns                = array (); // Columns available
    public  $displayFields          = array (); // Fields to represent joined data
    public  $entities               = array (); // Tables/entities visible to the current usergroup
    public  $isJoined;
    public  $foreigns               = array (); // Fields that are foreign keys
    public  $isOrder1;
    public  $isOrder2;
    public  $isParent;
    public  $joins                  = array ();
    public  $model;
    public  $models                 = array ();
    public  $properties             = array (); // Columns/properties of current table visible to the current usergroup
    public  $request;
    public  $rows                   = array ();
    public  $supportedPDODrivers    = array ();
    public  $tables                 = array (); // Tables available to model
    public  $trashField;


/*
    EVENT HANDLER SECTION
*/

    public function __construct ($page) {
        // Always construct the base class - PHP does not do this implicitly
        parent::__construct ($page,'\Swef\SwefMiner');
        $this->supportedPDODrivers  = explode (SWEF_STR__COMMA,swefminer_supported_pdo_drivers);
    }

    public function __destruct ( ) {
        // Always destruct the base class - PHP does not do this implicitly
        parent::__destruct ( );
    }

    public function _on_pageIdentifyBefore ( ) {
        if (preg_match(swefminer_dashboard_context_preg,$this->page->swef->context[SWEF_COL_CONTEXT])) {
            if (!strlen(swefminer_uris_dashboard)) {
                return SWEF_BOOL_TRUE;
            }
            $models                 = explode (swefminer_str_sep_pairs,swefminer_uris_dashboard);
        }
        else {
            if (!strlen(swefminer_uris_public)) {
                return SWEF_BOOL_TRUE;
            }
            $models                 = explode (swefminer_str_sep_pairs,swefminer_uris_public);
        }
        foreach ($models as $m) {
            $m = explode (swefminer_str_sep_keyval,$m);
            if ($m[1]==$this->page->requestPath) {
                $this->model        = $m[0];
                break;
            }
        }
        if (!$this->model) {
            return SWEF_BOOL_TRUE;
        }
        $this->page->endpoint       = $this->page->swef->context[SWEF_COL_HOME];
        $this->page->identify ();
        $this->isParent             = SWEF_BOOL_TRUE;
        return SWEF_BOOL_FALSE;
    }

    public function _on_pageScriptBefore ( ) {
        if (!$this->model) {
            return SWEF_BOOL_TRUE;
        }
        $this->_init ($this->model);
        $this->structure ($this->page->_GET(swefminer_get_table));
        require_once swefminer_file_browse;
        return SWEF_BOOL_FALSE;
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

    private function _init ($dm=null) {
        $constants                  = get_defined_constants (SWEF_BOOL_TRUE) [swefminer_col_user];
        $len                        = strlen (swefminer_str_pfx_dm);
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
            $model                  = substr ($c,0,(SWEF_INT_0-strlen(swefminer_str_sfx_dsn)));
            $dsn                    = constant (swefminer_str_pfx_dm.$c);
            if (!defined(swefminer_str_pfx_dm.$model.swefminer_str_sfx_dbn)) {
                $this->notify ('Data model "'.$model.'" has no database name - define '.swefminer_str_pfx_dm.$model.swefminer_str_sfx_dbn);
                return;
            }
            $dbn                    = constant (swefminer_str_pfx_dm.$model.swefminer_str_sfx_dbn);
            if (!defined(swefminer_str_pfx_dm.$model.swefminer_str_sfx_usr)) {
                $this->notify ('Data model "'.$model.'" has no database user - define '.swefminer_str_pfx_dm.$model.swefminer_str_sfx_usr);
                return;
            }
            if (!defined(swefminer_str_pfx_dm.$model.swefminer_str_sfx_pwd)) {
                $this->notify ('Data model "'.$model.'" has no database password - define '.swefminer_str_pfx_dm.$model.swefminer_str_sfx_pwd);
                return;
            }
            if (!defined(swefminer_str_pfx_dm.$model.swefminer_str_sfx_tag)) {
                $this->notify ('Data model "'.$model.'" has no descriptive tag - define '.swefminer_str_pfx_dm.$model.swefminer_str_sfx_tag);
                return;
            }
            $this->models[$model]   = constant (swefminer_str_pfx_dm.$model.swefminer_str_sfx_tag);
        }
        if (!$dm) {
            $dm                     = $this->page->_GET(SWEF_GET_OPTION);
        }
        if (!array_key_exists($dm,$this->models)) {
            return;
        }
        if (defined(swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tlk)) {
            $table_like             = constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_tlk);
        }
        else {
            $table_like             = swefminer_str_like_all;
        }
        $dsn                        = constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dsn);
        $dsn                       .= ';dbname=';
        $dsn                       .= constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_dbn);
        $this->db                   = new \Swef\Bespoke\Database (
            $dsn,
            constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_usr),
            constant (swefminer_str_pfx_dm.$dm.swefminer_str_sfx_pwd)
        );
        $this->tables               = $this->db->dbCall (
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
        $meta                       = $this->page->swef->db->dbCall (
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
                break;
            }
        }
        foreach ($this->tables as $i=>$t) {
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
        }
        $meta                       = $this->page->swef->db->dbCall (
            swefminer_call_columns
           ,$this->tables[SWEF_INT_0][swefminer_col_table_schema]
           ,$table_like
        );
        $this->columns              = $this->db->dbCall (
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
                break;
            }
        }
        foreach ($this->columns as $i=>$c) {
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
        }        
    }

    private function columnPermissionsUpdate ( ) {
        foreach ($this->page->swef->usergroups as $ug) {
            if ($this->page->_POST(swefminer_form_selector_pfx.$ug[SWEF_COL_USERGROUP])) {
                $this->page->swef->db->dbCall (
                    swefminer_call_select_allow,
                    $this->page->_POST (swefminer_form_database),
                    $this->page->_POST (swefminer_form_table),
                    $this->page->_POST (swefminer_form_column),
                    $ug[SWEF_COL_USERGROUP]
                );
            }
            else {
                $this->page->swef->db->dbCall (
                    swefminer_call_select_deny,
                    $this->page->_POST (swefminer_form_database),
                    $this->page->_POST (swefminer_form_table),
                    $this->page->_POST (swefminer_form_column),
                    $ug[SWEF_COL_USERGROUP]
                );
            }
            if ($this->page->_POST(swefminer_form_updater_pfx.$ug[SWEF_COL_USERGROUP])) {
                $this->page->swef->db->dbCall (
                    swefminer_call_update_allow,
                    $this->page->_POST (swefminer_form_database),
                    $this->page->_POST (swefminer_form_table),
                    $this->page->_POST (swefminer_form_column),
                    $ug[SWEF_COL_USERGROUP]
                );
            }
            else {
                $this->page->swef->db->dbCall (
                    swefminer_call_update_deny,
                    $this->page->_POST (swefminer_form_database),
                    $this->page->_POST (swefminer_form_table),
                    $this->page->_POST (swefminer_form_column),
                    $ug[SWEF_COL_USERGROUP]
                );
            }
        }
    }

    private function columnSelectUpdate ( ) {
        if ($this->page->_POST(swefminer_form_select)) {
            $this->page->swef->db->dbCall (
                swefminer_call_select_allow,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_column),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
        else {
            $this->page->swef->db->dbCall (
                swefminer_call_select_deny,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_column),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
    }

    private function columnUpdateUpdate ( ) {
        if ($this->page->_POST(swefminer_form_update)) {
            $this->page->swef->db->dbCall (
                swefminer_call_update_allow,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_column),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
        else {
            $this->page->swef->db->dbCall (
                swefminer_call_update_deny,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_column),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
    }

    private function columnUpdate ( ) {
        $dbn                        = $this->page->_POST (swefminer_form_database);
        $tbn                        = $this->page->_POST (swefminer_form_table);
        $cln                        = $this->page->_POST (swefminer_form_column);
        $columns                    = $this->page->swef->db->dbCall (
            swefminer_call_columns,
            $dbn,
            swefminer_str_like_all
        );
        foreach ($columns as $c) {
            if ($c[swefminer_col_database]==$dbn && $c[swefminer_col_table]==$tbn && $c[swefminer_col_column]==$cln) {
                if (array_key_exists(swefminer_form_column_remove,$_POST)) {
                    $this->page->swef->db->dbCall (swefminer_call_column_remove,$dbn,$tbn,$cln);
                }
                elseif (array_key_exists(swefminer_form_column_unremove,$_POST)) {
                    $this->page->swef->db->dbCall (swefminer_call_column_unremove,$dbn,$tbn,$cln);
                }
                else {
                    $this->page->swef->db->dbCall (
                        swefminer_call_column_update,
                        $dbn,
                        $tbn,
                        $cln,
                        $this->page->_POST (swefminer_form_heading),
                        $this->page->_POST (swefminer_form_hint),
                        $this->postedDbBoolean (swefminer_col_trashes_record),
                        $this->postedDbBoolean (swefminer_col_is_uuid),
                        $this->postedDbBoolean (swefminer_col_is_edited_by),
                        $this->postedDbBoolean (swefminer_col_is_edited),
                        $this->postedDbBoolean (swefminer_col_describes_record)
                    );
                }
                return;
            }
        }
        $this->page->swef->db->dbCall (swefminer_call_column_create,$dbn,$tbn,$cln);
    }

    private function dashboardController ( ) {
        if (!count($_POST)) {
            return;
        }
        if ($this->page->_POST(swefminer_form_select_update)) {
            $this->columnSelectUpdate ();
        }
        elseif ($this->page->_POST(swefminer_form_update_update)) {
            $this->columnUpdateUpdate ();
        }
        elseif ($this->page->_POST(swefminer_form_delete_update)) {
            $this->tableDeleteUpdate ();
        }
        elseif ($this->page->_POST(swefminer_form_insert_update)) {
            $this->tableInsertUpdate ();
        }
        elseif ($this->page->_POST (swefminer_form_table_couple)) {
            $this->tableUpdate ();
        }
        elseif ($this->page->_POST(swefminer_form_table_update)) {
            $this->tableUpdate ();            
        }
        elseif ($this->page->_POST(swefminer_form_table_decouple)) {
            $this->tableDecouple ();
        }
        elseif ($this->page->_POST(swefminer_form_column_update)) {
            $this->columnUpdate ();
            $this->columnPermissionsUpdate ();
        }
        else {
            $this->notify ("Sorry - that posted data did not make sense");
        }
        $this->page->reload ();
    }

    public function formID ( ) {
        return swefminer_formid_pfx.implode(swefminer_formid_sep,func_get_args());
    }

    public function postedDbBoolean ($key) {
        return intval (strlen($this->page->_POST($key))>SWEF_INT_0);
    }

    private function structure ($table_name) {
        $usergroup                  = null;
        foreach ($this->page->swef->user->memberships as $m) {
            if ($m[SWEF_COL_USERGROUP]==$this->page->_GET(swefminer_get_usergroup)) {
                $usergroup          = $m[SWEF_COL_USERGROUP];
            }
        }
        if (!$usergroup) {
            return;
        }
        foreach ($this->tables as $t) {
            if (!array_key_exists(swefminer_col_ignore,$t) || $t[swefminer_col_ignore]) {
                continue;
            }
            array_push ($this->entities,$t);
            if ($t[swefminer_col_table_name]!=$table_name) {
                continue;
            }
            foreach ($this->columns as $c) {
                if ($c[swefminer_col_table_schema]!=$t[swefminer_col_table_schema]) {
                    continue;
                }
                if ($c[swefminer_col_table_name]!=$t[swefminer_col_table_name]) {
                    continue;
                }
                if (!array_key_exists(swefminer_col_ignore,$c) || $c[swefminer_col_ignore]) {
                    continue;
                }
                if ($c[swefminer_col_trashes_record]) {
                    $this->trashField = $c;
                }
                if ($c[swefminer_col_describes_record]) {
                    array_push ($this->displayFields,$c);
                }
                if (strlen($c[swefminer_col_ref_table_name])) {
                    array_push ($this->foreigns,$c);
                }
                if (in_array($usergroup,$c[swefminer_col_selectors])) {
                    array_push ($this->properties,$c);
                }
            }
            if (!$this->isParent && count($this->displayFields)) {
                // This is not the table in focus and has at least one display field
                continue;
            }
            if (!$this->isOrder1 && !$this->isOrder2) {
                // This table is too distantly related to keep joining
                continue;
            }
            foreach ($this->foreigns as $f) {
                $join               = new \Swef\SwefMiner ($this->page);
                if ($this->isParent) {
                    $join->isOrder1 = SWEF_BOOL_TRUE;
                }
                elseif ($this->isOrder1) {
                    $join->isOrder2 = SWEF_BOOL_TRUE;
                }
                $join->_init ($this->model);
                $join->structure ($f[swefminer_col_ref_table_name]);
                array_push ($this->joins,$join);
            }
        }
    }

    private function tableInsertUpdate ($ug) {
        if ($this->page->_POST(swefminer_form_insert)) {
            $this->page->swef->db->dbCall (
                swefminer_call_insert_allow,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
        else {
            $this->page->swef->db->dbCall (
                swefminer_call_insert_deny,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
    }

    private function tableDeleteUpdate ($ug) {
        if ($this->page->_POST(swefminer_form_delete)) {
            $this->page->swef->db->dbCall (
                swefminer_call_delete_allow,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
        else {
            $this->page->swef->db->dbCall (
                swefminer_call_delete_deny,
                $this->page->_POST (swefminer_form_database),
                $this->page->_POST (swefminer_form_table),
                $this->page->_POST (swefminer_form_usergroup)
            );
        }
    }

    private function tableDecouple ( ) {
        $dbn                        = $this->page->_POST (swefminer_form_database);
        $tbn                        = $this->page->_POST (swefminer_form_table);
        $this->page->swef->db->dbCall (swefminer_call_table_remove,$dbn,$tbn);
    }

    private function tableUpdate ( ) {
        $dbn                        = $this->page->_POST (swefminer_form_database);
        $tbn                        = $this->page->_POST (swefminer_form_table);
        $tables                     = $this->page->swef->db->dbCall (
            swefminer_call_tables,
            $dbn,
            swefminer_str_like_all
        );
        foreach ($tables as $t) {
            if ($t[swefminer_col_database]==$dbn && $t[swefminer_col_table]==$tbn) {
                $ttl                = $this->page->_POST (swefminer_form_title);
                $dsc                = $this->page->_POST (swefminer_form_description);
                if ($ttl && $dsc) {
                    $this->page->swef->db->dbCall (swefminer_call_table_update,$dbn,$tbn,$ttl,$dsc);
                }
                else {
                    $this->page->swef->db->dbCall (swefminer_call_table_unremove,$dbn,$tbn);
                }
                return;
            }
        }
        $this->page->swef->db->dbCall (swefminer_call_table_create,$dbn,$tbn);
    }

}

?>
