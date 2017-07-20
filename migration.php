<?php

require_once "bootstrap.php";

$app = new \App\App();
$app->bootDatabase();

$sql = <<<query
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `files`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `post_tag`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `posts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `markdown_content` TEXT CHARACTER SET utf8mb4 NOT NULL,
  `parsed_content` TEXT CHARACTER SET utf8mb4 NOT NULL,
  `summary` VARCHAR(255) NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT '0',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `post_id` INT(11) UNSIGNED NOT NULL,
  `parent_id` INT(11) UNSIGNED DEFAULT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `files` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) UNSIGNED DEFAULT NULL,
  `path` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(50) DEFAULT NULL,
  `bytes` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post` (`post_id`),
  CONSTRAINT `post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `post_tag` (
  `post_id` INT(10) UNSIGNED NOT NULL,
  `tag_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_provider` VARCHAR(50) NOT NULL,
  `oauth_uid` VARCHAR(50) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `avatar` VARCHAR(255) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `is_admin` TINYINT(1) NOT NULL DEFAULT '0',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_provider_oauth_uid` (`oauth_provider`,`oauth_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
query;

/** @var PDO $pdo */
$pdo = $app->getContainer()->get('db')->getConnection()->getPdo();

try {
    $pdo->exec($sql);
    echo "success migration!";
} catch (\Exception $e) {
    echo "fail migration : {$e->getMessage()}";
}



