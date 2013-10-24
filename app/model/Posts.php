<?php

namespace Model;

use Nette;

/**
 * Class Posts
 * @package Model
 */
class Posts extends Nette\Object {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;

	/**
	 * @param $title
	 * @param $tags
	 * @param $body
	 * @param $release
	 */
	public function newPost($title, $slug, $tags, $body, $release) {
		$data = array(
			'title' => $title,
			'slug' => Nette\Utils\Strings::webalize($slug),
			'body' => $body,
			'date' => new \DateTime(),
			'release_date' => $release,
		);
		//Save post:
		$post = $this->sf->table('posts')->insert($data);
		//Save tags:
		if (!empty($tags[0])) {
			foreach ($tags as $tag) {
				$color = substr(md5(rand()), 0, 6); //Short and sweet
				if (count($this->getTagByName($tag)) == 0) { //Pouze nové tagy!
					$tag = $this->sf->table('tags')->insert(array('name' => $tag, 'color' => $color));
					$this->sf->table('posts_tags')->insert(array('post_id' => $post->id, 'tag_id' => $tag->id));
				} else {
					$tag = $this->getTagByName($tag)->fetch();
					$this->sf->table('posts_tags')->insert(array('post_id' => $post->id, 'tag_id' => $tag->id));
				}
			}
		}
	}

	/**
	 * @param $title
	 * @param $tags
	 * @param $body
	 * @param $release
	 * @param $id
	 */
	public function updatePost($title, $slug, $tags, $body, $release, $id) {
		$data = array(
			'title' => $title,
			'slug' => Nette\Utils\Strings::webalize($slug),
			'body' => $body,
			'release_date' => $release,
		);
		//Update post:
		$post = $this->sf->table('posts')->where('id = ?', $id)->update($data);
		if (!empty($tags[0])) {
			$this->sf->table('posts_tags')->where('post_id = ?', $id)->delete();
			foreach ($tags as $tag) {
				$color = substr(md5(rand()), 0, 6); //Short and sweet
				$tmp = $this->getTagByName($tag);
				if (count($tmp) == 0) { //Pouze nové tagy!
					$tag = $this->sf->table('tags')->insert(array('name' => $tag, 'color' => $color));
					$this->sf->table('posts_tags')->insert(array('post_id' => $id, 'tag_id' => $tag->id));
				} else {
					$tmp = $this->getTagByName($tag)->fetch(); //Again (because there is - maybe - one new tag)
					$this->sf->table('posts_tags')->insert(array('post_id' => $id, 'tag_id' => $tmp->id));
				}
			}
		} else {
			$this->sf->table('posts_tags')->where('post_id = ?', $id)->delete();
		}
	}

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllPosts() {
		return $this->sf->table('posts')->where('release_date < NOW()');
	}

	/**
	 * @param $limit
	 * @param $offset
	 * @return Nette\Database\Table\Selection
	 */
	public function getPosts($limit, $offset) {
		return $this->sf->table('posts')->where('release_date < NOW()')->limit($limit, $offset)->order('date DESC');
	}

	/**
	 * @param $id
	 * @return bool|mixed|IRow
	 */
	public function getPostByID($id) {
		return $this->sf->table('posts')->where('release_date < NOW()')->where('id = ?', $id)->fetch();
	}

	/**
	 * @param $tag_id
	 * @param int $limit
	 * @return array
	 */
	public function getPostsByTagID($tag_id, $limit = 20) {
		$array = array();
		foreach ($this->sf->table('posts_tags')->where('tag_id = ?', $tag_id) as $post_tag) {
			$array[] = $this->getPostByID($post_tag->post_id);
		}
		return array_reverse($array);
	}

	/**
	 * @param $tag_id
	 * @return bool|mixed|IRow
	 */
	public function getTagByID($tag_id) {
		return $this->sf->table('tags')->where('id = ?', $tag_id)->fetch();
	}

	/**
	 * @param $post_id
	 * @return Nette\Database\Table\Selection
	 */
	public function getTagsByPostID($post_id) {
		return $this->sf->table('posts_tags')->where('post_id = ?', $post_id);
	}

	/**
	 * @param $name
	 * @return Nette\Database\Table\Selection
	 */
	public function getTagByName($name) {
		return $this->sf->table('tags')->where('name = ?', $name);
	}

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllTags() {
		return $this->sf->table('tags')->order('name');
	}

	/**
	 * @param $tag_id
	 * @param $data
	 */
	public function updateTagByID($tag_id, $data) {
		$this->sf->table('tags')->where('id = ?', $tag_id)->update($data);
	}

	/**
	 * @param $id
	 */
	public function deletePostByID($id) {
		//Delete relation:
		$this->sf->table('posts_tags')->where('post_id = ?', $id)->delete();
		//Delete tags:
		// zatím je tam nechávám...
		//Delete post:
		$this->sf->table('posts')->where('id = ?', $id)->delete();
	}

	/**
	 * @param $tag_id
	 */
	public function deleteTagById($tag_id) {
		//Delete relation:
		$this->sf->table('posts_tags')->where('tag_id = ?', $tag_id)->delete();
		//Delete tag:
		$this->sf->table('tags')->where('id = ?', $tag_id)->delete();
	}

	public function fulltextSearch($search) {
		$where = "";
		$ft_min_word_len = 4;
		preg_match_all("~[\\pL\\pN_]+('[\\pL\\pN_]+)*~u", stripslashes($search), $matches);
		foreach ($matches[0] as $part) {
			if (iconv_strlen($part, "utf-8") < $ft_min_word_len) {
				$regexp = "REGEXP '[[:<:]]" . addslashes(strtoupper($part)) . "[[:>:]]'";
				$where .= " OR (title $regexp OR body $regexp)";
			}
		}

		//TODO: tag search
		//$where .= " OR tag LIKE $search";

		return $this->sf->table('mirror_posts')
			->where("MATCH(title, body) AGAINST (? IN BOOLEAN MODE)$where", $search)
			->order("5 * MATCH(title) AGAINST (?) + MATCH(body) AGAINST (?) DESC", $search, $search)
			->limit(50);
	}

	// Routers:
	public function getBySlug($slug) {
		return $this->sf->table('posts')->where('slug = ?', $slug);
	}

}