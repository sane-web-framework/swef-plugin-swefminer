
DELIMITER $$


DROP PROCEDURE IF EXISTS `swefMinerTables` $$
CREATE PROCEDURE `swefMinerTables` ()  BEGIN
  SELECT  `table_Database` AS `database`
         ,`table_Table` AS `table`
         ,`table_Title` AS `title`
         ,`table_Description` AS `description`
  FROM    `swefminer_table`
  ORDER BY `table_Database`,`table_Table`
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerColumns` $$
CREATE PROCEDURE `swefMinerColumns` ()  BEGIN
  SELECT `set_Set` AS `set`
        ,`set_Read_Only` AS `read_only`
        ,`table_Table` AS `table`
        ,`table_Title` AS `title`
        ,`table_Description` AS `description`
        ,`column_Column` AS `column`
        ,`column_Is_UUID` AS `is_uuid`
        ,`column_Parent_Table` AS `parent_table`
        ,`column_Parent_Column` AS `parent_column`
        ,`column_Describes_Record` AS `describes_record`
        ,`column_Heading` AS `heading`
        ,`column_Hint` AS `hint`
  FROM `swefminer_column`
  LEFT JOIN `swefminer_table`
         ON `table_Table`=`column_Table`
  ORDER BY `swefminer_table`.`table_Table`,`swefminer_column`.`column_Ordering`
  ;
END$$


DELIMITER ;

