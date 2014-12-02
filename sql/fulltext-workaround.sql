ALTER TABLE `mirror_posts`
ADD FULLTEXT `title` (`title`),
ADD FULLTEXT `body` (`body`),
ADD FULLTEXT `title_body` (`title`, `body`);