SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema ccro_bc
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ccro_bc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `ccro_bc` ;

-- -----------------------------------------------------
-- Table `ccro_bc`.`volume`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ccro_bc`.`volume` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NULL,
  `number` INT NULL,
  `series` VARCHAR(20) NULL,
  `date_received_start` DATE NULL,
  `date_received_end` DATE NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccro_bc`.`birth_certificate`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ccro_bc`.`birth_certificate` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `registry_no` VARCHAR(11) NULL,
  `date_of_registration` DATE NULL,
  `firstname` VARCHAR(45) NULL,
  `middlename` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  `sex` VARCHAR(6) NULL,
  `date_of_birth` DATE NULL,
  `place_of_birth` VARCHAR(150) NULL,
  `mother_firstname` VARCHAR(45) NULL,
  `mother_middlename` VARCHAR(45) NULL,
  `mother_lastname` VARCHAR(45) NULL,
  `mother_citizenship` VARCHAR(45) NULL,
  `father_firstname` VARCHAR(45) NULL,
  `father_middlename` VARCHAR(45) NULL,
  `father_lastname` VARCHAR(45) NULL,
  `father_citizenship` VARCHAR(45) NULL,
  `parents_marriage_date` DATE NULL,
  `parents_marriage_place` VARCHAR(150) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `registry_no_UNIQUE` (`registry_no` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccro_bc`.`attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ccro_bc`.`attachment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `volume_id` INT NOT NULL,
  `file_name` VARCHAR(15) NULL,
  `file_path` VARCHAR(255) NULL,
  `birth_certificate_id` INT NOT NULL,
  PRIMARY KEY (`id`, `volume_id`, `birth_certificate_id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_file_volume_idx` (`volume_id` ASC),
  INDEX `fk_attachment_birth_certificate_idx` (`birth_certificate_id` ASC),
  CONSTRAINT `fk_file_volume`
    FOREIGN KEY (`volume_id`)
    REFERENCES `ccro_bc`.`volume` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_attachment_birth_certificate`
    FOREIGN KEY (`birth_certificate_id`)
    REFERENCES `ccro_bc`.`birth_certificate` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
