<?php
if (!defined('DXMA_VERSION')) die();

DEFINE("DBVERSION", 2);

$migrations = array_fill(0, DBVERSION + 1, "");

$migrations[0] = <<<'COMMIT'
CREATE TABLE IF NOT EXISTS SchemaVersion (
    `version` INTEGER PRIMARY KEY NOT NULL
);
COMMIT;

$migrations[1] = <<<'COMMIT'
CREATE TABLE IF NOT EXISTS `User` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `username` VARCHAR(32) NOT NULL,
    `passhash` VARCHAR(256) NOT NULL,
    `email` VARCHAR(256),
    `realname` VARCHAR(256) NOT NULL,
    `website` VARCHAR(256) NOT NULL DEFAULT "",
    `description` VARCHAR(256) NOT NULL DEFAULT "",
    `joined` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `Mission` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `version` VARCHAR(40) NOT NULL,
    `user` INTEGER NOT NULL REFERENCES User(id) ON DELETE CASCADE,
    `author` VARCHAR(128) NOT NULL,
    `description` TEXT,
    `mode` INTEGER NOT NULL,
    `game` INTEGER NOT NULL,
    `levels` INTEGER NOT NULL DEFAULT 1,
    `playersMin` INTEGER NOT NULL DEFAULT 1,
    `playersMax` INTEGER NOT NULL DEFAULT 1,
    `released` DATE NOT NULL,
    `filename` VARCHAR(256) NOT NULL,
    `screenshot` VARCHAR(256),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `updated` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `Favorite` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `user` INTEGER NOT NULL REFERENCES User(id) ON DELETE CASCADE,
    `mission` INTEGER NOT NULL REFERENCES Mission(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `Rating` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `user` INTEGER NOT NULL REFERENCES User(id) ON DELETE CASCADE,
    `mission` INTEGER NOT NULL REFERENCES Mission(id) ON DELETE CASCADE,
    `rating` INTEGER NOT NULL
);

CREATE INDEX idx_user ON User (id);
CREATE INDEX idx_mission ON Mission (id);
CREATE INDEX idx_ratings_mission ON Rating (mission);
COMMIT;

$migrations[2] = <<<'COMMIT'
ALTER TABLE User
ADD COLUMN `forgotcode` VARCHAR(32) AFTER email,
ADD COLUMN `forgotexpiry` TIMESTAMP DEFAULT '0000-00-00 00:00:00' AFTER forgotcode;

COMMIT;

?>
