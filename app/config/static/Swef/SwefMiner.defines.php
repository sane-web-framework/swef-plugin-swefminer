<?php


// Data navigation
define ( 'swefminer_limit_rows',             50                                                     );


// Form field names
define ( 'swefminer_form_column',            'swefminer-column'                                     );
define ( 'swefminer_form_column_pfx',        'swefminer-column-'                                    );
define ( 'swefminer_form_column_update',     'swefminer-column-update'                              );
define ( 'swefminer_form_columns',           'swefminer-columns'                                    );
define ( 'swefminer_form_database',          'swefminer-database'                                   );
define ( 'swefminer_form_deleter',           'swefminer-deleter'                                    );
define ( 'swefminer_form_deleter_pfx',       'swefminer-deleter-'                                   );
define ( 'swefminer_form_description',       'swefminer-description'                                );
define ( 'swefminer_form_inserter',          'swefminer-inserter'                                   );
define ( 'swefminer_form_inserter_pfx',      'swefminer-inserter-'                                  );
define ( 'swefminer_form_pfx',               'swefminer-form-'                                      );
define ( 'swefminer_form_posted',            'swefminer-posted'                                     );
define ( 'swefminer_form_remove',            'swefminer-remove'                                     );
define ( 'swefminer_form_selector',          'swefminer-selector'                                   );
define ( 'swefminer_form_selector_pfx',      'swefminer-selector-'                                  );
define ( 'swefminer_form_table',             'swefminer-table'                                      );
define ( 'swefminer_form_table_pfx',         'swefminer-table-'                                     );
define ( 'swefminer_form_table_update',      'swefminer-table-update'                               );
define ( 'swefminer_form_table_usergroup',   'swefminer-table-usergroup'                            );
define ( 'swefminer_form_title',             'swefminer-title'                                      );
define ( 'swefminer_form_update',            'swefminer-update'                                     );
define ( 'swefminer_form_updater',           'swefminer-updater'                                    );
define ( 'swefminer_form_updater_pfx',       'swefminer-updater-'                                   );
define ( 'swefminer_form_usergroup_column',  'swefminer-usergroup-column'                           );
define ( 'swefminer_form_usergroup_table',   'swefminer-usergroup-table'                            );


