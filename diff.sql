ALTER TABLE `posts`
ADD `publish_date` datetime NOT NULL;

CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `key` text NOT NULL,
  `value` text NOT NULL
) COMMENT='';

REPLACE INTO `settings` (`key`, `value`)
VALUES
('random_search', TRUE);