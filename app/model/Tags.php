<?php

namespace Model;

use Nette;

class Tags extends Nette\Object {

	/** @var Nette\Database\Context @inject */
	public $database;

	public function getByName($name) {
		return $this->database->table('tags')->where('name = ?', $name);
	}

	public function getAllTags() {
		return $this->database->table('tags');
	}

	//TagCloud data:
	//SELECT tag_id,COUNT(*) AS cnt FROM posts_tags GROUP BY tag_id ORDER BY COUNT(*) DESC
	//https://github.com/NoahY/q2a-log-tags/blob/master/qa-tag-cloud.php
	public function getTagCloud() {
		$tags = $this->database->table('posts_tags')->select('tag_id,COUNT(*) AS cnt')->group('tag_id')->order('COUNT(*) DESC');
		$score = array();
		foreach ($tags as $tag) {
			$score[$tag->tag->name] = $tag->cnt;
		}
		$min_score = log(min($score));
		$score_spread = log(max($score)) - $min_score;
		$font_spread = 25 - 10; //font-sizes
		$font_step = $font_spread / $score_spread;
		foreach ($tags as $tag) {
			$font_size = 10 + ((log($tag->cnt) - $min_score) * $font_step);
			$score[$tag->tag->name] = round($font_size);
		}
		return $score;
	}

}