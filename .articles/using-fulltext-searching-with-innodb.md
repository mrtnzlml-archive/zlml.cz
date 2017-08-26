---
id: 30938e58-9de3-403e-a862-493564630ddd
timestamp: 1375126671000
title: Using fulltext searching with InnoDB
slug: using-fulltext-searching-with-innodb
---
Sometimes is quite useful to use InnoDB engine. 
Unfortunately InnoDB is good for tables with foreign keys, but useless for fulltext search. 
You can't create fulltext index on InnoDB tables, but you can create this index on MyISAM tables. 
Unfortunately you can't create foreign keys on MyISAM. It's starting to be quite embarassing. 
Let me show you how to search via fulltext on InnoDB tables.

In fact it's not possible to use fulltext index on InnoDB tables, 
but there is possible workaround. At first you need a classic InnoDB structure. 
For example database of  blog:

```sql
SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL,
  `release_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_3` (`title`(200))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `posts_tags`;
CREATE TABLE `posts_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `posts_tags_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`),
  CONSTRAINT `posts_tags_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Now we have pretty simple database structure with InnoDB tables with foreign keys. 
It would be nice to be able search on database table **posts** using fulltext search:

```sql
SELECT *
FROM posts
WHERE MATCH(title, body) AGAINST ('something' IN BOOLEAN MODE);
```

But it is not possible. It returns something like:
>   Error in query: The used table type doesn't support FULLTEXT indexes

Let's create another one table with triggers and fulltext indexes. 
We need to create mirror table. For example:

```sql
DROP TABLE IF EXISTS `mirror_posts`;
CREATE TABLE `mirror_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title_body` (`title`,`body`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

And than we need to create triggers:

```sql
DELIMITER ;;

CREATE TRIGGER `insert_posts` AFTER INSERT ON `posts` FOR EACH ROW
INSERT INTO mirror_posts VALUES (NEW.id, NEW.title, NEW.body);;

CREATE TRIGGER `update_posts` AFTER UPDATE ON `posts` FOR EACH ROW
UPDATE mirror_posts SET
    id = NEW.id,
    title = NEW.title,
    body = NEW.body
WHERE id = OLD.id;;

CREATE TRIGGER `delete_posts` AFTER DELETE ON `posts` FOR EACH ROW
DELETE FROM mirror_posts WHERE id = OLD.id;;
```

It means, that we copy all of events and data from table **posts** to the table **mirror_posts**.
Finally we can use more complex fulltext search feature:

```sql
SELECT *
FROM mirror_posts
WHERE MATCH(title, body) AGAINST ('something' IN BOOLEAN MODE)
ORDER BY 5 * MATCH(title) AGAINST ('something') + MATCH(body) AGAINST ('something') DESC;
```

As I said, this is just workaround, not solution. 
Sometimes it's bad practice, because you need copy of indexed columns. 
But it works. And for small blogs it's sufficient.