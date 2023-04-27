CREATE SCHEMA `pdo_dao_singleton` DEFAULT CHARACTER SET utf8mb4 ;

USE pdo_dao_singleton;

CREATE TABLE `pdo_dao_singleton`.`profile` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE = innodb;

CREATE TABLE `pdo_dao_singleton`.`user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(140) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 0 ,
  `profileId` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`)) ENGINE = innodb;

ALTER TABLE `pdo_dao_singleton`.`user` 
ADD INDEX `PROFILE_IDX` (`profileId` ASC);

ALTER TABLE `pdo_dao_singleton`.`user` 
ADD CONSTRAINT `FK_PROFILE`
  FOREIGN KEY (`profileId`)
  REFERENCES `pdo_dao_singleton`.`profile` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


