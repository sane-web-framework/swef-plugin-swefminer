
-- STORED PROCEDURES --

DELIMITER $$

DROP PROCEDURE IF EXISTS `swefMinerColumnCreate`$$
CREATE PROCEDURE `swefMinerColumnCreate`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
)
BEGIN
  INSERT INTO
            `swefminer_column`
  SET
            `column_Database`=dbn
           ,`column_Table`=tbn
           ,`column_Column`=cln
           ,`column_Heading`=cln
           ,`column_Hint`='Column hint'
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerColumnRemove`$$
CREATE PROCEDURE `swefMinerColumnRemove`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
)
BEGIN
  UPDATE
            `swefminer_column`
  SET
            `column_Ignore`='1'
  WHERE     `column_Database`=dbn
    AND     `column_Table`=tbn
    AND     `column_Column`=cln
  LIMIT 1
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerColumns`$$
CREATE PROCEDURE `swefMinerColumns`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `lik` VARCHAR(64) CHARSET ascii
)
BEGIN
  SELECT    (`column_Ignore` OR `table_Ignore`) AS `ignore`
           ,`column_Database` AS `database`
           ,`column_Table` AS `table`
           ,`table_Title` AS `title`
           ,`column_Column` AS `column`
           ,`column_Trashes_Record` AS `trashes_record`
           ,`column_Is_UUID` AS `is_uuid`
           ,`column_Is_Edited_By` AS `is_edited_by`
           ,`column_Is_Edited` AS `is_edited`
           ,`column_Parent_Table` AS `parent_table`
           ,`column_Parent_Column` AS `parent_column`
           ,`column_Describes_Record` AS `describes_record`
           ,`column_Heading` AS `heading`
           ,`column_Hint` AS `hint`
           ,GROUP_CONCAT(
                DISTINCT `select_Usergroup`
                SEPARATOR ','
              ) AS `selectors`
           ,GROUP_CONCAT(
                DISTINCT `update_Usergroup`
                SEPARATOR ','
              ) AS `updaters`
  FROM      `swefminer_column`
  LEFT JOIN `swefminer_table`
         ON `table_database`=`column_Database`
        AND `table_table`=`column_Table`
  LEFT JOIN `swefminer_select`
         ON `select_Database`=`column_Database`
        AND `select_Table`=`column_Table`
        AND `select_Column`=`column_Column`
  LEFT JOIN `swefminer_update`
         ON `update_Database`=`column_Database`
        AND `update_Table`=`column_Table`
        AND `update_Column`=`column_Column`
  WHERE     `column_Database`=dbn
    AND     `column_Table` LIKE lik
  GROUP BY  `column_Table`,`column_Column`
  ORDER BY  `column_Table`,`column_Column`
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerColumnUnremove`$$
CREATE PROCEDURE `swefMinerColumnUnremove`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
)
BEGIN
  UPDATE
            `swefminer_column`
  SET
            `column_Ignore`='0'
  WHERE     `column_Database`=dbn
    AND     `column_Table`=tbn
    AND     `column_Column`=cln
  LIMIT 1
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerColumnUpdate`$$
CREATE PROCEDURE `swefMinerColumnUpdate`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
     ,IN    `hdg` VARCHAR(64) CHARSET utf8
     ,IN    `hnt` VARCHAR(255) CHARSET utf8
     ,IN    `trr` INT(1)
     ,IN    `iuu` INT(1)
     ,IN    `ieb` INT(1)
     ,IN    `ied` INT(1)
     ,IN    `drc` INT(1)
)
BEGIN
  UPDATE
            `swefminer_column`
  SET
            `column_Ignore`='0'
           ,`column_Heading`=hdg
           ,`column_Hint`=hnt
           ,`column_Trashes_Record`=trr
           ,`column_Is_UUID`=iuu
           ,`column_Is_Edited_By`=ieb
           ,`column_Is_Edited`=ied
           ,`column_Describes_Record`=drc
  WHERE     `column_Database`=dbn
    AND     `column_Table`=tbn
    AND     `column_Column`=cln
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerDeleteAllow`$$
CREATE PROCEDURE `swefMinerDeleteAllow`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  INSERT IGNORE INTO
            `swefminer_delete`
  SET
            `delete_Database`=dbn
           ,`delete_Table`=tbn
           ,`delete_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerDeleteDeny`$$
CREATE PROCEDURE `swefMinerDeleteDeny`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  DELETE FROM
            `swefminer_delete`
  WHERE     `delete_Database`=dbn
    AND     `delete_Table`=tbn
    AND     `delete_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerInsertAllow`$$
CREATE PROCEDURE `swefMinerInsertAllow`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  INSERT IGNORE INTO
            `swefminer_insert`
  SET
            `insert_Database`=dbn
           ,`insert_Table`=tbn
           ,`insert_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerInsertDeny`$$
CREATE PROCEDURE `swefMinerInsertDeny`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  DELETE FROM
            `swefminer_insert`
  WHERE     `insert_Database`=dbn
    AND     `insert_Table`=tbn
    AND     `insert_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerSelectAllow`$$
CREATE PROCEDURE `swefMinerSelectAllow`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  INSERT IGNORE INTO
            `swefminer_select`
  SET
            `select_Database`=dbn
           ,`select_Table`=tbn
           ,`select_Column`=cln
           ,`select_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerSelectDeny`$$
CREATE PROCEDURE `swefMinerSelectDeny`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  DELETE FROM
            `swefminer_select`
  WHERE     `select_Database`=dbn
    AND     `select_Table`=tbn
    AND     `select_Column`=cln
    AND     `select_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerTableCreate`$$
CREATE PROCEDURE `swefMinerTableCreate`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
)
BEGIN
  INSERT INTO
            `swefminer_table`
  SET
            `table_Database`=dbn
           ,`table_Table`=tbn
           ,`table_Title`='Untitled'
           ,`table_Description`='No description'
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerTableRemove`$$
CREATE PROCEDURE `swefMinerTableRemove`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
)
BEGIN
  UPDATE
            `swefminer_table`
  SET
            `table_Ignore`='1'
  WHERE     `table_Database`=dbn
    AND     `table_Table`=tbn
  LIMIT 1
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerTables`$$
CREATE PROCEDURE `swefMinerTables`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `lik` VARCHAR(64) CHARSET ascii
)
BEGIN
  SELECT    `table_Ignore` AS `ignore`
           ,`table_Database` AS `database`
           ,`table_Table` AS `table`
           ,`table_Title` AS `title`
           ,`table_Description` AS `description`
           ,GROUP_CONCAT(
                DISTINCT `insert_Usergroup`
                SEPARATOR ','
              ) AS `inserters`
           ,GROUP_CONCAT(
                DISTINCT `delete_Usergroup`
                SEPARATOR ','
              ) AS `deleters`
  FROM      `swefminer_table`
  LEFT JOIN `swefminer_insert`
         ON `insert_Database`=`table_Database`
        AND `insert_Table`=`table_Table`
  LEFT JOIN `swefminer_delete`
         ON `delete_Database`=`table_Database`
        AND `delete_Table`=`table_Table`
  WHERE `table_Database`=dbn
    AND `table_Table` LIKE lik
  GROUP BY  `table_Table`
  ORDER BY  `table_Database`,`table_Table`
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerTableUnremove`$$
CREATE PROCEDURE `swefMinerTableUnremove`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
)
BEGIN
  UPDATE
            `swefminer_table`
  SET
            `table_Ignore`='0'
  WHERE     `table_Database`=dbn
    AND     `table_Table`=tbn
  LIMIT 1
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerTableUpdate`$$
CREATE PROCEDURE `swefMinerTableUpdate`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `ttl` VARCHAR(64) CHARSET utf8
     ,IN    `dsc` VARCHAR(255) CHARSET utf8
)
BEGIN
  UPDATE
            `swefminer_table`
  SET
            `table_Ignore`='0'
           ,`table_Title`=ttl
           ,`table_Description`=dsc
  WHERE     `table_Database`=dbn
    AND     `table_Table`=tbn
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerUpdateAllow`$$
CREATE PROCEDURE `swefMinerUpdateAllow`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  INSERT IGNORE INTO
            `swefminer_update`
  SET
            `update_Database`=dbn
           ,`update_Table`=tbn
           ,`update_Column`=cln
           ,`update_Usergroup`=ugp
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerUpdateDeny`$$
CREATE PROCEDURE `swefMinerUpdateDeny`(
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
     ,IN    `ugp` VARCHAR(64) CHARSET ascii
)
BEGIN
  DELETE FROM
            `swefminer_update`
  WHERE     `update_Database`=dbn
    AND     `update_Table`=tbn
    AND     `update_Column`=cln
    AND     `update_Usergroup`=ugp
  ;
END$$

DELIMITER ;


-- SWEF INPUT FILTERS --

INSERT IGNORE INTO `swef_config_input`
    ( `input_Procedure`, `input_Arg`, `input_Filter_Name` )
  VALUES
    ( 'swefMinerColumnCreate',      1,  'dbIdentifier'  ),
    ( 'swefMinerColumnCreate',      2,  'dbIdentifier'  ),
    ( 'swefMinerColumnCreate',      3,  'dbIdentifier'  ),
    ( 'swefMinerColumnRemove',      1,  'dbIdentifier'  ),
    ( 'swefMinerColumnRemove',      2,  'dbIdentifier'  ),
    ( 'swefMinerColumnRemove',      3,  'dbIdentifier'  ),
    ( 'swefMinerColumns',           1,  'dbIdentifier'  ),
    ( 'swefMinerColumns',           2,  'string1-64'    ),
    ( 'swefMinerColumnUnremove',    1,  'dbIdentifier'  ),
    ( 'swefMinerColumnUnremove',    2,  'dbIdentifier'  ),
    ( 'swefMinerColumnUnremove',    3,  'dbIdentifier'  ),
    ( 'swefMinerColumnUpdate',      1,  'dbIdentifier'  ),
    ( 'swefMinerColumnUpdate',      2,  'dbIdentifier'  ),
    ( 'swefMinerColumnUpdate',      3,  'dbIdentifier'  ),
    ( 'swefMinerColumnUpdate',      4,  'string1-64'    ),
    ( 'swefMinerColumnUpdate',      5,  'string1-255'   ),
    ( 'swefMinerColumnUpdate',      6,  'dbBoolean'     ),
    ( 'swefMinerColumnUpdate',      7,  'dbBoolean'     ),
    ( 'swefMinerColumnUpdate',      8,  'dbBoolean'     ),
    ( 'swefMinerColumnUpdate',      9,  'dbBoolean'     ),
    ( 'swefMinerColumnUpdate',     10,  'dbBoolean'     ),
    ( 'swefMinerDeleteAllow',       1,  'dbIdentifier'  ),
    ( 'swefMinerDeleteAllow',       2,  'dbIdentifier'  ),
    ( 'swefMinerDeleteAllow',       3,  'usergroup'     ),
    ( 'swefMinerDeleteDeny',        1,  'dbIdentifier'  ),
    ( 'swefMinerDeleteDeny',        2,  'dbIdentifier'  ),
    ( 'swefMinerDeleteDeny',        3,  'usergroup'     ),
    ( 'swefMinerInsertAllow',       1,  'dbIdentifier'  ),
    ( 'swefMinerInsertAllow',       2,  'dbIdentifier'  ),
    ( 'swefMinerInsertAllow',       3,  'usergroup'     ),
    ( 'swefMinerInsertDeny',        1,  'dbIdentifier'  ),
    ( 'swefMinerInsertDeny',        2,  'dbIdentifier'  ),
    ( 'swefMinerInsertDeny',        3,  'usergroup'     ),
    ( 'swefMinerSelectAllow',       1,  'dbIdentifier'  ),
    ( 'swefMinerSelectAllow',       2,  'dbIdentifier'  ),
    ( 'swefMinerSelectAllow',       3,  'dbIdentifier'  ),
    ( 'swefMinerSelectAllow',       4,  'usergroup'     ),
    ( 'swefMinerSelectDeny',        1,  'dbIdentifier'  ),
    ( 'swefMinerSelectDeny',        2,  'dbIdentifier'  ),
    ( 'swefMinerSelectDeny',        3,  'dbIdentifier'  ),
    ( 'swefMinerSelectDeny',        4,  'usergroup'     ),
    ( 'swefMinerTableCreate',       1,  'dbIdentifier'  ),
    ( 'swefMinerTableCreate',       2,  'dbIdentifier'  ),
    ( 'swefMinerTableRemove',       1,  'dbIdentifier'  ),
    ( 'swefMinerTableRemove',       2,  'dbIdentifier'  ),
    ( 'swefMinerTables',            1,  'dbIdentifier'  ),
    ( 'swefMinerTables',            2,  'string1-64'    ),
    ( 'swefMinerTableUnremove',     1,  'dbIdentifier'  ),
    ( 'swefMinerTableUnremove',     2,  'dbIdentifier'  ),
    ( 'swefMinerTableUpdate',       1,  'dbIdentifier'  ),
    ( 'swefMinerTableUpdate',       2,  'dbIdentifier'  ),
    ( 'swefMinerTableUpdate',       3,  'displayName'   ),
    ( 'swefMinerTableUpdate',       4,  'string1-255'   ),
    ( 'swefMinerUpdateAllow',       1,  'dbIdentifier'  ),
    ( 'swefMinerUpdateAllow',       2,  'dbIdentifier'  ),
    ( 'swefMinerUpdateAllow',       3,  'dbIdentifier'  ),
    ( 'swefMinerUpdateAllow',       4,  'usergroup'     ),
    ( 'swefMinerUpdateDeny',        1,  'dbIdentifier'  ),
    ( 'swefMinerUpdateDeny',        2,  'dbIdentifier'  ),
    ( 'swefMinerUpdateDeny',        3,  'dbIdentifier'  ),
    ( 'swefMinerUpdateDeny',        4,  'usergroup'     );


-- SWEF PLUGIN REGISTRATION --

INSERT IGNORE INTO `swef_config_plugin`
    (
      `plugin_Dash_Allow`, `plugin_Dash_Usergroup_Preg_Match`, `plugin_Enabled`,
      `plugin_Context_LIKE`, `plugin_Classname`, `plugin_Handle_Priority`
    )
  VALUES
    ( 0, '', 1, 'www-%', '\\Swef\\SwefMiner', 100 ),
    ( 1, '<^(sysadmin|admin)$>', 1, 'dashboard', '\\Swef\\SwefMiner', 100 );


-- SWEFMINER TABLES --

CREATE TABLE IF NOT EXISTS `swefminer_column` (
  `column_Ignore` int(1) unsigned NOT NULL,
  `column_Database` varchar(64) CHARACTER SET ascii NOT NULL,
  `column_Table` varchar(255) CHARACTER SET ascii NOT NULL,
  `column_Column` varchar(255) CHARACTER SET ascii NOT NULL,
  `column_Trashes_Record` int(1) unsigned NOT NULL,
  `column_Is_UUID` int(1) unsigned NOT NULL,
  `column_Is_Edited_By` int(1) unsigned NOT NULL,
  `column_Is_Edited` int(1) unsigned NOT NULL,
  `column_Parent_Table` varchar(64) CHARACTER SET ascii NOT NULL,
  `column_Parent_Column` varchar(64) CHARACTER SET ascii NOT NULL,
  `column_Describes_Record` int(1) unsigned NOT NULL,
  `column_Heading` varchar(64) NOT NULL,
  `column_Hint` varchar(255) NOT NULL,
  PRIMARY KEY (`column_Database`,`column_Table`,`column_Column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `swefminer_delete` (
  `delete_Database` varchar(64) NOT NULL,
  `delete_Table` varchar(64) CHARACTER SET ascii NOT NULL,
  `delete_Usergroup` varchar(64) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`delete_Database`,`delete_Table`,`delete_Usergroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `swefminer_insert` (
  `insert_Database` varchar(64) NOT NULL,
  `insert_Table` varchar(64) CHARACTER SET ascii NOT NULL,
  `insert_Usergroup` varchar(64) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`insert_Database`,`insert_Table`,`insert_Usergroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `swefminer_select` (
  `select_Database` varchar(64) NOT NULL,
  `select_Table` varchar(255) CHARACTER SET ascii NOT NULL,
  `select_Column` varchar(255) CHARACTER SET ascii NOT NULL,
  `select_Usergroup` varchar(64) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`select_Database`,`select_Table`,`select_Column`,`select_Usergroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `swefminer_table` (
  `table_Ignore` int(1) unsigned NOT NULL,
  `table_Database` varchar(64) CHARACTER SET ascii NOT NULL,
  `table_Table` varchar(64) CHARACTER SET ascii NOT NULL,
  `table_Title` varchar(64) NOT NULL,
  `table_Description` varchar(255) NOT NULL,
  PRIMARY KEY (`table_Database`,`table_Table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `swefminer_update` (
  `update_Database` varchar(64) NOT NULL,
  `update_Table` varchar(255) CHARACTER SET ascii NOT NULL,
  `update_Column` varchar(255) CHARACTER SET ascii NOT NULL,
  `update_Usergroup` varchar(64) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`update_Database`,`update_Table`,`update_Column`,`update_Usergroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

