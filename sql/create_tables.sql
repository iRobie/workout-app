CREATE TABLE `workout`.`categories` (
  `categoryID` INT(11) NOT NULL AUTO_INCREMENT,
  `instructions` VARCHAR(5000) default NULL ,
  `name` VARCHAR(255) default NULL ,
  `link` VARCHAR(255) default NULL ,
  `image` VARCHAR(255) default NULL,
  PRIMARY KEY (`categoryID`))
  ENGINE = InnoDB;

CREATE TABLE `workout`.`steps` (
    `stepID` INT(11) NOT NULL AUTO_INCREMENT,
    `categoryName` VARCHAR(255) NOT NULL ,
    `stepNumber` INT(11) NOT NULL ,
    `name` VARCHAR(255) default NULL ,
    `instructions` VARCHAR(5000) default NULL ,
    `link` VARCHAR(255) default NULL ,
    `video` VARCHAR(255) default NULL ,
    `image` VARCHAR(255) default NULL,
  PRIMARY KEY (`stepID`))
    ENGINE = InnoDB;

  CREATE TABLE `workout`.`tracking` (
    `trackingID` INT(11) NOT NULL AUTO_INCREMENT,
    `categoryName` VARCHAR(255) NOT NULL ,
    `stepNumber` INT(11) NOT NULL ,
    `user` VARCHAR(255) NOT NULL ,
    `reps` INT(11) NOT NULL ,
    `completed` BOOLEAN NOT NULL ,
    `failed` BOOLEAN NOT NULL ,
    `date` DATETIME NOT NULL,
  PRIMARY KEY (`trackingID`) )
    ENGINE = InnoDB;