// Files
define ( 'swefminer_file_dash',              SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.html'            );
define ( 'swefminer_file_columns',           SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.columns.html'    );
define ( 'swefminer_file_column',            SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.column.html'     );
define ( 'swefminer_file_column_perms',      SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.cperms.html'     );
define ( 'swefminer_file_info',              SWEF_DIR_PLUGIN.'/Swef/SwefMiner.info.html'            );
define ( 'swefminer_file_table_perms',       SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.tperms.html'     );
define ( 'swefminer_file_tables',            SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.tables.html'     );
define ( 'swefminer_file_usergroups',        SWEF_DIR_PLUGIN.'/Swef/SwefMiner.dash.usergroups.html' );


// Stored procedures
define ( 'swefminer_call_model_columns',     'swefMinerModelColumns'                                );
define ( 'swefminer_call_model_referers',    'swefMinerModelReferers'                               );
define ( 'swefminer_call_model_tables',      'swefMinerModelTables'                                 );
define ( 'swefminer_call_columns',           'swefMinerColumns'                                     );
define ( 'swefminer_call_table_create',      'swefMinerTableCreate'                                 );
define ( 'swefminer_call_table_remove',      'swefMinerTableRemove'                                 );
define ( 'swefminer_call_table_unremove',    'swefMinerTableUnremove'                               );
define ( 'swefminer_call_table_update',      'swefMinerTableUpdate'                                 );
define ( 'swefminer_call_tables',            'swefMinerTables'                                      );


// Standard columns
define ( 'swefminer_col_column',             'column'                                               );
define ( 'swefminer_col_column_key',         'COLUMN_KEY'                                           );
define ( 'swefminer_col_column_name',        'COLUMN_NAME'                                          );
define ( 'swefminer_col_database',           'database'                                             );
define ( 'swefminer_col_deleters',           'deleters'                                             );
define ( 'swefminer_col_describes_record',   'describes_record'                                     );
define ( 'swefminer_col_description',        'description'                                          );
define ( 'swefminer_col_dsn',                'dsn'                                                  );
define ( 'swefminer_col_heading',            'heading'                                              );
define ( 'swefminer_col_hint',               'hint'                                                 );
define ( 'swefminer_col_ignore',             'ignore'                                               );
define ( 'swefminer_col_indexes',            'indexes'                                              );
define ( 'swefminer_col_inserters',          'inserters'                                            );
define ( 'swefminer_col_is_child',           'is_child'                                             );
define ( 'swefminer_col_is_edited',          'is_edited'                                            );
define ( 'swefminer_col_is_edited_by',       'is_edited_by'                                         );
define ( 'swefminer_col_is_index',           'is_index'                                             );
define ( 'swefminer_col_is_self',            'is_self'                                              );
define ( 'swefminer_col_is_uuid',            'is_uuid'                                              );
define ( 'swefminer_col_is_parent',          'is_parent'                                            );
define ( 'swefminer_col_trashes_record',     'trashes_record'                                       );
define ( 'swefminer_col_mod',                'may_modify'                                           );
define ( 'swefminer_col_primary_key',        'PRIMARY_KEY'                                          );
define ( 'swefminer_col_ref_column_name',    'REFERENCED_COLUMN_NAME'                               );
define ( 'swefminer_col_ref_table_name',     'REFERENCED_TABLE_NAME'                                );
define ( 'swefminer_col_ref_table_schema',   'REFERENCED_TABLE_SCHEMA'                              );
define ( 'swefminer_col_table_schema',       'TABLE_SCHEMA'                                         );
define ( 'swefminer_col_table_name',         'TABLE_NAME'                                           );
define ( 'swefminer_col_parent_column',      'parent_column'                                        );
define ( 'swefminer_col_parent_table',       'parent_table'                                         );
define ( 'swefminer_col_pwd',                'pwd'                                                  );
define ( 'swefminer_col_selectors',          'selectors'                                            );
define ( 'swefminer_col_table',              'table'                                                );
define ( 'swefminer_col_tag',                'db_tagline'                                           );
define ( 'swefminer_col_title',              'title'                                                );
define ( 'swefminer_col_updaters',           'updaters'                                             );
define ( 'swefminer_col_user',               'user'                                                 );
define ( 'swefminer_col_usr',                'usr'                                                  );


// Other tokens
define ( 'swefminer_get_table',              't'                                                    );
define ( 'swefminer_get_usergroup',          'ug'                                                   );
define ( 'swefminer_vendor',                 SWEF_VENDOR_SWEF                                       );
define ( 'swefminer_columns_lookup',         'swefminer-columns'                                    );
define ( 'swefminer_concat_separator',       ';;'                                                   );
define ( 'swefminer_formid_pfx',             'swefminer-form-'                                      );
define ( 'swefminer_formid_sep',             '-'                                                    );
define ( 'swefminer_pdo_support',            '<^[A-z0-9_]+$>'                                       );
define ( 'swefminer_preg_table',             '<^[A-z0-9_]+$>'                                       );
define ( 'swefminer_str_indent',             '   '                                                  );
define ( 'swefminer_str_like_all',           '%'                                                    );
define ( 'swefminer_str_select',             'SELECT'                                               );
define ( 'swefminer_str_from',               'FROM'                                                 );
define ( 'swefminer_str_left_join',          'LEFT JOIN'                                            );
define ( 'swefminer_str_pfx_dm',             'swefminer_dm_'                                        );
define ( 'swefminer_str_pri',                'PRI'                                                  );
define ( 'swefminer_str_sfx_dbn',            '_dbn'                                                 );
define ( 'swefminer_str_sfx_dsn',            '_dsn'                                                 );
define ( 'swefminer_str_sfx_usr',            '_usr'                                                 );
define ( 'swefminer_str_sfx_pwd',            '_pwd'                                                 );
define ( 'swefminer_str_sfx_mod',            '_mod'                                                 );
define ( 'swefminer_str_sfx_tag',            '_tag'                                                 );
define ( 'swefminer_str_sfx_tlk',            '_tlk'                                                 );
define ( 'swefminer_supported_pdo_drivers',  'mysql'                                                );
define ( 'swefminer_pdo_driver_mysql',       'mysql'                                                );


?>
