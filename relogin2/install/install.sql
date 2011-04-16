-- table
CREATE TABLE IF NOT EXISTS `{prefix}sessions` (
  `sess_id` varchar(40) NOT NULL,
  `sess_data` text NOT NULL,
  `remember` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL,
  `mtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sess_id`)
) ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};

-- table
CREATE  TABLE IF NOT EXISTS `{prefix}users` (
  `userid` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `userpass` VARCHAR(45) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `rank` INT UNSIGNED NOT NULL ,
  `regtime` TIMESTAMP NULL ,
  `refreshtime` TIMESTAMP NULL ,
  `logintime` TIMESTAMP NULL ,
  `onlinetime` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `onlinestatus` TINYINT(1) NOT NULL DEFAULT 0 ,
  `invitations` TINYINT UNSIGNED NULL ,
  `useremail` VARCHAR(100) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `newsreadtime` TIMESTAMP NULL,
  PRIMARY KEY (`userid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};

-- table
CREATE  TABLE IF NOT EXISTS `{prefix}profiles` (
  `userid` INT UNSIGNED NOT NULL ,
  `firstname` VARCHAR(45) NOT NULL ,
  `lastname` VARCHAR(45) NOT NULL ,
  `birthdate` DATE NULL ,
  `sex` ENUM('m','f') NULL ,
  `country` VARCHAR(100) NOT NULL ,
  `city` VARCHAR(45) NOT NULL ,
  `useremail` VARCHAR(100) NOT NULL ,
  `public_mail` TINYINT(1) NOT NULL ,
  `website` VARCHAR(100) NOT NULL ,
  `msn` VARCHAR(100) NOT NULL ,
  `skype` VARCHAR(100) NOT NULL ,
  `other` text NULL ,
  `avatar` enum('gravatar','mkavatar','none') default 'none',
  PRIMARY KEY (`userid`) ,
  INDEX `fk_profiles_users` (`userid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};

-- table
CREATE  TABLE IF NOT EXISTS `{prefix}invites` (
  `inviteid` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `fromid` INT UNSIGNED NOT NULL ,
  `toid` INT UNSIGNED NOT NULL ,
  `code` VARCHAR(45) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `email` VARCHAR(100) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL COMMENT 'A meghivott email-je' ,
  `sendtime` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`inviteid`) ,
  INDEX `fk_invites_users1` (`fromid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};

-- table
CREATE  TABLE IF NOT EXISTS `{prefix}ranks` (
  `rankid` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL COMMENT 'A rang megjelenitendo neve' ,
  `varname` VARCHAR(20) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL COMMENT 'A valtozo neve' ,
  PRIMARY KEY (`rankid`) ,
  UNIQUE INDEX `unique` (`varname` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};

-- table
INSERT INTO `{prefix}ranks` (`rankid`, `name`, `varname`) VALUES
(5, 'Vendég', 'guest'),
(1, 'Tulajdonos', 'owner'),
(2, 'Adminisztrátor', 'admin'),
(3, 'Felhasználó', 'user'),
(4, 'Tiltott', 'banned');

-- table
UPDATE `{prefix}ranks` SET `rankid` = '0' WHERE `rankid` = 5;

-- table
CREATE  TABLE IF NOT EXISTS `{prefix}forgotpass` (
  `userid` INT UNSIGNED NOT NULL ,
  `sendtime` TIMESTAMP NOT NULL ,
  `code` VARCHAR(45) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  PRIMARY KEY (`userid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};

-- table
CREATE  TABLE IF NOT EXISTS `{prefix}messages` (
  `messageid` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `fromid` INT UNSIGNED NOT NULL ,
  `toid` INT UNSIGNED NOT NULL ,
  `deleted` INT UNSIGNED NOT NULL COMMENT 'Annak a usernek az id-je, aki torolte mar az uzenetet' ,
  `sendtime` TIMESTAMP NULL ,
  `readtime` TIMESTAMP NULL ,
  `subject` VARCHAR(100) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `body` TEXT CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  PRIMARY KEY (`messageid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};


-- table
CREATE  TABLE IF NOT EXISTS `{prefix}searchlog` (
  `searchid` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userid` INT UNSIGNED NOT NULL ,
  `logname` VARCHAR(100) CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `logtext` TEXT CHARACTER SET '{charset}' COLLATE '{collate}' NOT NULL ,
  `logtime` timestamp NOT NULL ,
  PRIMARY KEY (`searchid`) ,
  INDEX `fk_searchlog_users1` (`userid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = {charset}
COLLATE = {collate};