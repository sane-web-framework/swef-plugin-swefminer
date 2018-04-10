
DELIMITER $$

DROP PROCEDURE IF EXISTS `swefMinerColumnCreate` $$
CREATE PROCEDURE `swefMinerColumnCreate` (
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

DROP PROCEDURE IF EXISTS `swefMinerColumnRemove` $$
CREATE PROCEDURE `swefMinerColumnRemove` (
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

DROP PROCEDURE IF EXISTS `swefMinerColumns` $$
CREATE PROCEDURE `swefMinerColumns` (
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `lik` VARCHAR(64) CHARSET ascii
)
BEGIN
  SELECT    (`column_Ignore` OR `table_Ignore`) AS `ignore`
           ,`column_Database` AS `database`
           ,`column_Table` AS `table`
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

DROP PROCEDURE IF EXISTS `swefMinerColumnUnremove` $$
CREATE PROCEDURE `swefMinerColumnUnremove` (
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

DROP PROCEDURE IF EXISTS `swefMinerColumnUpdate` $$
CREATE PROCEDURE `swefMinerColumnUpdate` (
      IN    `dbn` VARCHAR(64) CHARSET ascii
     ,IN    `tbn` VARCHAR(64) CHARSET ascii
     ,IN    `cln` VARCHAR(64) CHARSET ascii
     ,IN    `hdg` VARCHAR(64) CHARSET utf8
     ,IN    `hnt` VARCHAR(255) CHARSET utf8
)
BEGIN
  UPDATE
            `swefminer_column`
  SET
            `column_Ignore`='0'
           ,`column_Heading`=hdg
           ,`column_Hint`=hnt
  WHERE     `column_Database`=dbn
    AND     `column_Table`=tbn
    AND     `column_Column`=cln
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerTableCreate` $$
CREATE PROCEDURE `swefMinerTableCreate` (
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

DROP PROCEDURE IF EXISTS `swefMinerTableRemove` $$
CREATE PROCEDURE `swefMinerTableRemove` (
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

DROP PROCEDURE IF EXISTS `swefMinerTables` $$
CREATE PROCEDURE `swefMinerTables` (
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

DROP PROCEDURE IF EXISTS `swefMinerTableUnremove` $$
CREATE PROCEDURE `swefMinerTableUnremove` (
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

DROP PROCEDURE IF EXISTS `swefMinerTableUpdate` $$
CREATE PROCEDURE `swefMinerTableUpdate` (
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

DELIMITER ;

