
DELIMITER $$

DROP PROCEDURE IF EXISTS `swefMinerModelColumns` $$
CREATE PROCEDURE `swefMinerModelColumns` (
      IN    `dbn` VARCHAR(64) CHARSET utf8
     ,IN    `lik` VARCHAR(64) CHARSET utf8
)
BEGIN
  SELECT    `information_schema`.`COLUMNS`.*
           ,`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_SCHEMA`
           ,`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME`
           ,`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_COLUMN_NAME`
  FROM      `information_schema`.`COLUMNS`
  LEFT JOIN `information_schema`.`KEY_COLUMN_USAGE`
      USING (
      	    `TABLE_CATALOG`,`TABLE_SCHEMA`,`TABLE_NAME`,`COLUMN_NAME`
      )
  WHERE     `information_schema`.`COLUMNS`.`TABLE_SCHEMA`=dbn
    AND     `information_schema`.`COLUMNS`.`TABLE_NAME` LIKE lik
  ORDER BY  `information_schema`.`COLUMNS`.`TABLE_NAME`
           ,`information_schema`.`COLUMNS`.`ORDINAL_POSITION`
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerModelReferers` $$
CREATE PROCEDURE `swefMinerModelReferers` (
      IN    `dbn` VARCHAR(64) CHARSET utf8
     ,IN    `tbn` VARCHAR(64) CHARSET utf8
)
BEGIN
  SELECT    `information_schema`.`COLUMNS`.*
           ,`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_SCHEMA`
           ,`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME`
           ,`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_COLUMN_NAME`
  FROM      `information_schema`.`COLUMNS`
  LEFT JOIN `information_schema`.`KEY_COLUMN_USAGE`
      USING (
      	    `TABLE_CATALOG`,`TABLE_SCHEMA`,`TABLE_NAME`,`COLUMN_NAME`
      )
  WHERE     `information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_SCHEMA`=dbn
    AND     `information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME`=tbn
  ORDER BY  `information_schema`.`COLUMNS`.`ORDINAL_POSITION`
  ;
END$$

DROP PROCEDURE IF EXISTS `swefMinerModelTables` $$
CREATE PROCEDURE `swefMinerModelTables` (
      IN    `dbn` VARCHAR(64) CHARSET utf8
     ,IN    `lik` VARCHAR(64) CHARSET utf8
)
BEGIN
  SELECT    `information_schema`.`TABLES`.*
           ,GROUP_CONCAT(
                `information_schema`.`COLUMNS`.`COLUMN_NAME`
         	    SEPARATOR ','
            ) AS `PRIMARY_KEY`
  FROM      `information_schema`.`TABLES`
  LEFT JOIN `information_schema`.`COLUMNS`
        USING (
            `TABLE_CATALOG`,`TABLE_SCHEMA`,`TABLE_NAME`
        )
  WHERE     `information_schema`.`COLUMNS`.`TABLE_SCHEMA`=dbn
    AND     `information_schema`.`COLUMNS`.`TABLE_NAME` LIKE lik
    AND     `information_schema`.`COLUMNS`.`COLUMN_KEY`='PRI'
  GROUP BY  `information_schema`.`COLUMNS`.`TABLE_NAME`
  ORDER BY  `information_schema`.`COLUMNS`.`TABLE_NAME`
  ;
END$$

DELIMITER ;

